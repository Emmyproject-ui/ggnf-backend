<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user ? $this->user->name : 'Unknown User',
            'user_email' => $this->user ? $this->user->email : 'N/A',
            'amount' => number_format($this->amount, 2),
            'amount_raw' => (float) $this->amount,
            'payment_reference' => $this->payment_reference,
            'payment_method' => $this->payment_method ?? 'Paystack',
            'cause' => $this->cause ?? 'General',
            'status' => $this->status ?? 'completed',
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
