<?php

namespace App\Console\Commands;

use App\Models\Mask;
use App\Models\Pharmacy;
use Illuminate\Console\Command;

class ImportPharmacyData extends Command
{
    protected $signature = 'import:pharmacies {file? : JSON file path}';
    protected $description = 'Import pharmacy data from JSON file';

    public function handle()
    {
        $filePath = $this->argument('file') ?? storage_path('app/data/pharmacies.json');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info('Starting pharmacy data import...');

        $pharmaciesData = json_decode(file_get_contents($filePath), true);

        $this->withProgressBar($pharmaciesData, function ($pharmacyData) {
            $pharmacy = Pharmacy::create([
                'name' => $pharmacyData['name'],
                'cash_balance' => $pharmacyData['cashBalance']
            ]);

            $openingHours = $this->parseOpeningHours($pharmacyData['openingHours']);
            foreach ($openingHours as $hours) {
                $pharmacy->openingHours()->create([
                    'day_of_week' => $hours['day_of_week'],
                    'open_time' => $hours['open_time'],
                    'close_time' => $hours['close_time']
                ]);
            }

            foreach ($pharmacyData['masks'] as $maskData) {
                $parsedMask = $this->parseMaskName($maskData['name']);

                if ($parsedMask) {
                    $mask = Mask::firstOrCreate([
                        'brand' => $parsedMask['brand'],
                        'color' => $parsedMask['color'],
                        'quantity_per_pack' => $parsedMask['quantity_per_pack']
                    ]);

                    $pharmacy->masks()->attach($mask->id, [
                        'price' => $maskData['price']
                    ]);
                }
            }
        });

        $this->newLine();
        $this->info('Pharmacy data import completed!');

        return 0;
    }

    private function parseOpeningHours($openingHoursString)
    {
        $result = [];
        $sections = explode('/', $openingHoursString);

        foreach ($sections as $section) {
            $section = trim($section);

            preg_match('/^(.*?)(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/', $section, $matches);

            if (count($matches) >= 4) {
                $days = trim($matches[1]);
                $openTime = $matches[2];
                $closeTime = $matches[3];

                if (strpos($days, '-') !== false) {
                    list($startDay, $endDay) = explode('-', $days);
                    $startDay = trim($startDay);
                    $endDay = trim($endDay);

                    $daysOfWeek = $this->getDaysInRange($startDay, $endDay);
                } else {
                    $daysOfWeek = array_map('trim', explode(',', $days));
                }

                foreach ($daysOfWeek as $day) {
                    $result[] = [
                        'day_of_week' => $this->standardizeDayName($day),
                        'open_time' => $openTime,
                        'close_time' => $closeTime
                    ];
                }
            }
        }

        return $result;
    }

    private function getDaysInRange($startDay, $endDay)
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat', 'Sun'];
        $startIndex = array_search($startDay, $days);
        $endIndex = array_search($endDay, $days);

        if ($startIndex === false || $endIndex === false) {
            return [];
        }

        return array_slice($days, $startIndex, $endIndex - $startIndex + 1);
    }

    private function standardizeDayName($day)
    {
        $map = [
            'Mon' => 'Monday',
            'Tue' => 'Tuesday',
            'Wed' => 'Wednesday',
            'Thur' => 'Thursday',
            'Fri' => 'Friday',
            'Sat' => 'Saturday',
            'Sun' => 'Sunday'
        ];

        return $map[$day] ?? $day;
    }

    private function parseMaskName($maskName)
    {
        preg_match('/^(.*?)\s+\((.*?)\)\s+\((\d+)\s+per\s+pack\)$/', $maskName, $matches);

        if (count($matches) >= 4) {
            return [
                'brand' => trim($matches[1]),
                'color' => trim($matches[2]),
                'quantity_per_pack' => (int) $matches[3]
            ];
        }

        return null;
    }
}