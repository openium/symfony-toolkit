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

    public function testPrepareUploadPathWithoutFile()
    {
        $entity = new EntityWithUpload();
        $fileUploaderService = new FileUploaderService('/tmp', '/tmp/test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->prepareUploadPath($entity);
        $this->assertEquals($entity, $result);
    }

    public function testPrepareUploadPathWithFile()
    {
        $this->file->expects($this->once())
            ->method('guessClientExtension')
            ->will($this->returnValue('png'));
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);
        $fileUploaderService = new FileUploaderService('/tmp', '/tmp/test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->prepareUploadPath($entity, "somename");
        $this->assertEquals($result->getImagePath(), "/tmp/test/withUpload/somename.png");
    }

    public function testPrepareUploadPathWithFileWithoutname()
    {
        $this->file->expects($this->once())
            ->method('guessClientExtension')
            ->will($this->returnValue('png'));
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);
        $fileUploaderService = new FileUploaderService('/tmp', '/tmp/test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->prepareUploadPath($entity);
        $this->assertRegExp('/(\/tmp\/test\/withUpload\/).{32}\.png/', $result->getImagePath());
    }

    public function testUploadWithoutFile()
    {
        $entity = new EntityWithUpload();
        $fileUploaderService = new FileUploaderService('/tmp', '/tmp/test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $result = $fileUploaderService->upload($entity);
        $this->assertEquals($entity, $result);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Entity must be pre-uploaded
     */
    public function testUploadWithFileButNotPreuploaded()
    {
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);
        $fileUploaderService = new FileUploaderService('/tmp', '/tmp/test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $fileUploaderService->upload($entity);
    }

    public function testUploadWithFilePreuploaded()
    {
        $this->file->expects($this->once())
            ->method('guessClientExtension')
            ->will($this->returnValue('png'));
        $entity = new EntityWithUpload();
        $entity->setFile($this->file);
        $fileUploaderService = new FileUploaderService('/tmp', '/tmp/test');
        $this->assertTrue($fileUploaderService instanceof FileUploaderService);
        $entity = $fileUploaderService->prepareUploadPath($entity);
        $entity = $fileUploaderService->upload($entity);
        $this->assertNull($entity->getFile());
    }
}
