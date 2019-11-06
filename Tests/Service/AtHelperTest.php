<?php

namespace Openium\SymfonyToolKitBundle\Test\Service;

use Openium\SymfonyToolKitBundle\Service\AtHelper;
use Openium\SymfonyToolKitBundle\Service\AtHelperInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class AtHelperTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 *
 * @codeCoverageIgnore
 */
class AtHelperTest extends TestCase
{
    private $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->logger->expects(static::any())
            ->method("debug")
            ->will(
                static::returnCallback(
                    function ($subject) {
                        error_log($subject);
                    }
                )
            );
        parent::setUp();
    }

    public function testFormatTimestampForAt()
    {
        $atHelper = new AtHelper($this->logger);
        $this->assertTrue($atHelper instanceof AtHelperInterface);
        $result = $atHelper->formatTimestampForAt(1514761200);
        $this->assertEquals('11:00 PM December 31 2017', $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage timestamp < 0
     */
    public function testFormatTimestampForAtWhitNegativeTimestamp()
    {
        $atHelper = new AtHelper($this->logger);
        $this->assertTrue($atHelper instanceof AtHelperInterface);
        $atHelper->formatTimestampForAt(-654987);
    }

    public function testCreateAtCommand()
    {
        // given
        $atHelper = new AtHelper($this->logger);
        $result = "";
        // when
        $output = $atHelper->createAtCommand("echo coucou", time() + 33600, $result);
        $atNumber = $atHelper->extractJobNumberFromAtOutput($output);
        // then
        static::assertEquals(3, strlen($atNumber));
        static::assertEquals("0", $result);
        // when
        $removeResult = $atHelper->removeAtCommand($atNumber);
        static::assertTrue($removeResult);
    }
}
