<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\Controller
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\Controller;

use Openium\SymfonyToolKitBundle\Controller\AbstractController;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\Controller\TestController;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractControllerTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Controller
 */
class AbstractControllerTest extends TestCase
{
    public function testAbstractController()
    {
        $controller = new TestController();
        $this->assertTrue($controller instanceof AbstractController);
        $contentArray = ["content" => 8];
        $content = json_encode(["content" => 8]);
        $request =  new Request(['query' => "5"], ['request' => 6], [], [], [], ["server" => 7], $content);
        $result = $controller->test($request);
        $this->assertTrue(is_array($result));
        $this->assertEquals($contentArray, $result);
    }

    /**
     * @expectedException  Openium\SymfonyToolKitBundle\Exception\MissingContentException
     * @expectedExceptionMessage Missing content
     */
    public function testAbstractControllerWithEmptyContent()
    {
        $controller = new TestController();
        $this->assertTrue($controller instanceof AbstractController);
        $request =  new Request(['query' => "5"], ['request' => 6], [], [], [], ["server" => 7]);
        $controller->test($request);
    }
    /**
     * @expectedException  Openium\SymfonyToolKitBundle\Exception\InvalidContentFormatException
     * @expectedExceptionMessage Incorrect content format
     */
    public function testAbstractControllerWithNonArrayContent()
    {
        $controller = new TestController();
        $this->assertTrue($controller instanceof AbstractController);
        $request =  new Request(['query' => "5"], ['request' => 6], [], [], [], ["server" => 7], 8);
        $controller->test($request);
    }
}
