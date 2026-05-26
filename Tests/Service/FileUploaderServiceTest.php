<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Service\FileUploaderService;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\Entity\EntityWithUpload;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class FileUploaderServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Service
 */
#[CoversNothing]
class FileUploaderServiceTest extends TestCase
{
    /**
     * Test prepareUploadPath method without file. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithoutFile(): void
    {
        $entityWithUpload = new EntityWithUpload();
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $withUpload = $fileUploaderService->prepareUploadPath($entityWithUpload);
        self::assertEquals($entityWithUpload, $withUpload);
    }

    /**
     * Test prepareUploadPath method with file. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFile(): void
    {
        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn('png');
        $entityWithUpload = new EntityWithUpload();
        $entityWithUpload->setFile($file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $withUpload = $fileUploaderService->prepareUploadPath($entityWithUpload, "somename");
        self::assertEquals("test/withUpload/somename.png", $withUpload->getImagePath());
    }

    /**
     * Test prepareUploadPath method with file without extension. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFileWithoutExtension(): void
    {
        static::expectException(BadRequestHttpException::class);
        static::expectExceptionMessage("The file extension is empty.");
        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn(null);
        $entityWithUpload = new EntityWithUpload();
        $entityWithUpload->setFile($file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $fileUploaderService->prepareUploadPath($entityWithUpload, "somename");
    }

    /**
     * Test prepareUploadPath method with file and without name. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFileWithoutName(): void
    {
        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn('png');
        $entityWithUpload = new EntityWithUpload();
        $entityWithUpload->setFile($file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $withUpload = $fileUploaderService->prepareUploadPath($entityWithUpload);
        self::assertMatchesRegularExpression('/(test\/withUpload\/).{32}\.png/', $withUpload->getImagePath());
    }

    /**
     * Test uploadEntity method without file.
     */
    public function testUploadEntityWithoutFile(): void
    {
        $entityWithUpload = new EntityWithUpload();
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $withUpload = $fileUploaderService->uploadEntity($entityWithUpload);
        self::assertEquals($entityWithUpload, $withUpload);
    }

    /**
     * Test uploadEntity method with file without having configured the upload path (prepareUploadPath method).
     */
    public function testUploadEntityWithFileButNotPreUploaded(): void
    {
        static::expectException("UnexpectedValueException");
        static::expectExceptionMessage("Call prepareUploadPath method on the entity before upload.");
        $file = $this->createStub(UploadedFile::class);
        $entityWithUpload = new EntityWithUpload();
        $entityWithUpload->setFile($file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $fileUploaderService->uploadEntity($entityWithUpload);
    }

    /**
     * Test uploadEntity method with file and the upload path configured (prepareUploadPath method).
     */
    public function testUploadWithFilePreuploaded(): void
    {
        // Make File
        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn('png');
        $entity = new EntityWithUpload();
        $entity->setFile($file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $entity = $fileUploaderService->prepareUploadPath($entity);
        $entity = $fileUploaderService->uploadEntity($entity);
        self::assertNull($entity->getFile());
    }
}
