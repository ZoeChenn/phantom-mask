<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Purchases",
 *     description="和購買有關的 API 們"
 * )
 * 
 * @OA\Post(
 *     path="/api/purchases",
 *     operationId="buyMask",
 *     summary="處理使用者購買口罩的交易",
 *     description="處理顧客從藥局購買口罩的交易。驗證資金是否充足，更新顧客餘額和藥局餘額，並建立購買記錄。",
 *     tags={"Purchases"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"customer_id", "pharmacy_id", "mask_id", "quantity"},
 *             @OA\Property(
 *                 property="customer_id", 
 *                 type="integer", 
 *                 example=1,
 *                 description="進行購買的顧客 ID"
 *             ),
 *             @OA\Property(
 *                 property="pharmacy_id", 
 *                 type="integer", 
 *                 example=1,
 *                 description="銷售口罩的藥局 ID"
 *             ),
 *             @OA\Property(
 *                 property="mask_id", 
 *                 type="integer", 
 *                 example=1,
 *                 description="被購買的口罩 ID"
 *             ),
 *             @OA\Property(
 *                 property="quantity", 
 *                 type="integer", 
 *                 example=1, 
 *                 minimum=1,
 *                 description="購買的口罩數量"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="購買成功",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message", 
 *                 type="string", 
 *                 example="購買成功",
 *                 description="狀態訊息"
 *             ),
 *             @OA\Property(
 *                 property="transaction", 
 *                 type="object",
 *                 description="交易詳情",
 *                 @OA\Property(
 *                     property="transaction_id", 
 *                     type="integer", 
 *                     example=123,
 *                     description="交易 ID"
 *                 ),
 *                 @OA\Property(
 *                     property="transaction_date", 
 *                     type="string", 
 *                     format="date-time", 
 *                     example="2021-01-13 01:18:23",
 *                     description="交易日期時間"
 *                 ),
 *                 @OA\Property(
 *                     property="transaction_amount", 
 *                     type="number", 
 *                     format="decimal", 
 *                     example=12.99,
 *                     description="交易總金額"
 *                 ),
 *                 @OA\Property(
 *                     property="status", 
 *                     type="string", 
 *                     example="success",
 *                     description="交易狀態"
 *                 ),
 *                 @OA\Property(
 *                     property="mask_id", 
 *                     type="integer", 
 *                     example=5,
 *                     description="購買的口罩 ID"
 *                 ),
 *                 @OA\Property(
 *                     property="pharmacy_id", 
 *                     type="integer", 
 *                     example=3,
 *                     description="銷售的藥局 ID"
 *                 ),
 *                 @OA\Property(
 *                     property="price", 
 *                     type="number", 
 *                     format="decimal", 
 *                     example=12.99,
 *                     description="口罩單價"
 *                 ),
 *                 @OA\Property(
 *                     property="quantity", 
 *                     type="integer", 
 *                     example=1,
 *                     description="購買數量"
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="customer", 
 *                 type="object",
 *                 description="顧客資訊",
 *                 @OA\Property(
 *                     property="customer_id", 
 *                     type="integer", 
 *                     example=10,
 *                     description="顧客 ID"
 *                 ),
 *                 @OA\Property(
 *                     property="previous_cash_balance", 
 *                     type="number", 
 *                     format="decimal", 
 *                     example=100.00,
 *                     description="交易前的現金餘額"
 *                 ),
 *                 @OA\Property(
 *                     property="new_cash_balance", 
 *                     type="number", 
 *                     format="decimal", 
 *                     example=87.01,
 *                     description="交易後的現金餘額"
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
 *                 example="無效的參數請求",
 *                 description="錯誤訊息"
 *             ),
 *             @OA\Property(
 *                 property="errors", 
 *                 type="object",
 *                 @OA\Property(
 *                     property="customer_id", 
 *                     type="array",
 *                     @OA\Items(type="string", example="無此顧客 ID")
 *                 ),
 *                 @OA\Property(
 *                     property="pharmacy_id", 
 *                     type="array",
 *                     @OA\Items(type="string", example="無此藥局 ID")
 *                 ),
 *                 @OA\Property(
 *                     property="mask_id", 
 *                     type="array",
 *                     @OA\Items(type="string", example="無此口罩 ID")
 *                 ),
 *                 @OA\Property(
 *                     property="quantity", 
 *                     type="array",
 *                     @OA\Items(type="string", example="數量不能小於 1")
 *                 ),
 *                 @OA\Property(
 *                     property="pharmacy_mask", 
 *                     type="array",
 *                     @OA\Items(type="string", example="此藥局未提供該口罩")
 *                 ),
 *                 @OA\Property(
 *                     property="cash_balance", 
 *                     type="array",
 *                     @OA\Items(type="string", example="資金不足")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message", 
 *                 type="string", 
 *                 example="購買失敗: 資料庫錯誤",
 *                 description="錯誤訊息"
 *             )
 *         )
 *     )
 * )
 */
class Purchase
{
}