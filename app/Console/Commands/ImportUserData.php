<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Mask;
use App\Models\Pharmacy;
use App\Models\PurchaseHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportUserData extends Command
{
    protected $signature = 'import:customers {file? : JSON file path}';
    protected $description = 'Import customer data from JSON file';

    public function handle()
    {
        $filePath = $this->argument('file') ?? storage_path('app/data/users.json');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info('Starting customer data import...');

        $usersData = json_decode(file_get_contents($filePath), true);

        $this->withProgressBar($usersData, function ($userData) {
            $customer = Customer::create([
                'name' => $userData['name'],
                'cash_balance' => $userData['cashBalance']
            ]);

            if (isset($userData['purchaseHistories']) && is_array($userData['purchaseHistories'])) {
                foreach ($userData['purchaseHistories'] as $history) {
                    $pharmacy = Pharmacy::where('name', $history['pharmacyName'])->first();

                    if (!$pharmacy) {
                        $this->warn("Pharmacy not found: {$history['pharmacyName']}");
                        continue;
                    }

                    $maskInfo = $this->parseMaskName($history['maskName']);

                    $mask = Mask::where([
                        'brand' => $maskInfo['brand'],
                        'color' => $maskInfo['color'],
                        'quantity_per_pack' => $maskInfo['quantity_per_pack']
                    ])->first();

                    if (!$mask) {
                        $this->warn("Mask not found: {$history['maskName']}");
                        continue;
                    }

                    PurchaseHistory::create([
                        'customer_id' => $customer->id,
                        'pharmacy_id' => $pharmacy->id,
                        'mask_id' => $mask->id,
                        'transaction_amount' => $history['transactionAmount'],
                        'transaction_date' => Carbon::parse($history['transactionDate'])
                    ]);
                }
            }
        });

        $this->newLine();
        $this->info('Customer data import completed!');

        return 0;
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