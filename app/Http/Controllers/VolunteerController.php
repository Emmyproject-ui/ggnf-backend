<?php

namespace App\Http\Controllers;

use App\Http\Requests\Volunteer\StoreVolunteerRequest;
use App\Http\Resources\VolunteerResource;
use App\Services\VolunteerService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VolunteerController extends Controller
{
    public function __construct(
        protected VolunteerService $volunteerService
    ) {}

    public function history(Request $request): JsonResponse
    {
        $volunteers = $this->volunteerService->getUserVolunteerHistory(
            $request->user(),
            $request->get('per_page', 15)
        );

        return ResponseHelper::success(
            VolunteerResource::collection($volunteers)->response()->getData(true),
            'Volunteer history retrieved successfully'
        );
    }

    public function index(Request $request): JsonResponse
    {
        return $this->history($request);
    }

    public function store(StoreVolunteerRequest $request): JsonResponse
    {
        $volunteer = $this->volunteerService->store(
            $request->user(),
            $request->validated(),
            $request
        );

        return ResponseHelper::created(
            new VolunteerResource($volunteer),
            'Volunteer signup successful'
        );
    }
}
