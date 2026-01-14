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
     * Safari Office PDFs typically have day-by-day breakdowns.
     */
    protected function extractItinerary(): array
    {
        $days = [];
        $currentDay = null;
        $dayNumber = 0;

        foreach ($this->lines as $line) {
            // Try to detect day markers - common patterns:
            // "Day 1:", "Day 1 -", "DAY 1", "Day One:", etc.
            if (preg_match('/^day\s*(\d+|one|two|three|four|five|six|seven|eight|nine|ten)\s*[-:]/i', $line, $matches)) {
                // Save previous day if exists
                if ($currentDay !== null) {
                    $days[] = $currentDay;
                }

                $dayNumber = $this->parseNumber($matches[1]);
                $currentDay = [
                    'day_number' => $dayNumber,
                    'date' => null,
                    'location' => $this->extractAfterMarker($line, [':', '-']),
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
                    $currentDay['lodge'] = $this->extractValue($line);
                }

                // Morning activity
                elseif ($this->containsAny($lineLower, ['morning', 'am:', 'sunrise', 'early game drive', 'breakfast'])) {
                    $existing = $currentDay['morning_activity'];
                    $value = $this->extractValue($line);
                    $currentDay['morning_activity'] = $existing ? "$existing; $value" : $value;
                }

                // Afternoon activity
                elseif ($this->containsAny($lineLower, ['afternoon', 'pm:', 'evening', 'sunset', 'late game drive'])) {
                    $existing = $currentDay['afternoon_activity'];
                    $value = $this->extractValue($line);
                    $currentDay['afternoon_activity'] = $existing ? "$existing; $value" : $value;
                }

                // Midday/lunch activity
                elseif ($this->containsAny($lineLower, ['midday', 'lunch', 'noon', 'mid-day'])) {
                    $existing = $currentDay['midday_activity'];
                    $value = $this->extractValue($line);
                    $currentDay['midday_activity'] = $existing ? "$existing; $value" : $value;
                }

                // Meal plan
                elseif ($this->containsAny($lineLower, ['meals', 'meal plan', 'full board', 'half board', 'b&b', 'all inclusive', 'fb', 'hb', 'bb'])) {
                    $currentDay['meal_plan'] = $this->extractValue($line);
                }

                // Drinks
                elseif ($this->containsAny($lineLower, ['drinks', 'beverages', 'drink plan', 'inclusive drinks'])) {
                    $currentDay['drink_plan'] = $this->extractValue($line);
                }

                // Location/area (if not already set and line mentions a known safari region)
                elseif ($currentDay['location'] === null && $this->containsAny($lineLower, [
                    'masai mara', 'serengeti', 'amboseli', 'ngorongoro', 'tarangire', 'samburu',
                    'lake nakuru', 'lake naivasha', 'tsavo', 'kruger', 'chobe', 'okavango',
                    'nairobi', 'arusha', 'dar es salaam', 'zanzibar', 'mombasa', 'entebbe', 'kampala'
                ])) {
                    $currentDay['location'] = $this->extractValue($line);
                }

                // Activities (game drive, safari, etc.)
                elseif ($this->containsAny($lineLower, ['game drive', 'safari', 'game viewing', 'transfer', 'flight', 'drive to'])) {
                    // Append to other_activities if not caught by time-of-day
                    $existing = $currentDay['other_activities'];
                    $value = $this->extractValue($line);
                    $currentDay['other_activities'] = $existing ? "$existing; $value" : $value;
                }
            }
        }

        // Don't forget the last day
        if ($currentDay !== null) {
            $days[] = $currentDay;
        }

        return $days;
    }

    /**
     * Convert word numbers to integers.
     */
    protected function parseNumber(string $value): int
    {
        $words = [
            'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
            'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10,
        ];

        $lower = strtolower($value);
        return $words[$lower] ?? (int) $value;
    }

    /**
     * Extract text after common markers like ":" or "-".
     */
    protected function extractAfterMarker(string $line, array $markers = [':', '-']): ?string
    {
        foreach ($markers as $marker) {
            $pos = strpos($line, $marker);
            if ($pos !== false) {
                $value = trim(substr($line, $pos + 1));
                return $value ?: null;
            }
        }
        return null;
    }

    /**
     * Extract a cleaned value from a line.
     */
    protected function extractValue(string $line): string
    {
        // Remove common prefixes
        $cleaned = preg_replace('/^(morning|afternoon|evening|midday|accommodation|lodge|meals?|drinks?)[\s:-]*/i', '', $line);
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
