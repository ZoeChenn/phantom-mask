<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PurchaseHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    protected $validateRules = [
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ];

    protected $validateMessages = [
        'start_date.required' => '開始日期欄位必填',
        'end_date.required' => '結束日期欄位必填',
        'end_date.after_or_equal' => '結束日期必須等於或晚於開始日期',
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

    // 4. 統計指定日期區間購買口罩總金額最高的前 X 名使用者
    public function getTopCustomers(Request $request, $count)
    {
        $validation = $this->validateRequest($request);

        if ($validation !== true) {
            return $validation;
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $topCustomers = Customer::select('customers.id', 'customers.name')
            ->selectRaw('SUM(purchase_histories.transaction_amount) as total_amount')
            ->join('purchase_histories', 'customers.id', '=', 'purchase_histories.customer_id')
            ->whereBetween('purchase_histories.transaction_date', [$startDate, $endDate])
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_amount')
            ->limit($count)
            ->get();

        return response()->json($topCustomers);
    }
}