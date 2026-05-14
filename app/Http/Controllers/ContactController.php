<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $message = Message::create([
                'name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'message' => $this->formatMessage($request),
                'user_id' => auth('sanctum')->id(),
            ]);

            return ResponseHelper::created(
                new MessageResource($message),
                'Message sent successfully'
            );
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());

            return ResponseHelper::serverError(
                'Failed to send message. Please try again later.'
            );
        }
    }

    private function formatMessage(StoreContactRequest $request): string
    {
        $message = $request->input('message');
        $metaInfo = "\n\n--- Additional Info ---\n";
        $metaInfo .= "Subject: " . $request->input('subject') . "\n";

        if ($request->input('phone')) {
            $metaInfo .= "Phone: " . $request->input('phone') . "\n";
        }

        return $message . $metaInfo;
    }
}
