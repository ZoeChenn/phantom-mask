<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PharmacyController extends Controller
{
    protected $validateRules = [
        'day' => 'required|integer|between:1,7',
        'time' => 'required|date_format:H:i',
        'min_price' => 'sometimes|numeric|min:0',
        'max_price' => 'sometimes|numeric|min:0|gte:min_price',
        'mask_count' => 'sometimes|integer|min:0',
        'comparison' => 'required_with:mask_count|in:more,less'
    ];

    protected $validateMessages = [
        'time.date_format' => '時間格式必須為 HH:MM，並符合正常時間範圍',
        'day.between' => '日期必須是 1 到 7 之間的數字（1 代表星期一，7 代表星期日）',
        'day.required' => '日期欄位必填',
        'day.integer' => '日期必須是整數',
        'time.required' => '時間欄位必填',
        'min_price.numeric' => '最低價格必須是數字',
        'min_price.min' => '最低價格不能小於 0',
        'max_price.numeric' => '最高價格必須是數字',
        'max_price.min' => '最高價格不能小於 0',
        'max_price.gte' => '最高價格必須大於或等於最低價格',
        'mask_count.integer' => '口罩數量必須是整數',
        'mask_count.min' => '口罩數量不能小於 0',
        'comparison.required_with' => '當提供口罩數量時，必須指定比較方式',
        'comparison.in' => '比較方式必須是：超過(more)或少於(less)'
    ];

    protected function validateRequest(Request $request, array $rules = null, array $messages = null)
    {
        $validateRules = $rules ?? $this->validateRules;
        $validateMessages = $messages ?? $this->validateMessages;
        
        $validator = Validator::make($request->all(), $validateRules, $validateMessages);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => '無效的參數請求',
                'errors' => $validator->errors()
            ], 400);
        }
        
        return true;
    }

    // 1. 列出特定時間和星期幾營業的藥局
    public function getPharmacyByOpenHour(Request $request)
    {
        $validation = $this->validateRequest($request);
        
        if ($validation !== true) {
            return $validation;
        }

        $query = Pharmacy::query();

        $day = $request->day;
        $time = $request->time;

        $dayNames = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        $dayName = $dayNames[$day] ?? null;

        if ($dayName) {
            $query->whereHas('openingHours', function ($q) use ($dayName, $time) {
                $q->where('day_of_week', $dayName)
                    ->where('open_time', '<=', $time)
                    ->where('close_time', '>=', $time);
            });
        } else {
            return response()->json([
                'message' => '無效的日期參數',
                'errors' => ['day' => ['指定的日期參數無效']]
            ], 400);
        }

        $query->with(['openingHours' => function($query) use ($dayName) {
            $query->where('day_of_week', $dayName);
        }]);

        $pharmacies = $query->get();

        $formattedPharmacies = $pharmacies->map(function ($pharmacy) {
            $openingHour = $pharmacy->openingHours->first();
            
            return [
                'id' => $pharmacy->id,
                'name' => $pharmacy->name,
                'day_of_week' => $openingHour ? $openingHour->day_of_week : null,
                'open_time' => $openingHour ? $openingHour->open_time : null,
                'close_time' => $openingHour ? $openingHour->close_time : null,
            ];
        });

        return response()->json($formattedPharmacies);
    }

    // 2. 列出特定藥局販售的所有口罩
    public function getPharmacyMasks(Request $request, $id)
    {
        $validation = $this->validateRequest($request);
        
        if ($validation !== true) {
            return $validation;
        }

        try {
            $pharmacy = Pharmacy::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => '找不到指定的藥局',
                'requested_id' => $request->route('id'),
                'status' => 'error'
            ], 404);
        }
        
        $masksQuery = $pharmacy->masks();
        $masksQuery->orderBy('price');
        
        $masks = $masksQuery->get()->map(function ($mask) {
            return [
                'id' => $mask->id,
                'brand' => $mask->brand,
                'color' => $mask->color,
                'quantity_per_pack' => $mask->quantity_per_pack,
                'price' => $mask->pivot->price
            ];
        });
        
        return response()->json([
            'pharmacy_id' => $id,
            'pharmacy_name' => $pharmacy->name,
            'masks' => $masks
        ]);
    }

    // 3. 列出特定價格範圍內有超過或少於 x 種口罩產品的藥局
    public function getPharmaciesByMaskFilter(Request $request)
    {
        $validation = $this->validateRequest($request);
        
        if ($validation !== true) {
            return $validation;
        }

        $maskCount = $request->mask_count;
        $comparison = $request->comparison;
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price;
        
        $query = Pharmacy::query();
        
        if ($minPrice && $maxPrice) {
            $query->whereHas('masks', function ($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('price', [$minPrice, $maxPrice]);
            });
        }
        
        $pharmacies = $query->get()->map(function ($pharmacy) use ($minPrice, $maxPrice) {
            $masks = $pharmacy->masks()
                ->when($minPrice && $maxPrice, function ($q) use ($minPrice, $maxPrice) {
                    return $q->whereBetween('price', [$minPrice, $maxPrice]);
                })
                ->get()
                ->map(function ($mask) {
                    return [
                        'id' => $mask->id,
                        'brand' => $mask->brand,
                        'color' => $mask->color,
                        'quantity_per_pack' => $mask->quantity_per_pack,
                        'price' => $mask->pivot->price
                    ];
                });
            
            $countOfMasks = $masks->count();
            
            return [
                'id' => $pharmacy->id,
                'name' => $pharmacy->name,
                'mask_count' => $countOfMasks,
                'masks' => $masks
            ];
        });
        
        if ($maskCount && $comparison) {
            if ($comparison == 'more') {
                $pharmacies = $pharmacies->where('mask_count', '>', $maskCount);
            } elseif ($comparison == 'less') {
                $pharmacies = $pharmacies->where('mask_count', '<', $maskCount);
            }
        }
        
        return response()->json($pharmacies->values());
    }
}