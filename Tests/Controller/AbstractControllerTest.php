<?php

namespace Openium\SymfonyToolKitBundle\Tests\Controller;

use Openium\SymfonyToolKitBundle\Controller\AbstractController;
use Openium\SymfonyToolKitBundle\Exception\InvalidContentFormatException;
use Openium\SymfonyToolKitBundle\Exception\MissingContentException;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\Controller\TestController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractControllerTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Controller
 */
class AbstractControllerTest extends TestCase
{
    public function testAbstractController(): void
    {
        $controller = new TestController();
        self::assertTrue($controller instanceof AbstractController);
        $contentArray = ["content" => 8];
        $content = json_encode(["content" => 8]);
        $request =  new Request(['query' => "5"], ['request' => 6], [], [], [], ["server" => 7], $content);
        $result = $controller->test($request);
        self::assertTrue(is_array($result));
        self::assertEquals($contentArray, $result);
    }

    public function testAbstractControllerWithEmptyContent(): void
    {
        static::expectException(MissingContentException::class);
        static::expectExceptionMessage("Missing content");
        $controller = new TestController();
        self::assertInstanceOf(AbstractController::class, $controller);
        $request =  new Request(['query' => "5"], ['request' => 6], [], [], [], ["server" => 7]);
        $controller->test($request);
    }

    public function testAbstractControllerWithNonArrayContent(): void
    {
        static::expectException(InvalidContentFormatException::class);
        static::expectExceptionMessage("Incorrect content format");
        $controller = new TestController();
        self::assertInstanceOf(AbstractController::class, $controller);
        $request =  new Request(['query' => "5"], ['request' => 6], [], [], [], ["server" => 7], 8);
        $controller->test($request);
    }
}
