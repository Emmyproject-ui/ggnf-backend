<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\DonationResource;
use App\Http\Resources\VolunteerResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ActivityLogResource;
use App\Services\AdminService;
use App\Helpers\ResponseHelper;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    public function users(Request $request): JsonResponse
    {
        $users = $this->adminService->getUsers(
            $request->get('per_page', 20)
        );

        return ResponseHelper::success(
            UserResource::collection($users)->response()->getData(true),
            'Users retrieved successfully'
        );
    }

    public function donations(Request $request): JsonResponse
    {
        $donations = $this->adminService->getDonations(
            $request->get('per_page', 20)
        );

        return ResponseHelper::success(
            DonationResource::collection($donations)->response()->getData(true),
            'Donations retrieved successfully'
        );
    }

    public function payments(Request $request): JsonResponse
    {
        $payments = $this->adminService->getPayments(
            $request->get('per_page', 20)
        );

        return ResponseHelper::success(
            PaymentResource::collection($payments)->response()->getData(true),
            'Payments retrieved successfully'
        );
    }

    public function contributions(): JsonResponse
    {
        $contributions = $this->adminService->getContributions(20);

        return ResponseHelper::success(
            $contributions,
            'Contributions retrieved successfully'
        );
    }

    public function volunteers(Request $request): JsonResponse
    {
        $volunteers = $this->adminService->getVolunteers(
            $request->get('per_page', 20)
        );

        return ResponseHelper::success(
            VolunteerResource::collection($volunteers)->response()->getData(true),
            'Volunteers retrieved successfully'
        );
    }

    public function messages(Request $request): JsonResponse
    {
        $messages = Message::latest()
            ->paginate($request->get('per_page', 20));

        return ResponseHelper::success(
            MessageResource::collection($messages)->response()->getData(true),
            'Messages retrieved successfully'
        );
    }

    public function activityLogs(Request $request): JsonResponse
    {
        $logs = $this->adminService->getActivityLogs(
            $request->get('per_page', 50)
        );

        return ResponseHelper::success(
            ActivityLogResource::collection($logs)->response()->getData(true),
            'Activity logs retrieved successfully'
        );
    }

    public function securityLogs(Request $request): JsonResponse
    {
        $logs = $this->adminService->getSecurityLogs(
            $request->get('per_page', 50)
        );

        return ResponseHelper::success(
            ActivityLogResource::collection($logs)->response()->getData(true),
            'Security logs retrieved successfully'
        );
    }

    public function stats(): JsonResponse
    {
        $stats = $this->adminService->getStats();

        return ResponseHelper::success(
            $stats,
            'Stats retrieved successfully'
        );
    }

    // Alias methods for backward compatibility
    public function getUsers(Request $request): JsonResponse
    {
        return $this->users($request);
    }

    public function getDonations(Request $request): JsonResponse
    {
        return $this->donations($request);
    }

    public function getVolunteers(Request $request): JsonResponse
    {
        return $this->volunteers($request);
    }

    public function getActivityLogs(Request $request): JsonResponse
    {
        return $this->activityLogs($request);
    }

    public function approveVolunteer(int $id): JsonResponse
    {
        $volunteer = $this->adminService->updateVolunteerStatus($id, 'approved');

        return ResponseHelper::success(
            new VolunteerResource($volunteer->load('user')),
            'Volunteer approved successfully'
        );
    }

    public function rejectVolunteer(int $id): JsonResponse
    {
        $volunteer = $this->adminService->updateVolunteerStatus($id, 'rejected');

        return ResponseHelper::success(
            new VolunteerResource($volunteer->load('user')),
            'Volunteer rejected successfully'
        );
    }

    public function loginLogs(Request $request): JsonResponse
    {
        return $this->securityLogs($request);
    }
}
