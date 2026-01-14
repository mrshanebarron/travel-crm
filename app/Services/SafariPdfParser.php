<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SafariPdfParser
{
    protected Parser $parser;
    protected string $text;
    protected array $lines;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Parse a Safari Office PDF and extract itinerary data.
     *
     * @param string $filePath Full path to the PDF file
     * @return array Parsed safari days data
     */
    public function parse(string $filePath): array
    {
        $pdf = $this->parser->parseFile($filePath);
        $this->text = $pdf->getText();
        $this->lines = array_filter(array_map('trim', explode("\n", $this->text)));

        return $this->extractItinerary();
    }

    /**
     * Extract itinerary from parsed text.
     * Safari Office PDFs have a summary section with format:
     * "LocationDay X-Y\nLodge NameAccommodation:\tRoom Info\nMeal Info Meal Plan:"
     */
    protected function extractItinerary(): array
    {
        $days = [];

        // First, try to find the summary section which has the cleaner format
        // Pattern: LocationDay X or LocationDay X-Y
        // Examples: "NairobiDay 1", "Masai Mara National ReserveDay 2-3"

        // Find all day entries in summary format
        preg_match_all('/([A-Za-z\s]+(?:National\s+(?:Reserve|Park)|Island|Airport)?)\s*Day\s*(\d+)(?:-(\d+))?\s*\n([^\n]+?)(?:Accommodation:|$)/s', $this->text, $matches, PREG_SET_ORDER);

        if (!empty($matches)) {
            foreach ($matches as $match) {
                $location = trim($match[1]);
                $startDay = (int)$match[2];
                $endDay = isset($match[3]) && $match[3] ? (int)$match[3] : $startDay;
                $lodge = trim($match[4]);

                // Clean up lodge name - remove trailing location data
                $lodge = preg_replace('/\s*(Accommodation|Meal Plan|Room|Double|Single|Triple).*$/i', '', $lodge);

                // Find meal plan for this day section
                $mealPlan = $this->findMealPlan($location, $startDay);

                // Create entries for each day in the range
                for ($dayNum = $startDay; $dayNum <= $endDay; $dayNum++) {
                    $days[$dayNum] = [
                        'day_number' => $dayNum,
                        'date' => null,
                        'location' => $location,
                        'lodge' => $lodge ?: null,
                        'morning_activity' => null,
                        'midday_activity' => null,
                        'afternoon_activity' => null,
                        'other_activities' => null,
                        'meal_plan' => $mealPlan,
                        'drink_plan' => null,
                    ];
                }
            }
        }

        // If summary parsing didn't work well, try detailed day-by-day parsing
        if (empty($days)) {
            $days = $this->extractDetailedItinerary();
        }

        // Sort by day number and return as array
        ksort($days);
        return array_values($days);
    }

    /**
     * Find meal plan text for a given location/day.
     */
    protected function findMealPlan(string $location, int $dayNum): ?string
    {
        // Look for meal plan patterns near this location
        // Common formats: "Breakfast, Lunch & Dinner", "Full Board", "FB", etc.

        $patterns = [
            'Breakfast, Lunch & Dinner' => 'Full Board',
            'Breakfast, Lunch and Dinner' => 'Full Board',
            'Full Board' => 'Full Board',
            'Half Board' => 'Half Board',
            'Breakfast' => 'Breakfast Only',
            'B&B' => 'Bed & Breakfast',
            'All Inclusive' => 'All Inclusive',
        ];

        // Find meal plan near this day's section
        $dayPattern = "Day\s*{$dayNum}";
        if (preg_match("/{$dayPattern}.*?Meal Plan:\s*\n?([^\n]+)/si", $this->text, $match)) {
            $mealText = trim($match[1]);
            foreach ($patterns as $search => $result) {
                if (stripos($mealText, $search) !== false) {
                    // Check for drinks inclusion
                    if (stripos($mealText, 'Drinking water') !== false || stripos($mealText, 'drinks') !== false) {
                        return $result . ' + Drinks';
                    }
                    return $result;
                }
            }
            return $mealText;
        }

        return null;
    }

    /**
     * Extract detailed day-by-day itinerary when summary parsing fails.
     */
    protected function extractDetailedItinerary(): array
    {
        $days = [];
        $currentDay = null;
        $dayNumber = 0;

        foreach ($this->lines as $line) {
            // Try to detect day markers - various patterns:
            // "Day 1:", "Day 1 -", "DAY 1", "Day 1 Nairobi", etc.
            if (preg_match('/^day\s*(\d+)(?:\s*[-:]\s*|\s+)(.+)?$/i', $line, $matches)) {
                // Save previous day if exists
                if ($currentDay !== null) {
                    $days[$dayNumber] = $currentDay;
                }

                $dayNumber = (int)$matches[1];
                $location = isset($matches[2]) ? trim($matches[2]) : null;

                $currentDay = [
                    'day_number' => $dayNumber,
                    'date' => null,
                    'location' => $location,
                    'lodge' => null,
                    'morning_activity' => null,
                    'midday_activity' => null,
                    'afternoon_activity' => null,
                    'other_activities' => null,
                    'meal_plan' => null,
                    'drink_plan' => null,
                ];
                continue;
            }

            // If we're in a day context, look for details
            if ($currentDay !== null) {
                $lineLower = strtolower($line);

                // Accommodation/Lodge patterns
                if ($this->containsAny($lineLower, ['accommodation', 'lodge', 'camp', 'hotel', 'stay at', 'overnight'])) {
                    $lodge = $this->extractValue($line);
                    if ($lodge && !$currentDay['lodge']) {
                        $currentDay['lodge'] = $lodge;
                    }
                }

                // Morning activity
                elseif ($this->containsAny($lineLower, ['morning game', 'morning:', 'sunrise', 'early game drive'])) {
                    $existing = $currentDay['morning_activity'];
                    $value = $this->extractValue($line);
                    $currentDay['morning_activity'] = $existing ? "$existing; $value" : $value;
                }

                // Afternoon activity
                elseif ($this->containsAny($lineLower, ['afternoon game', 'afternoon:', 'evening', 'sunset', 'late game drive'])) {
                    $existing = $currentDay['afternoon_activity'];
                    $value = $this->extractValue($line);
                    $currentDay['afternoon_activity'] = $existing ? "$existing; $value" : $value;
                }

                // Midday/lunch activity
                elseif ($this->containsAny($lineLower, ['midday', 'lunch', 'noon'])) {
                    $existing = $currentDay['midday_activity'];
                    $value = $this->extractValue($line);
                    $currentDay['midday_activity'] = $existing ? "$existing; $value" : $value;
                }

                // Meal plan
                elseif ($this->containsAny($lineLower, ['meal plan', 'full board', 'half board', 'all inclusive'])) {
                    $currentDay['meal_plan'] = $this->extractValue($line);
                }

                // Location/area (if not already set)
                elseif ($currentDay['location'] === null && $this->containsAny($lineLower, [
                    'masai mara', 'serengeti', 'amboseli', 'ngorongoro', 'tarangire', 'samburu',
                    'lake nakuru', 'lake naivasha', 'tsavo', 'kruger', 'chobe', 'okavango',
                    'nairobi', 'arusha', 'dar es salaam', 'zanzibar', 'mombasa', 'entebbe', 'kampala'
                ])) {
                    $currentDay['location'] = $line;
                }

                // Activities (game drive, safari, transfer, etc.)
                elseif ($this->containsAny($lineLower, ['game drive', 'safari', 'transfer', 'flight to', 'drive to', 'check-in', 'check in'])) {
                    $existing = $currentDay['other_activities'];
                    $value = $this->extractValue($line);
                    $currentDay['other_activities'] = $existing ? "$existing; $value" : $value;
                }
            }
        }

        // Don't forget the last day
        if ($currentDay !== null) {
            $days[$dayNumber] = $currentDay;
        }

        return $days;
    }

    /**
     * Extract a cleaned value from a line.
     */
    protected function extractValue(string $line): string
    {
        // Remove common prefixes
        $cleaned = preg_replace('/^(morning|afternoon|evening|midday|accommodation|lodge|meals?|drinks?|meal plan)[\s:-]*/i', '', $line);
        // Clean up extra whitespace
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        return trim($cleaned);
    }

    /**
     * Check if string contains any of the given substrings.
     */
    protected function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the raw extracted text (for debugging).
     */
    public function getRawText(): string
    {
        return $this->text ?? '';
    }
}
