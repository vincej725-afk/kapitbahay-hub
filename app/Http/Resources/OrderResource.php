<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Excludes 'internal_notes' (staff-only) and 'customer_id' (internal FK) per Question 4(c).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'status' => $this->status,
            'total' => (float) $this->total,
            'pickup_address' => $this->pickup_address,
            'pickup_date' => $this->pickup_date?->format('Y-m-d'),
            'created_at' => $this->created_at?->toIso8601String(),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
