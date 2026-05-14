<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(
        ?int $userId,
        string $type,
        string $description,
        ?array $metadata = null,
        ?Request $request = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'type' => $type,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    public function logAuth(int $userId, string $action, Request $request, ?array $metadata = null): ActivityLog
    {
        return $this->log(
            $userId,
            $action,
            "{$action} performed",
            $metadata,
            $request
        );
    }

    public function logDonation(int $userId, float $amount, string $reference, Request $request): ActivityLog
    {
        return $this->log(
            $userId,
            'donation',
            "Donation of ₦" . number_format($amount, 2) . " received",
            [
                'amount' => $amount,
                'payment_reference' => $reference,
            ],
            $request
        );
    }

    public function logVolunteer(int $userId, Request $request): ActivityLog
    {
        return $this->log(
            $userId,
            'volunteer',
            "New volunteer signup",
            null,
            $request
        );
    }
}
