<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\Mask;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    // 6. 以關鍵字搜尋藥局或口罩名稱，並依相關性排序
    public function getPharmacyAndMaskbyName(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $searchTerm = $request->q;

        $pharmacies = Pharmacy::where('name', 'like', "%{$searchTerm}%")
            ->orderByRaw("CASE 
                WHEN name LIKE '{$searchTerm}%' THEN 1 
                WHEN name LIKE '% {$searchTerm}%' THEN 2 
                ELSE 3 
            END")
            ->get()
            ->map(function ($pharmacy) {
                $pharmacy->result_type = 'pharmacy';
                unset($pharmacy->created_at);
                unset($pharmacy->updated_at);
                $pharmacy->cash_balance = (float) $pharmacy->cash_balance;
                return $pharmacy;
            });

        $masks = Mask::where('brand', 'like', "%{$searchTerm}%")
            ->orderByRaw("CASE 
                WHEN brand LIKE '{$searchTerm}%' THEN 1 
                WHEN brand LIKE '% {$searchTerm}%' THEN 2 
                ELSE 3 
            END")
            ->get()
            ->map(function ($mask) {
                $mask->result_type = 'mask';
                unset($mask->created_at);
                unset($mask->updated_at);
                return $mask;
            });

        $results = $this->mergeAndSortResults($pharmacies, $masks, $searchTerm);

        return response()->json($results);
    }

    private function mergeAndSortResults($pharmacies, $masks, $searchTerm)
    {
        $combined = $pharmacies->concat($masks);

        return $combined->sortBy(function ($item) use ($searchTerm) {
            if ($item->result_type === 'pharmacy') {
                if (stripos($item->name, $searchTerm) === 0)
                    return 1;
                if (stripos($item->name, ' ' . $searchTerm) !== false)
                    return 3;
                return 5;
            } else {
                if (stripos($item->brand, $searchTerm) === 0)
                    return 2;
                if (stripos($item->brand, ' ' . $searchTerm) !== false)
                    return 4;
                return 6;
            }
        })->values();
    }
}