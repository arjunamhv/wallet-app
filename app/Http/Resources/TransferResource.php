<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'user_id' => $this->user_id,
            'target_user_id' => $this->target_user_id,
            'amount' => $this->amount,
            'remarks' => $this->remarks,
            'balance_before' => $this->balance_before,
            'balance_after' => $this->balance_after,
        ];
    }
}
