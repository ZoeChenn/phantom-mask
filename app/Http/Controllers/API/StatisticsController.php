<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PurchaseHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class StatisticsController extends Controller
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

    // 5. 統計特定日期區間內的口罩銷售數量和總金額
    public function getMaskSales(Request $request)
    {
        $validation = $this->validateRequest($request);

        if ($validation !== true) {
            return $validation;
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $statistics = DB::table('purchase_histories')
            ->join('masks', 'purchase_histories.mask_id', '=', 'masks.id')
            ->whereBetween('purchase_histories.transaction_date', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(masks.quantity_per_pack) as total_masks'),
                DB::raw('SUM(purchase_histories.transaction_amount) as total_value')
            )
            ->first();

        return response()->json([
            'total_masks' => (int) $statistics->total_masks,
            'total_value' => (float) $statistics->total_value
        ]);
    }
}