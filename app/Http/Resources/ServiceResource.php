<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'unit' => $this->unit,
            'quantity' => $this->whenPivotLoaded('order_service', function () {
                return (float) $this->pivot->quantity;
            }),
            'subtotal' => $this->whenPivotLoaded('order_service', function () {
                return (float) $this->pivot->subtotal;
            }),
        ];
    }
}
