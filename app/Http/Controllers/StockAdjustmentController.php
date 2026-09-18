<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Http\Resources\StockMovementResource;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {
    }

    public function store(
        StockAdjustmentRequest $request
    ): JsonResponse {
        $movement = DB::transaction(function () use ($request) {
            return $this->inventoryService->adjust(
                productId: (int) $request->integer('product_id'),
                newQuantity: (int) $request->integer('quantity'),
                employeeId: $request->user()?->employee_id,
                note: $request->input('note'),
            );
        });

        if ($movement === null) {
            return response()->json([
                'message' => 'No stock change was required.',
                'data' => null,
            ], 200);
        }

        return response()->json([
            'message' => 'Stock adjusted successfully.',
            'data' => new StockMovementResource(
                $movement->load([
                    'product',
                    'employee',
                ])
            ),
        ], 200);
    }
}