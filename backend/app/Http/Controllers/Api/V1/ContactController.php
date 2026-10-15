<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\AutoReply;
use App\Mail\ContactReceived;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __invoke(ContactRequest $request): JsonResponse
    {
        $message = ContactMessage::create($request->safe()->only(['name', 'email', 'phone', 'subject', 'message']) + [
            'ip' => $request->ip(),
        ]);

        if ($notify = config('mail.admin_notify_email')) {
            Mail::to($notify)->queue(new ContactReceived($message));
        }

        Mail::to($message->email)->queue(new AutoReply($message));

        return response()->json([
            'message' => 'Thank you. Your message has been received and we will reply shortly.',
        ], 201);
    }
}
