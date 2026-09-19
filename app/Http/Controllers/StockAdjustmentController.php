<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
    ) {
    }

    public function store(
        StockAdjustmentRequest $request
    ): JsonResponse {
        $movement = DB::transaction(function () use ($request) {
            $product = Product::findOrFail((int) $request->integer('product_id'));

            // Determine stockable: explicit stockable_type & stockable_id, or warehouse_id, or shop_id, or employee's shop
            $stockable = null;

            if ($request->filled('warehouse_id')) {
                $stockable = Warehouse::findOrFail($request->integer('warehouse_id'));
            } elseif ($request->filled('shop_id')) {
                $stockable = Shop::findOrFail($request->integer('shop_id'));
            } elseif ($request->filled('stockable_type') && $request->filled('stockable_id')) {
                $type = $request->input('stockable_type');
                $id = $request->integer('stockable_id');
                $stockable = $type === 'warehouse' || $type === Warehouse::class
                    ? Warehouse::findOrFail($id)
                    : Shop::findOrFail($id);
            } else {
                $employee = $request->user();
                if ($employee?->shop_id) {
                    $stockable = Shop::findOrFail($employee->shop_id);
                } else {
                    $stockable = Shop::first() ?? Warehouse::first();
                }
            }

            if (! $stockable) {
                return null;
            }

            return $this->stockService->adjust(
                product: $product,
                stockable: $stockable,
                newQuantity: (int) $request->integer('quantity'),
                employee: $request->user(),
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