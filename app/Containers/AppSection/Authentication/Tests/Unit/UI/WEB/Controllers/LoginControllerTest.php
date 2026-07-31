<?php

namespace App\Containers\AppSection\Authentication\Tests\Unit\UI\WEB\Controllers;

use App\Containers\AppSection\Authentication\Tests\UnitTestCase;
use App\Containers\AppSection\Authentication\UI\WEB\Controllers\LoginController;
use Inertia\Response;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LoginController::class)]
final class LoginControllerTest extends UnitTestCase
{
    public function testControllerRendersCorrectInertiaPage(): void
    {
        $controller = app(LoginController::class);

        $response = $controller->showForm();

        $this->assertInstanceOf(Response::class, $response);

        $page = $response->toResponse(request())->getOriginalContent()->getData()['page'];

        $this->assertSame('login', $page['component']);
    }
}
