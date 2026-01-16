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
    protected array $bookingMetadata = [];

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Extract booking metadata from the PDF.
     * Returns: lead_name, traveler_count, adult_count, child_count, start_date, end_date, country, adult_rate
     */
    public function extractBookingMetadata(): array
    {
        if (empty($this->bookingMetadata)) {
            $this->parseBookingMetadata();
        }
        return $this->bookingMetadata;
    }

    /**
     * Parse booking metadata from the PDF text.
     */
    protected function parseBookingMetadata(): void
    {
        $this->bookingMetadata = [
            'lead_name' => null,
            'lead_first_name' => null,
            'lead_last_name' => null,
            'traveler_count' => 0,
            'adult_count' => 0,
            'child_count' => 0,
            'start_date' => null,
            'end_date' => null,
            'country' => null,
            'adult_rate' => null,
            'child_rate' => null,
            'reference_number' => null,
        ];

        // Extract lead name - "Proposal for Alex Rodgers" or "Dear Alex Rodgers,"
        if (preg_match('/Proposal for ([A-Za-z\s]+?)(?:\s+\d+\s+Adult|\s*$)/i', $this->text, $match)) {
            $this->bookingMetadata['lead_name'] = trim($match[1]);
        } elseif (preg_match('/Dear ([A-Za-z\s]+?),/i', $this->text, $match)) {
            $this->bookingMetadata['lead_name'] = trim($match[1]);
        }

        // Split lead name into first/last
        if ($this->bookingMetadata['lead_name']) {
            $parts = explode(' ', $this->bookingMetadata['lead_name']);
            if (count($parts) >= 2) {
                $this->bookingMetadata['lead_first_name'] = $parts[0];
                $this->bookingMetadata['lead_last_name'] = implode(' ', array_slice($parts, 1));
            } else {
                $this->bookingMetadata['lead_first_name'] = $this->bookingMetadata['lead_name'];
                $this->bookingMetadata['lead_last_name'] = '';
            }
        }

        // Extract traveler counts - "4 Adults" or "2 Adults, 2 Children"
        if (preg_match('/(\d+)\s*Adult/i', $this->text, $match)) {
            $this->bookingMetadata['adult_count'] = (int) $match[1];
        }
        if (preg_match('/(\d+)\s*Child/i', $this->text, $match)) {
            $this->bookingMetadata['child_count'] = (int) $match[1];
        }
        $this->bookingMetadata['traveler_count'] = $this->bookingMetadata['adult_count'] + $this->bookingMetadata['child_count'];

        // Extract dates - "Start Tour\nJanuary 4, 2027" or "Monday, January 4, 2027"
        // The PDF has newlines between "Start Tour" and the date
        if (preg_match('/Start\s*Tour\s*\n\s*([A-Za-z]+\s+\d{1,2},?\s+\d{4})/i', $this->text, $match)) {
            try {
                $this->bookingMetadata['start_date'] = Carbon::parse(trim($match[1]))->format('Y-m-d');
            } catch (\Exception $e) {}
        } elseif (preg_match('/Start\s*\n\s*([A-Za-z]+,?\s+[A-Za-z]+\s+\d{1,2},?\s+\d{4})/i', $this->text, $match)) {
            try {
                $this->bookingMetadata['start_date'] = Carbon::parse(trim($match[1]))->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        if (preg_match('/End\s*Tour\s*\n\s*([A-Za-z]+\s+\d{1,2},?\s+\d{4})/i', $this->text, $match)) {
            try {
                $this->bookingMetadata['end_date'] = Carbon::parse(trim($match[1]))->format('Y-m-d');
            } catch (\Exception $e) {}
        } elseif (preg_match('/End\s*\n\s*([A-Za-z]+,?\s+[A-Za-z]+\s+\d{1,2},?\s+\d{4})/i', $this->text, $match)) {
            try {
                $this->bookingMetadata['end_date'] = Carbon::parse(trim($match[1]))->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        // Extract country - look for Kenya, Tanzania, Uganda, etc.
        $countries = [];
        if (preg_match('/Kenya/i', $this->text)) $countries[] = 'Kenya';
        if (preg_match('/Tanzania/i', $this->text)) $countries[] = 'Tanzania';
        if (preg_match('/Uganda/i', $this->text)) $countries[] = 'Uganda';
        if (preg_match('/Rwanda/i', $this->text)) $countries[] = 'Rwanda';
        if (preg_match('/Botswana/i', $this->text)) $countries[] = 'Botswana';
        if (preg_match('/South Africa/i', $this->text)) $countries[] = 'South Africa';
        if (preg_match('/Namibia/i', $this->text)) $countries[] = 'Namibia';
        if (preg_match('/Zimbabwe/i', $this->text)) $countries[] = 'Zimbabwe';
        if (preg_match('/Zambia/i', $this->text)) $countries[] = 'Zambia';

        if (!empty($countries)) {
            $this->bookingMetadata['country'] = implode(' & ', array_unique($countries));
        }

        // Extract adult rate - "4x Adult 4x $6,149.00" or "Adult: $5,990"
        if (preg_match('/\d+x\s*Adult\s+\d+x\s*\$([0-9,]+(?:\.\d{2})?)/i', $this->text, $match)) {
            $this->bookingMetadata['adult_rate'] = (float) str_replace(',', '', $match[1]);
        } elseif (preg_match('/Adult[s]?\s*[:\-]?\s*\$([0-9,]+(?:\.\d{2})?)/i', $this->text, $match)) {
            $this->bookingMetadata['adult_rate'] = (float) str_replace(',', '', $match[1]);
        }

        // Extract child rate if present
        if (preg_match('/\d+x\s*Child\s+\d+x\s*\$([0-9,]+(?:\.\d{2})?)/i', $this->text, $match)) {
            $this->bookingMetadata['child_rate'] = (float) str_replace(',', '', $match[1]);
        } elseif (preg_match('/Child(?:ren)?\s*[:\-]?\s*\$([0-9,]+(?:\.\d{2})?)/i', $this->text, $match)) {
            $this->bookingMetadata['child_rate'] = (float) str_replace(',', '', $match[1]);
        }

        // Extract reference number - "Ref. Number: #2025-1333.4"
        if (preg_match('/Ref\.?\s*(?:Number|#)?[:\s]*#?(\d{4}-\d+(?:\.\d+)?)/i', $this->text, $match)) {
            $this->bookingMetadata['reference_number'] = $match[1];
        }
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

    /**
     * Extract rates by age category from the PDF.
     * Returns array with keys: adult, child_12_17, child_2_11, infant
     *
     * Safari Office PDFs typically have rates in formats like:
     * - "Adult: $5,990" or "Adults: USD 5990"
     * - "Child 12-17: $4,990" or "Children (12-17 years): $4990"
     * - "Child 2-11: $3,990" or "Children (2-11 years): $3990"
     * - May also show as table format or per-person pricing section
     */
    public function extractRates(): array
    {
        $rates = [
            'adult' => null,
            'child_12_17' => null,
            'child_2_11' => null,
            'infant' => null,
        ];

        // Pattern to match currency amounts: $5,990 or USD 5990 or 5,990 USD etc.
        $currencyPattern = '[\$]?\s*([0-9,]+(?:\.[0-9]{2})?)\s*(?:USD|pp|per person)?';

        // Adult rate patterns
        $adultPatterns = [
            '/adult[s]?\s*(?:rate)?[:\s]+' . $currencyPattern . '/i',
            '/(?:per\s+)?adult[s]?\s*[:\-]\s*' . $currencyPattern . '/i',
            '/adult\s+sharing\s*[:\-]?\s*' . $currencyPattern . '/i',
            '/' . $currencyPattern . '\s*(?:per\s+)?adult/i',
        ];

        // Child 12-17 patterns
        $child12to17Patterns = [
            '/child(?:ren)?\s*\(?12[\s\-–]+17(?:\s*(?:years?|yrs?))?\)?\s*[:\-]?\s*' . $currencyPattern . '/i',
            '/(?:teen(?:ager)?s?|youth)\s*\(?12[\s\-–]+17\)?\s*[:\-]?\s*' . $currencyPattern . '/i',
            '/' . $currencyPattern . '\s*(?:per\s+)?child(?:ren)?\s*\(?12[\s\-–]+17/i',
        ];

        // Child 2-11 patterns
        $child2to11Patterns = [
            '/child(?:ren)?\s*\(?2[\s\-–]+11(?:\s*(?:years?|yrs?))?\)?\s*[:\-]?\s*' . $currencyPattern . '/i',
            '/(?:kids?|children)\s*\(?2[\s\-–]+11\)?\s*[:\-]?\s*' . $currencyPattern . '/i',
            '/' . $currencyPattern . '\s*(?:per\s+)?child(?:ren)?\s*\(?2[\s\-–]+11/i',
        ];

        // Infant patterns (usually free or reduced)
        $infantPatterns = [
            '/infant[s]?\s*\(?(?:0[\s\-–]+1|under\s*2)(?:\s*(?:years?|yrs?))?\)?\s*[:\-]?\s*' . $currencyPattern . '/i',
            '/infant[s]?\s*[:\-]?\s*(?:free|complimentary|no\s+charge)/i',
            '/' . $currencyPattern . '\s*(?:per\s+)?infant/i',
        ];

        // Search for adult rates
        foreach ($adultPatterns as $pattern) {
            if (preg_match($pattern, $this->text, $match)) {
                $rates['adult'] = $this->parseAmount($match[1]);
                break;
            }
        }

        // Search for child 12-17 rates
        foreach ($child12to17Patterns as $pattern) {
            if (preg_match($pattern, $this->text, $match)) {
                $rates['child_12_17'] = $this->parseAmount($match[1]);
                break;
            }
        }

        // Search for child 2-11 rates
        foreach ($child2to11Patterns as $pattern) {
            if (preg_match($pattern, $this->text, $match)) {
                $rates['child_2_11'] = $this->parseAmount($match[1]);
                break;
            }
        }

        // Search for infant rates
        foreach ($infantPatterns as $pattern) {
            if (preg_match($pattern, $this->text, $match)) {
                if (isset($match[1]) && is_numeric(str_replace(',', '', $match[1]))) {
                    $rates['infant'] = $this->parseAmount($match[1]);
                } else {
                    $rates['infant'] = 0; // Free
                }
                break;
            }
        }

        // If we only found one rate (likely adult), try generic "per person" patterns
        if ($rates['adult'] === null) {
            $genericPatterns = [
                '/(?:rate|price|cost)\s*(?:per\s+person|pp)\s*[:\-]?\s*' . $currencyPattern . '/i',
                '/(?:total|safari)\s*(?:price|rate|cost)\s*[:\-]?\s*' . $currencyPattern . '/i',
                '/' . $currencyPattern . '\s*(?:per\s+person|pp)/i',
            ];

            foreach ($genericPatterns as $pattern) {
                if (preg_match($pattern, $this->text, $match)) {
                    $rates['adult'] = $this->parseAmount($match[1]);
                    break;
                }
            }
        }

        return $rates;
    }

    /**
     * Parse a currency amount string to float.
     */
    protected function parseAmount(string $amount): float
    {
        // Remove currency symbols and thousands separators
        $cleaned = preg_replace('/[^\d.]/', '', str_replace(',', '', $amount));
        return (float) $cleaned;
    }
}
