<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Mask;
use App\Models\Pharmacy;
use App\Models\PurchaseHistory;
use App\Models\PharmacyMask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class PurchaseController extends Controller
{
    protected $validateRules = [
        'customer_id' => 'required|exists:customers,id',
        'pharmacy_id' => 'required|exists:pharmacies,id',
        'mask_id' => 'required|exists:masks,id',
        'quantity' => 'required|integer|min:1',
    ];

    protected $validateMessages = [
        'customer_id.required' => '顧客 ID 欄位必填',
        'customer_id.exists' => '無此顧客 ID',
        'pharmacy_id.required' => '藥局 ID 欄位必填',
        'pharmacy_id.exists' => '無此藥局 ID',
        'mask_id.required' => '口罩 ID 欄位必填',
        'mask_id.exists' => '無此口罩 ID',
        'quantity.required' => '數量欄位必填',
        'quantity.integer' => '數量必須是整數',
        'quantity.min' => '數量不能小於 1',
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
    // 7. 處理使用者購買口罩的交易
    public function buyMask(Request $request)
    {
        $validation = $this->validateRequest($request);
        if ($validation !== true) {
            return $validation;
        }

        $customer = Customer::findOrFail($request->customer_id);
        $pharmacy = Pharmacy::findOrFail($request->pharmacy_id);
        $mask = Mask::findOrFail($request->mask_id);

        $pharmacyMask = PharmacyMask::where('pharmacy_id', $pharmacy->id)
            ->where('mask_id', $mask->id)
            ->first();

        if (!$pharmacyMask) {
            return response()->json([
                'message' => '無效的參數請求',
                'errors' => [
                    'pharmacy_mask' => ['此藥局未提供該口罩']
                ]
            ], 400);
        }

        $price = $pharmacyMask->price;
        $transactionAmount = $price * $request->quantity;

        if ($customer->cash_balance < $transactionAmount) {
            return response()->json([
                'message' => '無效的參數請求',
                'errors' => [
                    'cash_balance' => ['資金不足']
                ]
            ], 400);
        }

        DB::beginTransaction();

        try {
            $previousBalance = $customer->cash_balance;
            $customer->cash_balance -= $transactionAmount;
            $customer->save();

            $pharmacy->cash_balance += $transactionAmount;
            $pharmacy->save();

            $purchaseHistory = PurchaseHistory::create([
                'customer_id' => $customer->id,
                'pharmacy_id' => $pharmacy->id,
                'mask_id' => $mask->id,
                'transaction_amount' => $transactionAmount,
                'transaction_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => '購買成功',
                'transaction' => [
                    'transaction_id' => $purchaseHistory->id,
                    'transaction_date' => $purchaseHistory->transaction_date->format('Y-m-d H:i:s'),
                    'transaction_amount' => $transactionAmount,
                    'status' => 'success',
                    'mask_id' => $mask->id,
                    'pharmacy_id' => $pharmacy->id,
                    'price' => $price,
                    'quantity' => $request->quantity
                ],
                'customer' => [
                    'customer_id' => $customer->id,
                    'previous_cash_balance' => $previousBalance,
                    'new_cash_balance' => $customer->cash_balance
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => '購買失敗: ' . $e->getMessage()
            ], 500);
        }
    }
}