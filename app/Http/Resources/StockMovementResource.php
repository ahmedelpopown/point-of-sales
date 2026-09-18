<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'type' => $this->type->value,

            'quantity' => $this->quantity,

            'quantity_before' => $this->quantity_before,

            'quantity_after' => $this->quantity_after,

            'note' => $this->note,

            'product' => [
                'id' => $this->product?->id,
                'name' => $this->product?->name,
                'barcode' => $this->product?->barcode,
            ],

            'employee' => $this->when(
                $this->employee,
                fn () => [
                    'id' => $this->employee->id,
                    'name' => $this->employee->full_name,
                ]
            ),

            'reference' => [
                'type' => $this->reference_type,
                'id' => $this->reference_id,
            ],

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}