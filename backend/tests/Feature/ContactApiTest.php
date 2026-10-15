<?php

use App\Mail\AutoReply;
use App\Mail\ContactReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('contact');
    config(['mail.admin_notify_email' => 'admin@example.com']);
});

function validContact(array $overrides = []): array
{
    return array_merge([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '+673 123 4567',
        'subject' => 'Partnership',
        'message' => 'I would like to talk.',
    ], $overrides);
}

it('stores the message and queues both emails', function () {
    Mail::fake();

    $this->postJson('/api/v1/contact', validContact())->assertCreated()->assertJsonStructure(['message']);

    $this->assertDatabaseHas('contact_messages', ['email' => 'jane@example.com', 'is_read' => false]);
    expect(ContactMessage::first()->ip)->not->toBeNull();

    Mail::assertQueued(ContactReceived::class, fn ($m) => $m->hasTo('admin@example.com'));
    Mail::assertQueued(AutoReply::class, fn ($m) => $m->hasTo('jane@example.com'));
});

it('returns 422 for invalid input', function () {
    $this->postJson('/api/v1/contact', ['email' => 'not-an-email', 'message' => str_repeat('a', 2001)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'message']);

    expect(ContactMessage::count())->toBe(0);
});

it('rejects submissions that fill the honeypot', function () {
    Mail::fake();

    $this->postJson('/api/v1/contact', validContact(['website' => 'http://spam.test']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('website');

    expect(ContactMessage::count())->toBe(0);
    Mail::assertNothingQueued();
});

it('throttles to 3 requests per minute per IP', function () {
    Mail::fake();

    foreach (range(1, 3) as $i) {
        $this->postJson('/api/v1/contact', validContact())->assertCreated();
    }

    $this->postJson('/api/v1/contact', validContact())->assertTooManyRequests();
});

it('verifies the Turnstile token when a secret is configured', function () {
    config(['services.turnstile.secret' => 'secret']);
    Illuminate\Support\Facades\Http::fake([
        'challenges.cloudflare.com/*' => Illuminate\Support\Facades\Http::response(['success' => false]),
    ]);

    $this->postJson('/api/v1/contact', validContact(['turnstile_token' => 'bad']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('turnstile_token');
});
