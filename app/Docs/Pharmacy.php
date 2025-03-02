<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Pharmacies",
 *     description="和藥局有關的 API 們"
 * )
 * 
 * @OA\Get(
 *     path="/api/pharmacies",
 *     operationId="getPharmacyByOpenHour",
 *     summary="列出特定時間和星期幾營業的藥局",
 *     description="回傳在指定日期和時間營業的藥局列表。",
 *     tags={"Pharmacies"},
 *     @OA\Parameter(
 *         name="day",
 *         in="query",
 *         description="星期幾（1-7，其中 1 代表星期一）",
 *         required=true,
 *         @OA\Schema(type="integer", minimum=1, maximum=7)
 *     ),
 *     @OA\Parameter(
 *         name="time",
 *         in="query",
 *         description="24 小時制時間格式（HH:MM）",
 *         required=true,
 *         @OA\Schema(type="string", format="time")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="藥局列表",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1, description="藥局 ID"),
 *                 @OA\Property(property="name", type="string", example="健康藥局", description="藥局名稱"),
 *                 @OA\Property(property="day_of_week", type="string", example="Monday", description="營業日"),
 *                 @OA\Property(property="open_time", type="string", example="09:00", description="開始營業時間"),
 *                 @OA\Property(property="close_time", type="string", example="18:00", description="結束營業時間")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="無效的參數請求",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="無效的參數請求"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(property="day", type="array", @OA\Items(type="string", example="日期欄位必填")),
 *                 @OA\Property(property="time", type="array", @OA\Items(type="string", example="時間欄位必填"))
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/pharmacies/{id}/masks",
 *     operationId="getPharmacyMasks",
 *     summary="列出特定藥局販售的所有口罩",
 *     description="回傳指定藥局及其銷售的口罩列表，口罩列表依價格由小至大排序。",
 *     tags={"Pharmacies"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="藥局 ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="口罩列表",
 *         @OA\JsonContent(
 *             @OA\Property(property="pharmacy_id", type="integer", example=1, description="藥局 ID"),
 *             @OA\Property(property="pharmacy_name", type="string", example="健康藥局", description="藥局名稱"),
 *             @OA\Property(
 *                 property="masks",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Mask")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="無效的參數請求",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="無效的參數請求"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(property="sort", type="array", @OA\Items(type="string", example="排序參數必須是 price 或 name"))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="找不到藥局",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="找不到指定的藥局"),
 *             @OA\Property(property="requested_id", type="integer", example=25),
 *             @OA\Property(property="status", type="string", example="error")
 *         )
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/pharmacies/filter",
 *     operationId="getPharmaciesByMaskFilter",
 *     summary="列出特定價格範圍內有超過或少於 x 種口罩產品的藥局",
 *     description="根據藥局販售的口罩種類數量和價格範圍來過濾藥局，回傳符合條件的藥局及其銷售的口罩詳細資料。",
 *     tags={"Pharmacies"},
 *     @OA\Parameter(
 *         name="mask_count",
 *         in="query",
 *         description="口罩數量",
 *         required=false,
 *         @OA\Schema(type="integer", minimum=0)
 *     ),
 *     @OA\Parameter(
 *         name="comparison",
 *         in="query",
 *         description="比較方式：超過(more)或少於(less)",
 *         required=false,
 *         @OA\Schema(type="string", enum={"more", "less"})
 *     ),
 *     @OA\Parameter(
 *         name="min_price",
 *         in="query",
 *         description="最低價格",
 *         required=false,
 *         @OA\Schema(type="number", format="float", minimum=0)
 *     ),
 *     @OA\Parameter(
 *         name="max_price",
 *         in="query",
 *         description="最高價格",
 *         required=false,
 *         @OA\Schema(type="number", format="float", minimum=0.01)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="帶有口罩詳細資料的藥局列表",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1, description="藥局 ID"),
 *                 @OA\Property(property="name", type="string", example="健康藥局", description="藥局名稱"),
 *                 @OA\Property(property="mask_count", type="integer", example=5, description="符合過濾條件的口罩數量"),
 *                 @OA\Property(
 *                     property="masks",
 *                     type="array",
 *                     description="符合條件的口罩列表",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1, description="口罩 ID"),
 *                         @OA\Property(property="brand", type="string", example="3M", description="口罩品牌"),
 *                         @OA\Property(property="color", type="string", example="白色", description="口罩顏色"),
 *                         @OA\Property(property="quantity_per_pack", type="integer", example=5, description="每包口罩數量"),
 *                         @OA\Property(property="price", type="number", format="float", example=25.5, description="口罩價格")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="無效的參數請求",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="無效的參數請求"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(property="mask_count", type="array", @OA\Items(type="string", example="口罩數量必須是整數")),
 *                 @OA\Property(property="comparison", type="array", @OA\Items(type="string", example="比較方式必須是：超過(more)或少於(less)")),
 *                 @OA\Property(property="min_price", type="array", @OA\Items(type="string", example="最低價格必須是正數")),
 *                 @OA\Property(property="max_price", type="array", @OA\Items(type="string", example="最高價格必須是正數"))
 *             )
 *         )
 *     )
 * )
 */
class Pharmacy
{
}