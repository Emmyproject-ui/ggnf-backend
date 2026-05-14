<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DonationService
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function store(User $user, array $data, Request $request): Donation
    {
        $donation = $user->donations()->create([
            'amount' => $data['amount'],
            'payment_reference' => $data['payment_reference'],
            'cause' => $data['cause'] ?? 'General',
            'payment_method' => $data['payment_method'] ?? 'paystack',
            'status' => 'completed',
        ]);

        $this->activityLogService->logDonation(
            $user->id,
            $donation->amount,
            $donation->payment_reference,
            $request
        );

        return $donation;
    }

    public function getUserDonations(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->donations()
            ->latest()
            ->paginate($perPage);
    }

    public function getAllDonations(int $perPage = 15): LengthAwarePaginator
    {
        return Donation::with('user')
            ->latest()
            ->paginate($perPage);
    }
}
