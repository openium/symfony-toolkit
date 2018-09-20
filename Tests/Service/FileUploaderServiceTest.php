<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Service\FileUploaderService;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\Entity\EntityWithUpload;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploaderServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Service
 *
 * @codeCoverageIgnore
 */
class FileUploaderServiceTest extends TestCase
{
    protected $file;

    public function setUp()
    {
        $this->file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    /**
     * Test prepareUploadPath method without file. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithoutFile()
    {
        $entity = new EntityWithUpload();
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->prepareUploadPath($entity);
        $this->assertEquals($entity, $result);
    }

    /**
     * Test prepareUploadPath method with file. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFile()
    {
        $this->file->expects($this->once())
            ->method('guessClientExtension')
            ->will($this->returnValue('png'));
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->prepareUploadPath($entity, "somename");
        $this->assertEquals("test/withUpload/somename.png", $result->getImagePath());
    }

    /**
     * Test prepareUploadPath method with file without extension. Prepare image path in the entity
     *
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage The file extension is empty.
     */
    public function testPrepareUploadPathWithFileWithoutExtension()
    {
        $this->file->expects($this->once())
            ->method('guessClientExtension')
            ->will($this->returnValue(null));
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $fileUploaderService->prepareUploadPath($entity, "somename");
    }

    /**
     * Test prepareUploadPath method with file and without name. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFileWithoutName()
    {
        $this->file->expects($this->once())
            ->method('guessClientExtension')
            ->will($this->returnValue('png'));
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->prepareUploadPath($entity);
        $this->assertRegExp('/(test\/withUpload\/).{32}\.png/', $result->getImagePath());
    }

    /**
     * Test uploadEntity method without file.
     */
    public function testUploadEntityWithoutFile()
    {
        $entity = new EntityWithUpload();
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->uploadEntity($entity);
        $this->assertEquals($entity, $result);
    }

    /**
     * Test uploadEntity method with file without having configured the upload path (prepareUploadPath method).
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Call prepareUploadPath method on the entity before upload.
     */
    public function testUploadEntityWithFileButNotPreUploaded()
    {
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $fileUploaderService->uploadEntity($entity);
    }

    /**
     * Test uploadEntity method with file and the upload path configured (prepareUploadPath method).
     */
    public function testUploadWithFilePreuploaded()
    {
        // Make File
        $this->file->expects($this->once())
            ->method('guessClientExtension')
            ->will($this->returnValue('png'));
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $entity = $fileUploaderService->prepareUploadPath($entity);
        $entity = $fileUploaderService->uploadEntity($entity);
        $this->assertNull($entity->getFile());
    }
}
