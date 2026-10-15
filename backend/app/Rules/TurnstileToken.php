<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class TurnstileToken implements ValidationRule
{
    public bool $implicit = true;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.turnstile.secret');

        if (blank($secret)) {
            if (app()->environment('local', 'testing')) {
                return;
            }

            $fail('Verification is not configured.');

            return;
        }

        if (blank($value)) {
            $fail('Please complete the verification.');

            return;
        }

        $response = Http::asForm()->timeout(10)->post(config('services.turnstile.url'), [
            'secret' => $secret,
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->ok() || ! $response->json('success')) {
            $fail('Verification failed. Please try again.');
        }
    }
}
