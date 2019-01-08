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
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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
}
