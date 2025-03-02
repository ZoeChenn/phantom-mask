<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Statistics",
 *     description="和統計有關的 API 們"
 * )
 * 
 * @OA\Get(
 *     path="/api/statistics",
 *     operationId="getMaskSales",
 *     summary="統計特定日期區間內的口罩銷售數量和總金額",
 *     description="計算並回傳指定日期範圍內售出的口罩總數量和交易總金額。",
 *     tags={"Statistics"},
 *     @OA\Parameter(
 *         name="start_date",
 *         in="query",
 *         description="開始日期 (YYYY-MM-DD)",
 *         required=true,
 *         @OA\Schema(type="string", format="date")
 *     ),
 *     @OA\Parameter(
 *         name="end_date",
 *         in="query",
 *         description="結束日期 (YYYY-MM-DD)",
 *         required=true,
 *         @OA\Schema(type="string", format="date")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="統計資料",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="total_masks", 
 *                 type="integer", 
 *                 example=150,
 *                 description="售出的口罩總數量"
 *             ),
 *             @OA\Property(
 *                 property="total_value", 
 *                 type="number", 
 *                 format="float", 
 *                 example=1234.56,
 *                 description="日期範圍內所有交易的總金額"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="無效的參數請求",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message", 
 *                 type="string", 
 *                 example="無效的參數請求"
 *             ),
 *             @OA\Property(
 *                 property="errors", 
 *                 type="object",
 *                 @OA\Property(
 *                     property="start_date", 
 *                     type="array",
 *                     @OA\Items(type="string", example="開始日期欄位必填")
 *                 ),
 *                 @OA\Property(
 *                     property="end_date", 
 *                     type="array",
 *                     @OA\Items(type="string", example={"結束日期欄位必填", "結束日期必須等於或晚於開始日期"})
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class Statistics
{
}