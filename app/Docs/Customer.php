<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Customers",
 *     description="和顧客有關的 API 們"
 * )
 * 
 * @OA\Get(
 *     path="/api/customers/top/{count}",
 *     operationId="getTopCustomers",
 *     summary="統計指定日期區間購買口罩總金額最高的前 X 名使用者",
 *     description="回傳在指定日期範圍內，依照購買總金額排名的前幾名顧客列表。",
 *     tags={"Customers"},
 *     @OA\Parameter(
 *         name="count",
 *         in="path",
 *         description="要列出前幾名顧客",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1)
 *     ),
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
 *         description="顧客排名列表",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(
 *                     property="total_amount", 
 *                     type="number", 
 *                     format="float", 
 *                     example=123.45,
 *                     description="指定日期範圍內的總交易金額"
 *                 )
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
 *                     @OA\Items(
 *                         type="string", 
 *                         example={"結束日期欄位必填", "結束日期必須等於或晚於開始日期"}
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class Customer
{
}