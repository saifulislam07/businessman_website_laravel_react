<?php

namespace App\Http\Requests;

use App\Rules\TurnstileToken;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
            // Honeypot: real users never see this field, bots fill it in.
            'website' => ['prohibited'],
            'turnstile_token' => [new TurnstileToken],
        ];
    }

    public function messages(): array
    {
        return [
            'website.prohibited' => 'Your message could not be sent. Please reload the page and try again.',
        ];
    }
}
