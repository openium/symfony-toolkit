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
    protected MockObject&UploadedFile $file;

    protected function setUp(): void
    {
        $this->file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    /**
     * Test prepareUploadPath method without file. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithoutFile(): void
    {
        $entity = new EntityWithUpload();
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->prepareUploadPath($entity);
        self::assertEquals($entity, $result);
    }

    /**
     * Test prepareUploadPath method with file. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFile(): void
    {
        $this->file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn('png');
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->prepareUploadPath($entity, "somename");
        self::assertEquals("test/withUpload/somename.png", $result->getImagePath());
    }

    /**
     * Test prepareUploadPath method with file without extension. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFileWithoutExtension(): void
    {
        static::expectException(BadRequestHttpException::class);
        static::expectExceptionMessage("The file extension is empty.");
        $this->file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn(null);
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $fileUploaderService->prepareUploadPath($entity, "somename");
    }

    /**
     * Test prepareUploadPath method with file and without name. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFileWithoutName(): void
    {
        $this->file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn('png');
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->prepareUploadPath($entity);
        self::assertMatchesRegularExpression('/(test\/withUpload\/).{32}\.png/', $result->getImagePath());
    }

    /**
     * Test uploadEntity method without file.
     */
    public function testUploadEntityWithoutFile(): void
    {
        $entity = new EntityWithUpload();
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->uploadEntity($entity);
        self::assertEquals($entity, $result);
    }

    /**
     * Test uploadEntity method with file without having configured the upload path (prepareUploadPath method).
     */
    public function testUploadEntityWithFileButNotPreUploaded(): void
    {
        static::expectException("UnexpectedValueException");
        static::expectExceptionMessage("Call prepareUploadPath method on the entity before upload.");
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $fileUploaderService->uploadEntity($entity);
    }

    /**
     * Test uploadEntity method with file and the upload path configured (prepareUploadPath method).
     */
    public function testUploadWithFilePreuploaded(): void
    {
        // Make File
        $this->file->expects(self::once())
            ->method('guessClientExtension')
            ->willReturn('png');
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);

        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        self::assertTrue($fileUploaderService instanceof FileUploaderService);
        $entity = $fileUploaderService->prepareUploadPath($entity);
        $entity = $fileUploaderService->uploadEntity($entity);
        self::assertNull($entity->getFile());
    }
}
