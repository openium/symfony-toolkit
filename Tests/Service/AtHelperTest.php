<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Service\AtHelper;
use Openium\SymfonyToolKitBundle\Service\AtHelperInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class AtHelperTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 * @codeCoverageIgnore
 */
class AtHelperTest extends TestCase
{
    private MockObject&LoggerInterface $logger;

    public function setUp(): void
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

    public function testFormatTimestampForAt(): void
    {
        $atHelper = new AtHelper($this->logger);
        self::assertTrue($atHelper instanceof AtHelperInterface);
        $result = $atHelper->formatTimestampForAt(1_514_761_200);
        self::assertEquals('12:00 AM January 1 2018', $result);
    }

    public function testFormatTimestampForAtWhitNegativeTimestamp(): void
    {
        static::expectException("InvalidArgumentException");
        static::expectExceptionMessage("timestamp < 0");
        $atHelper = new AtHelper($this->logger);
        self::assertTrue($atHelper instanceof AtHelperInterface);
        $atHelper->formatTimestampForAt(-654987);
    }

    public function testCreateAtCommand(): void
    {
        // given
        $atHelper = new AtHelper($this->logger);
        $result = -1;
        // when
        $output = $atHelper->createAtCommand("echo coucou", time() + 33600, $result);
        $atNumber = $atHelper->extractJobNumberFromAtOutput($output);
        // then
        static::assertEquals(3, strlen($atNumber));
        static::assertEquals(0, $result);
        // when
        $removeResult = $atHelper->removeAtCommand($atNumber);
        static::assertTrue($removeResult);
    }

    public function testCreateAtCommandFromPath(): void
    {
        // given
        $atHelper = new AtHelper($this->logger);
        $result = -1;
        // when
        $output = $atHelper->createAtCommandFromPath("echo coucou", time() + 33600, __DIR__, $result);
        $atNumber = $atHelper->extractJobNumberFromAtOutput($output);
        // then
        static::assertEquals(3, strlen($atNumber));
        static::assertEquals(0, $result);
        // when
        $removeResult = $atHelper->removeAtCommand($atNumber);
        static::assertTrue($removeResult);
    }

    public function testExtractJobNumberFromAtOutputWithRightOutput(): void
    {
        // given
        $atHelper = new AtHelper($this->logger);
        $output = 'commands will be executed using /bin/sh job 130 at Wed Nov  6 15:06:00 2019';
        // when
        $jobNumber = $atHelper->extractJobNumberFromAtOutput($output);
        // then
        static::assertEquals("130", $jobNumber);
    }

    public function testExtractJobNumberFromAtOutputWithSmallRightOutput(): void
    {
        // given
        $atHelper = new AtHelper($this->logger);
        $output = 'job 130 at Wed Nov  6 15:06:00 2019';
        // when
        $jobNumber = $atHelper->extractJobNumberFromAtOutput($output);
        // then
        static::assertEquals("130", $jobNumber);
    }

    public function testExtractJobNumberFromAtOutputWithOutputSpecialChar(): void
    {
        // given
        $atHelper = new AtHelper($this->logger);
        $output = "commands will be executed using /bin/sh\njob 130 at Wed Nov  6 15:06:00 2019";
        // when
        $jobNumber = $atHelper->extractJobNumberFromAtOutput($output);
        // then
        static::assertEquals("130", $jobNumber);
    }

    public function testExtractJobNumberFromAtOutputWithWrongOutput(): void
    {
        // given
        $atHelper = new AtHelper($this->logger);
        $output = ' at Wed Nov  6 15:06:00 2019';
        // when
        $jobNumber = $atHelper->extractJobNumberFromAtOutput($output);
        // then
        static::assertNull($jobNumber);
    }

    public function testExtractJobNumberFromAtOutputWithEmptyOutput(): void
    {
        // given
        $atHelper = new AtHelper($this->logger);
        $output = '';
        // when
        $jobNumber = $atHelper->extractJobNumberFromAtOutput($output);
        // then
        static::assertNull($jobNumber);
    }

    public function testExtractJobNumberFromAtOutputPastMessageOutput(): void
    {
        // given
        $atHelper = new AtHelper($this->logger);
        $output = 'at: refusing to create job destined in the past';
        // when
        $jobNumber = $atHelper->extractJobNumberFromAtOutput($output);
        // then
        static::assertNull($jobNumber);
    }
}
