<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Pharmacy",
 *     title="Pharmacy",
 *     description="藥局",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Keystone Pharmacy", description="藥局名稱"),
 *     @OA\Property(property="cash_balance", type="number", format="decimal", example=1000.00, description="藥局現金餘額")
 * )
 * 
 * @OA\Schema(
 *     schema="Mask",
 *     title="Mask",
 *     description="口罩",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="brand", type="string", example="True Barrier", description="口罩品牌"),
 *     @OA\Property(property="color", type="string", example="blue", description="口罩顏色"),
 *     @OA\Property(property="quantity_per_pack", type="integer", example=10, description="每包口罩數量")
 * )
 * 
 * @OA\Schema(
 *     schema="Customer",
 *     title="Customer",
 *     description="顧客",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe", description="顧客姓名"),
 *     @OA\Property(property="cash_balance", type="number", format="decimal", example=100.00, description="顧客現金餘額")
 * )
 * 
 * @OA\Schema(
 *     schema="PurchaseHistory",
 *     title="Purchase History",
 *     description="顧客的交易歷史紀錄",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="customer_id", type="integer", example=1, description="顧客 ID"),
 *     @OA\Property(property="pharmacy_id", type="integer", example=1, description="藥局 ID"),
 *     @OA\Property(property="mask_id", type="integer", example=1, description="口罩 ID"),
 *     @OA\Property(property="transaction_amount", type="number", format="decimal", example=12.99, description="交易金額"),
 *     @OA\Property(property="transaction_date", type="string", example="2021-01-04 15:18:51", description="交易日期時間")
 * )
 * 
 * @OA\Schema(
 *     schema="PharmacyMask",
 *     title="Pharmacy Mask",
 *     description="藥局與口罩的關係，包含藥局特定的口罩價格",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="pharmacy_id", type="integer", example=1, description="藥局 ID"),
 *     @OA\Property(property="mask_id", type="integer", example=1, description="口罩 ID"),
 *     @OA\Property(property="price", type="number", format="decimal", example=12.99, description="藥局特定的口罩價格")
 * )
 * 
 * @OA\Schema(
 *     schema="PharmacyOpeningHour",
 *     title="Pharmacy Opening Hour",
 *     description="藥局營業時間",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="pharmacy_id", type="integer", example=1, description="藥局 ID"),
 *     @OA\Property(
 *         property="day_of_week", 
 *         type="string", 
 *         example="Monday", 
 *         description="星期幾 (例如: Monday, Tuesday, Wednesday...)"
 *     ),
 *     @OA\Property(
 *         property="open_time", 
 *         type="string", 
 *         format="time",
 *         example="09:00:00", 
 *         description="開始營業時間 (24小時制)"
 *     ),
 *     @OA\Property(
 *         property="close_time", 
 *         type="string", 
 *         format="time",
 *         example="18:00:00", 
 *         description="結束營業時間 (24小時制)"
 *     )
 * )
 */
class Models
{
}