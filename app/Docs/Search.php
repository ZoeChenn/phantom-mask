<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Search",
 *     description="和搜尋有關的 API 們"
 * )
 * 
 * @OA\Get(
 *     path="/api/search",
 *     operationId="getPharmacyAndMaskbyName",
 *     summary="以關鍵字搜尋藥局或口罩名稱，並依相關性排序",
 *     description="以關鍵字搜尋藥局名稱或口罩品牌，結果會依照相關性排序。搜尋結果會包含藥局和口罩，並標記其類型。",
 *     tags={"Search"},
 *     @OA\Parameter(
 *         name="q",
 *         in="query",
 *         description="關鍵字（至少 2 個字元）",
 *         required=true,
 *         @OA\Schema(type="string", minLength=2)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="搜尋結果",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 oneOf={
 *                     @OA\Schema(
 *                         type="object",
 *                         @OA\Property(
 *                             property="id",
 *                             type="integer",
 *                             example=3,
 *                             description="藥局 ID"
 *                         ),
 *                         @OA\Property(
 *                             property="name",
 *                             type="string",
 *                             example="First Care Rx",
 *                             description="藥局名稱"
 *                         ),
 *                         @OA\Property(
 *                             property="cash_balance",
 *                             type="number",
 *                             format="float",
 *                             example="222.52",
 *                             description="現金餘額"
 *                         ),
 *                         @OA\Property(
 *                             property="result_type",
 *                             type="string",
 *                             example="pharmacy",
 *                             description="結果類型標記"
 *                         )
 *                     ),
 *                     @OA\Schema(
 *                         type="object",
 *                         @OA\Property(
 *                             property="id",
 *                             type="integer",
 *                             example=5,
 *                             description="口罩 ID"
 *                         ),
 *                         @OA\Property(
 *                             property="brand",
 *                             type="string",
 *                             example="3M",
 *                             description="口罩品牌"
 *                         ),
 *                         @OA\Property(
 *                             property="color",
 *                             type="string",
 *                             example="白色",
 *                             description="口罩顏色"
 *                         ),
 *                         @OA\Property(
 *                             property="price",
 *                             type="number",
 *                             format="float",
 *                             example=25.5,
 *                             description="口罩價格"
 *                         ),
 *                         @OA\Property(
 *                             property="quantity_per_pack",
 *                             type="integer",
 *                             example=10,
 *                             description="每包數量"
 *                         ),
 *                         @OA\Property(
 *                             property="result_type",
 *                             type="string",
 *                             example="mask",
 *                             description="結果類型標記"
 *                         )
 *                     )
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="無效的輸入",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message", 
 *                 type="string", 
 *                 example="關鍵字欄位必填"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="驗證錯誤",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message", 
 *                 type="string", 
 *                 example="提供的資料無效。"
 *             ),
 *             @OA\Property(
 *                 property="errors", 
 *                 type="object",
 *                 @OA\Property(
 *                     property="q", 
 *                     type="array",
 *                     @OA\Items(type="string", example="關鍵字欄位必須至少有 2 個字元。")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class Search
{
}