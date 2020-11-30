<?php

/**
 * PHP Version >=7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\EventListener;

use Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener\TestAuthenticationFailureListener;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class AuthenticationFailureListenerTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\EventListener
 */
class AuthenticationFailureListenerTest extends TestCase
{
    private $logger;

    public function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testIsEnable()
    {
        $listener = new TestAuthenticationFailureListener(true, $this->logger);
        $this->assertTrue($listener->getEnable());
        $listener = new TestAuthenticationFailureListener(false, $this->logger);
        $this->assertFalse($listener->getEnable());
    }

    public function testOnSymfonyAuthenticationFailure()
    {
        $authenticationException = new AuthenticationException('TestMessage');
        $token = $this->createMock(AnonymousToken::class);
        $event = new AuthenticationFailureEvent($token, $authenticationException);
        $listener = new TestAuthenticationFailureListener(true, $this->logger);
        try {
            $listener->onSymfonyAuthenticationFailure($event);
            $this->fail('Method must throw an exception');
        } catch (HttpException $e) {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $e->getStatusCode());
            $this->assertEquals('TestMessage', $e->getMessage());
        }
    }
}
