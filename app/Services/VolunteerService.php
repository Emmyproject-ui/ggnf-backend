<?php

namespace App\Services;

use App\Models\Volunteer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class VolunteerService
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function store(User $user, array $data, Request $request): Volunteer
    {
        $volunteer = $user->volunteer_engagements()->create([
            'skills' => $data['skills'],
            'availability' => $data['availability'],
            'phone' => $data['phone'] ?? null,
            'status' => 'pending',
            'form_data' => ['message' => $data['message'] ?? null],
        ]);

        $this->activityLogService->logVolunteer($user->id, $request);

        return $volunteer;
    }

    public function getUserVolunteerHistory(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->volunteer_engagements()
            ->latest()
            ->paginate($perPage);
    }

    public function getAllVolunteers(int $perPage = 15): LengthAwarePaginator
    {
        return Volunteer::with('user')
            ->latest()
            ->paginate($perPage);
    }
}
