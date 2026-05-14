<?php

namespace App\Http\Controllers;

use App\Http\Requests\Donation\StoreDonationRequest;
use App\Http\Resources\DonationResource;
use App\Services\DonationService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService
    ) {}

    public function userHistory(Request $request): JsonResponse
    {
        $donations = $this->donationService->getUserDonations(
            $request->user(),
            $request->get('per_page', 15)
        );

        return ResponseHelper::success(
            DonationResource::collection($donations)->response()->getData(true),
            'Donations retrieved successfully'
        );
    }

    public function index(Request $request): JsonResponse
    {
        return $this->userHistory($request);
    }

    public function store(StoreDonationRequest $request): JsonResponse
    {
        $donation = $this->donationService->store(
            $request->user(),
            $request->validated(),
            $request
        );

        return ResponseHelper::created(
            new DonationResource($donation),
            'Donation recorded successfully'
        );
    }
}
