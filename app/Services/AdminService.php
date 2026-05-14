<?php

namespace App\Services;

use App\Models\User;
use App\Models\Donation;
use App\Models\Volunteer;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminService
{
    public function getUsers(int $perPage = 20): LengthAwarePaginator
    {
        return User::latest()->paginate($perPage);
    }

    public function getDonations(int $perPage = 20): LengthAwarePaginator
    {
        return Donation::with('user')->latest()->paginate($perPage);
    }

    public function getPayments(int $perPage = 20): LengthAwarePaginator
    {
        return Payment::with('user')->latest()->paginate($perPage);
    }

    public function getVolunteers(int $perPage = 20): LengthAwarePaginator
    {
        return Volunteer::with('user')->latest()->paginate($perPage);
    }

    public function getActivityLogs(int $perPage = 50): LengthAwarePaginator
    {
        return ActivityLog::with('user')
            ->latest()
            ->paginate($perPage);
    }

    public function getSecurityLogs(int $perPage = 50): LengthAwarePaginator
    {
        return ActivityLog::with('user')
            ->whereIn('type', ['login', 'admin_action', 'security'])
            ->latest()
            ->paginate($perPage);
    }

    public function getContributions(int $perPage = 20): array
    {
        $donations = Donation::with('user')->latest()->take(100)->get();
        $payments = Payment::with('user')->latest()->take(100)->get();

        $contributions = $donations->map(function ($donation) {
            return [
                'id' => 'DON-' . $donation->id,
                'type' => 'donation',
                'user_id' => $donation->user_id,
                'user_name' => $donation->user ? $donation->user->name : 'Unknown User',
                'user_email' => $donation->user ? $donation->user->email : 'N/A',
                'amount' => $donation->amount,
                'status' => $donation->status ?? 'completed',
                'reference' => $donation->payment_reference,
                'created_at' => $donation->created_at,
            ];
        })->concat($payments->map(function ($payment) {
            return [
                'id' => 'PAY-' . $payment->id,
                'type' => 'payment',
                'user_id' => $payment->user_id,
                'user_name' => $payment->user ? $payment->user->name : 'Unknown User',
                'user_email' => $payment->user ? $payment->user->email : 'N/A',
                'amount' => $payment->amount,
                'status' => $payment->status ?? 'completed',
                'reference' => $payment->id,
                'created_at' => $payment->created_at,
            ];
        }))->sortByDesc('created_at')->values()->take($perPage);

        return $contributions->toArray();
    }

    public function getStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_donations' => Donation::count(),
            'total_volunteers' => Volunteer::count(),
            'pending_volunteers' => Volunteer::where('status', 'pending')->count(),
            'recent_logins' => ActivityLog::where('type', 'login')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'recent_donations' => Donation::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    public function updateVolunteerStatus(int $id, string $status): Volunteer
    {
        $volunteer = Volunteer::findOrFail($id);
        $volunteer->update(['status' => $status]);
        return $volunteer;
    }
}
