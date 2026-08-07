<?php

namespace Tests\Unit;

use App\Http\Middleware\PreserveActiveTab;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class PreserveActiveTabTest extends TestCase
{
    public function test_it_appends_submitted_active_tab_to_local_redirect(): void
    {
        $request = Request::create(
            'http://localhost/groups/3?tab=profile',
            'POST',
            ['_active_tab' => 'profile']
        );

        $response = (new PreserveActiveTab())->handle(
            $request,
            fn () => new RedirectResponse('http://localhost/groups/3')
        );

        $this->assertSame('http://localhost/groups/3?tab=profile', $response->getTargetUrl());
    }

    public function test_it_uses_referer_tab_and_replaces_existing_tab(): void
    {
        $request = Request::create('http://localhost/cprofile/1', 'PUT');
        $request->headers->set('referer', 'http://localhost/groups/3?tab=profile');

        $response = (new PreserveActiveTab())->handle(
            $request,
            fn () => new RedirectResponse('http://localhost/groups/3?tab=group#content')
        );

        $this->assertSame(
            'http://localhost/groups/3?tab=profile#content',
            $response->getTargetUrl()
        );
    }

    public function test_it_rejects_invalid_tab_and_does_not_modify_external_redirect(): void
    {
        $middleware = new PreserveActiveTab();

        $invalidRequest = Request::create(
            'http://localhost/groups/3',
            'POST',
            ['_active_tab' => 'profile&next=https://example.com']
        );
        $invalidResponse = $middleware->handle(
            $invalidRequest,
            fn () => new RedirectResponse('http://localhost/groups/3')
        );
        $this->assertSame('http://localhost/groups/3', $invalidResponse->getTargetUrl());

        $externalRequest = Request::create(
            'http://localhost/groups/3',
            'POST',
            ['_active_tab' => 'profile']
        );
        $externalResponse = $middleware->handle(
            $externalRequest,
            fn () => new RedirectResponse('https://example.com/done')
        );
        $this->assertSame('https://example.com/done', $externalResponse->getTargetUrl());
    }
}
