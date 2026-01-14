<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class SafariOfficeWebScraper
{
    protected string $html;
    protected DOMDocument $dom;
    protected DOMXPath $xpath;

    /**
     * Parse a Safari Office online booking URL and extract itinerary data.
     *
     * @param string $url The Safari Office online booking URL
     * @return array Parsed safari days data
     * @throws \Exception If URL cannot be fetched or parsed
     */
    public function parse(string $url): array
    {
        // Validate URL is a Safari Office URL
        if (!$this->isValidSafariOfficeUrl($url)) {
            throw new \Exception('Invalid Safari Office URL. Expected format: https://*.safarioffice.app/*/online');
        }

        // Fetch the page
        $response = Http::timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch Safari Office page: ' . $response->status());
        }

        $this->html = $response->body();

        // Parse HTML
        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($this->html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $this->xpath = new DOMXPath($this->dom);

        return $this->extractItinerary();
    }

    /**
     * Validate the Safari Office URL format.
     */
    protected function isValidSafariOfficeUrl(string $url): bool
    {
        return (bool) preg_match('/^https:\/\/[a-z0-9-]+\.safarioffice\.app\/[a-z0-9-]+\/online$/i', $url);
    }

    /**
     * Extract itinerary data from parsed HTML.
     */
    protected function extractItinerary(): array
    {
        $days = [];

        // Extract day navigation links to understand structure
        // Format: Day 1, Day 2 & 3, Day 4 to 6, etc.
        $dayLinks = $this->xpath->query("//a[@class='anchor-link']");
        $daySections = [];

        foreach ($dayLinks as $link) {
            $href = $link->getAttribute('href');
            $text = trim($link->textContent);

            // Match Day patterns
            if (preg_match('/^Day\s+(.+)$/i', $text, $matches)) {
                $daySections[$href] = $this->parseDayRange($matches[1]);
            }
        }

        // Now extract data from each day section
        foreach ($daySections as $sectionId => $dayNumbers) {
            $sectionId = ltrim($sectionId, '#');
            $sectionData = $this->extractSectionData($sectionId, $dayNumbers);

            foreach ($sectionData as $day) {
                $days[] = $day;
            }
        }

        return $days;
    }

    /**
     * Parse day range text into array of day numbers.
     * "1" => [1]
     * "2 & 3" => [2, 3]
     * "4 to 6" => [4, 5, 6]
     * "7 to 10" => [7, 8, 9, 10]
     */
    protected function parseDayRange(string $text): array
    {
        $text = trim($text);
        $days = [];

        // Pattern: "X to Y" or "X - Y"
        if (preg_match('/^(\d+)\s*(?:to|-)\s*(\d+)$/i', $text, $matches)) {
            for ($i = (int)$matches[1]; $i <= (int)$matches[2]; $i++) {
                $days[] = $i;
            }
            return $days;
        }

        // Pattern: "X & Y"
        if (preg_match('/^(\d+)\s*&\s*(\d+)$/i', $text, $matches)) {
            return [(int)$matches[1], (int)$matches[2]];
        }

        // Single day
        if (preg_match('/^(\d+)$/', $text, $matches)) {
            return [(int)$matches[1]];
        }

        return [];
    }

    /**
     * Extract data from a specific section.
     */
    protected function extractSectionData(string $sectionId, array $dayNumbers): array
    {
        $days = [];

        // Find the section
        $section = $this->xpath->query("//div[@id='{$sectionId}']")->item(0);
        if (!$section) {
            return $days;
        }

        // Get the next container div which has the details
        $container = $this->xpath->query("following-sibling::div[@class='container']", $section)->item(0);

        // Extract location from the h1 in the section
        $locationNode = $this->xpath->query(".//h1", $section)->item(0);
        $location = $locationNode ? trim($locationNode->textContent) : null;

        // Extract date from time element
        $dateNode = $this->xpath->query(".//time", $section)->item(0);
        $dateText = $dateNode ? trim($dateNode->textContent) : null;
        $startDate = $this->parseDate($dateText);

        // Extract accommodation name from data-caption attributes
        $lodge = $this->extractAccommodation($sectionId);

        // Extract meal plan
        $mealPlan = $this->extractMealPlan($sectionId);

        // Extract activities from wysiwyg content
        $activities = $this->extractActivities($sectionId);

        // Create a day entry for each day in the range
        foreach ($dayNumbers as $index => $dayNumber) {
            $dayDate = $startDate ? $startDate->copy()->addDays($index) : null;

            $days[] = [
                'day_number' => $dayNumber,
                'date' => $dayDate?->format('Y-m-d'),
                'location' => $location,
                'lodge' => $lodge,
                'morning_activity' => $activities['morning'] ?? null,
                'midday_activity' => $activities['midday'] ?? null,
                'afternoon_activity' => $activities['afternoon'] ?? null,
                'other_activities' => $activities['other'] ?? null,
                'meal_plan' => $mealPlan,
                'drink_plan' => null,
            ];
        }

        return $days;
    }

    /**
     * Extract accommodation name from the page.
     */
    protected function extractAccommodation(string $sectionId): ?string
    {
        // Look for accommodation spans near this section
        // The structure has spans with accommodation names
        $accommodations = $this->xpath->query("//span[contains(@class, 'acco-type')]/preceding-sibling::span");

        // Try to find accommodation from data-caption attributes
        $captions = $this->xpath->query("//*[@data-caption]");

        foreach ($captions as $caption) {
            $captionText = $caption->getAttribute('data-caption');
            // Look for accommodation in data-caption
            // Format: <h1><span>Day X <strong>Location</strong></span></h1>  <div>Lodge Name</div>
            if (preg_match('/<div>([^<]+)<\/div>/', $captionText, $matches)) {
                $potentialLodge = trim($matches[1]);
                // Filter out generic descriptions
                if (!preg_match('/^(View|A |The |bathroom|room)/i', $potentialLodge)) {
                    return $potentialLodge;
                }
            }
        }

        // Try to find from figcaption with acco-type
        $accoNodes = $this->xpath->query("//span[@class='acco-type']/ancestor::figcaption");
        foreach ($accoNodes as $node) {
            $text = trim($node->textContent);
            $text = preg_replace('/\s*(Hotel|Lodge|Camp|Tented camp)\s*$/i', '', $text);
            if ($text) {
                return $text;
            }
        }

        return null;
    }

    /**
     * Extract meal plan information.
     */
    protected function extractMealPlan(string $sectionId): ?string
    {
        // Look for meal plan text
        $mealNodes = $this->xpath->query("//strong[@class='text' and contains(text(), 'Meal Plan')]");

        foreach ($mealNodes as $node) {
            // Get the sibling or child with actual meal plan
            $parent = $node->parentNode;
            if ($parent) {
                // Look for text content after "Meal Plan"
                $fullText = trim($parent->textContent);
                if (preg_match('/Meal Plan\s*([A-Z]{2,})/i', $fullText, $matches)) {
                    return strtoupper($matches[1]);
                }
            }
        }

        // Try common patterns
        if (preg_match('/Full Board|FB/i', $this->html)) {
            return 'FB';
        }
        if (preg_match('/Half Board|HB/i', $this->html)) {
            return 'HB';
        }
        if (preg_match('/Bed\s*&\s*Breakfast|BB/i', $this->html)) {
            return 'BB';
        }
        if (preg_match('/All Inclusive|AI/i', $this->html)) {
            return 'AI';
        }

        return null;
    }

    /**
     * Extract activities from the section.
     */
    protected function extractActivities(string $sectionId): array
    {
        $activities = [
            'morning' => null,
            'midday' => null,
            'afternoon' => null,
            'other' => null,
        ];

        // Look for activity descriptions in wysiwyg content
        $wysiwygNodes = $this->xpath->query("//div[@class='wysiwyg-content']//div[@class='wysiwyg-content-inner']");

        $allActivities = [];
        foreach ($wysiwygNodes as $node) {
            $text = trim($node->textContent);
            if ($text && strlen($text) > 5 && strlen($text) < 500) {
                // Filter out booking terms
                if (!preg_match('/BOOKING|PAYMENT|CANCELLATION|deposit|refund/i', $text)) {
                    $allActivities[] = $text;
                }
            }
        }

        if (!empty($allActivities)) {
            foreach ($allActivities as $activity) {
                $activityLower = strtolower($activity);

                if (preg_match('/morning|sunrise|breakfast|early game|am\s+game/i', $activityLower)) {
                    $activities['morning'] = $activities['morning']
                        ? $activities['morning'] . '; ' . $activity
                        : $activity;
                } elseif (preg_match('/afternoon|sunset|evening|pm\s+game|late game/i', $activityLower)) {
                    $activities['afternoon'] = $activities['afternoon']
                        ? $activities['afternoon'] . '; ' . $activity
                        : $activity;
                } elseif (preg_match('/lunch|midday|noon/i', $activityLower)) {
                    $activities['midday'] = $activities['midday']
                        ? $activities['midday'] . '; ' . $activity
                        : $activity;
                } else {
                    $activities['other'] = $activities['other']
                        ? $activities['other'] . '; ' . $activity
                        : $activity;
                }
            }
        }

        return $activities;
    }

    /**
     * Parse date text to Carbon instance.
     */
    protected function parseDate(?string $dateText): ?Carbon
    {
        if (!$dateText) {
            return null;
        }

        // Clean up the date text - remove day name prefix
        // Format: "Monday, January 4, 2027" or "Monday, January 4, 2027"
        $dateText = preg_replace('/^[a-z]+,\s*/i', '', $dateText);

        try {
            return Carbon::parse($dateText);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the raw HTML (for debugging).
     */
    public function getRawHtml(): string
    {
        return $this->html ?? '';
    }
}
