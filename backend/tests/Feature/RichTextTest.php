<?php

use App\Models\About;
use App\Models\Venture;
use App\Support\RichText;

it('keeps formatting the editor produces', function () {
    $html = RichText::html('<p>Hello <strong>bold</strong> and <em>italic</em></p><ul><li>One</li><li>Two</li></ul><h3>Title</h3><blockquote>Quote</blockquote><p><a href="https://example.com/a">link</a></p>');

    expect($html)->toContain('<strong>bold</strong>', '<em>italic</em>', '<ul>', '<li>One</li>', '<h3>Title</h3>', '<blockquote>Quote</blockquote>')
        ->and($html)->toContain('href="https://example.com/a"');
});

it('removes anything dangerous', function (string $dirty, array $mustNotContain) {
    $clean = (string) RichText::html($dirty);

    foreach ($mustNotContain as $bad) {
        expect(strtolower($clean))->not->toContain($bad);
    }
})->with([
    'script tag' => ['<p>Hi</p><script>alert(1)</script>', ['<script', 'alert(1)']],
    'event handler' => ['<p onclick="steal()">Hi</p>', ['onclick', 'steal']],
    'javascript link' => ['<a href="javascript:alert(1)">x</a>', ['javascript:']],
    'data link' => ['<a href="data:text/html;base64,AAAA">x</a>', ['data:']],
    'image with handler' => ['<img src="x" onerror="alert(1)">', ['<img', 'onerror']],
    'iframe' => ['<p>a</p><iframe src="https://evil.example"></iframe>', ['<iframe', 'evil.example']],
    'inline style overlay' => ['<p style="position:fixed;inset:0">Hi</p>', ['style', 'position']],
    'class names' => ['<p class="fixed inset-0">Hi</p>', ['class=']],
    'relative link' => ['<a href="/admin">x</a>', ['href="/admin"']],
    'style tag' => ['<style>body{display:none}</style><p>Hi</p>', ['<style', 'display:none']],
]);

it('marks links so they cannot access the opener', function () {
    expect(RichText::html('<a href="https://example.com">x</a>'))->toContain('rel="noopener noreferrer"');
});

it('turns legacy plain text into paragraphs and escapes it', function () {
    expect(RichText::html("Tom & Jerry\n\nSecond\nline"))->toBe("<p>Tom &amp; Jerry</p><p>Second<br>\nline</p>");
});

it('treats empty editor output as no content', function () {
    expect(RichText::html(null))->toBeNull()
        ->and(RichText::html(''))->toBeNull()
        ->and(RichText::html('<p></p>'))->toBeNull()
        ->and(RichText::html('<p><br></p>'))->toBeNull();
});

it('extracts plain text for table cells', function () {
    expect(RichText::text('<p>Hello&nbsp;<strong>world</strong></p><p>Next</p>'))->toBe('Hello world Next');
});

it('publishes only sanitized HTML through the API', function () {
    About::create(['bio' => '<p>Safe</p><script>alert(1)</script>']);
    Venture::create(['name' => 'Acme', 'slug' => 'acme', 'industry' => 'X', 'country' => 'BN', 'description' => '<p onmouseover="x()">Desc <a href="javascript:evil()">link</a></p>']);

    $response = $this->getJson('/api/v1/home')->assertOk();

    expect($response->json('data.about.bio'))->toBe('<p>Safe</p>')
        ->and($response->json('data.ventures.0.description'))->not->toContain('onmouseover', 'javascript:')
        ->and($response->json('data.ventures.0.description'))->toContain('Desc');
});
