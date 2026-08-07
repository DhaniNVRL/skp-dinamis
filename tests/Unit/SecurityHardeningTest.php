<?php

namespace Tests\Unit;

use App\Models\Answer;
use App\Http\Middleware\SecurityHeaders;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityHardeningTest extends TestCase
{
    public function test_rich_text_sanitizer_removes_executable_content(): void
    {
        $output = (new HtmlSanitizer())->sanitize(
            '<p onclick="alert(1)">A<script>alert(1)</script>'
            .'<img src="x" onerror="alert(1)"><a href="javascript:alert(1)">B</a>'
            .'<table style="width: 100%; position: fixed"><tr><td colspan="2">C</td></tr></table></p>'
        );

        $this->assertStringNotContainsStringIgnoringCase('<script', $output);
        $this->assertStringNotContainsStringIgnoringCase('onclick', $output);
        $this->assertStringNotContainsStringIgnoringCase('javascript:', $output);
        $this->assertStringNotContainsStringIgnoringCase('<img', $output);
        $this->assertStringNotContainsStringIgnoringCase('position', $output);
        $this->assertStringContainsString('<table', $output);
        $this->assertStringContainsString('colspan="2"', $output);
    }

    public function test_answers_are_encoded_once_and_legacy_double_json_is_readable(): void
    {
        $answer = new Answer();
        $answer->answer = ['ranking' => [1, 2], 'note' => 'aman'];

        $stored = $answer->getAttributes()['answer'];
        $this->assertSame('{"ranking":[1,2],"note":"aman"}', $stored);

        $answer->setRawAttributes(['answer' => json_encode($stored, JSON_THROW_ON_ERROR)]);
        $this->assertSame(
            ['ranking' => [1, 2], 'note' => 'aman'],
            $answer->answer
        );
    }

    public function test_security_headers_are_added_and_hsts_is_https_only(): void
    {
        $middleware = new SecurityHeaders();
        $httpResponse = $middleware->handle(
            Request::create('http://example.test'),
            fn () => new Response('ok')
        );

        $this->assertSame('nosniff', $httpResponse->headers->get('X-Content-Type-Options'));
        $this->assertSame('SAMEORIGIN', $httpResponse->headers->get('X-Frame-Options'));
        $this->assertFalse($httpResponse->headers->has('Strict-Transport-Security'));

        $httpsResponse = $middleware->handle(
            Request::create('https://example.test'),
            fn () => new Response('ok')
        );
        $this->assertTrue($httpsResponse->headers->has('Strict-Transport-Security'));
    }
}
