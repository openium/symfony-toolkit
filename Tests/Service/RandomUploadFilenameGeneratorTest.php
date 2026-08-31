<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Service\RandomUploadFilenameGenerator;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class RandomUploadFilenameGeneratorTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Service
 */
#[CoversNothing]
class RandomUploadFilenameGeneratorTest extends TestCase
{
    public function testGenerateWithImageNameUsesGivenName(): void
    {
        // given
        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn('png');
        $generator = new RandomUploadFilenameGenerator();
        // when
        $result = $generator->generate($file, 'somename');
        // then
        self::assertEquals('somename.png', $result);
    }

    public function testGenerateWithoutImageNameGeneratesRandomName(): void
    {
        // given
        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn('png');
        $generator = new RandomUploadFilenameGenerator();
        // when
        $result = $generator->generate($file);
        // then
        self::assertMatchesRegularExpression('/^.{32}\.png$/', $result);
    }

    public function testGenerateWithoutExtensionThrows(): void
    {
        // given
        static::expectException(BadRequestHttpException::class);
        static::expectExceptionMessage('The file extension is empty.');
        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn(null);
        $generator = new RandomUploadFilenameGenerator();
        // when
        $generator->generate($file, 'somename');
    }

    public function testGenerateWithPlainFileUsesFilenameExtension(): void
    {
        // given
        $tmpFile = tempnam(sys_get_temp_dir(), 'toolkit');
        self::assertIsString($tmpFile);
        $withExtension = $tmpFile . '.jpg';
        rename($tmpFile, $withExtension);
        $file = new File($withExtension);
        $generator = new RandomUploadFilenameGenerator();
        // when
        $result = $generator->generate($file, 'somename');
        // then
        self::assertEquals('somename.jpg', $result);
        unlink($withExtension);
    }
}
