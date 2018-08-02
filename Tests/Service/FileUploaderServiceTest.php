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
        // Make Entity
        $entity = new EntityWithUpload();

        // Get Service
        $fileUploaderService = new FileUploaderService('/tmp', '/tmp/test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);

        // Get Path
        $result = $fileUploaderService->prepareUploadPath($entity);
        $this->assertEquals($entity, $result);
    }

    /**
     * Test prepareUploadPath method with file. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFile()
    {
        // Make File
        $this->file->expects($this->once())
            ->method('guessClientExtension')
            ->will($this->returnValue('png'));

        // Make Entity
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);

        // Get Service
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);

        // Get Path
        $result = $fileUploaderService->prepareUploadPath($entity, "somename");
        $this->assertEquals("/test/withUpload/somename.png", $result->getImagePath());
    }

    /**
     * Test prepareUploadPath method with file and without name. Prepare image path in the entity
     */
    public function testPrepareUploadPathWithFileWithoutName()
    {
        // Make File
        $this->file->expects($this->once())
            ->method('guessClientExtension')
            ->will($this->returnValue('png'));

        // Make Entity
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);

        // Get Service
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);

        // Get Path
        $result = $fileUploaderService->prepareUploadPath($entity);
        $this->assertRegExp('/(\/test\/withUpload\/).{32}\.png/', $result->getImagePath());
    }

    /**
     * Test uploadEntity method without file.
     */
    public function testUploadEntityWithoutFile()
    {
        // Make Entity
        $entity = new EntityWithUpload();

        // Get Service
        $fileUploaderService = new FileUploaderService('/tmp', 'test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);

        // Upload
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
        // Make Entity
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);

        // Get Service
        $fileUploaderService = new FileUploaderService('/tmp', '/tmp/test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);

        // Upload
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

        // Make Entity
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);

        // Get Service
        $fileUploaderService = new FileUploaderService('/tmp', '/tmp/test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);

        // Prepare Path
        $entity = $fileUploaderService->prepareUploadPath($entity);

        // Upload
        $entity = $fileUploaderService->uploadEntity($entity);
        $this->assertNull($entity->getFile());
    }
}
