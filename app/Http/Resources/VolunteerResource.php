<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user ? $this->user->name : 'Unknown User',
            'user_email' => $this->user ? $this->user->email : 'N/A',
            'skills' => $this->skills,
            'availability' => $this->availability ?? 'flexible',
            'phone' => $this->phone,
            'status' => $this->status ?? 'pending',
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
