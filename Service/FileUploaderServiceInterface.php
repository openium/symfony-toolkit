<?php

namespace Openium\SymfonyToolKitBundle\Service;

use Openium\SymfonyToolKitBundle\Entity\MultiUploadInterface;
use Openium\SymfonyToolKitBundle\Entity\WithUploadInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Interface FileUploaderServiceInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 */
interface FileUploaderServiceInterface
{
    /**
     * Prepare upload path for file
     */
    public function prepareUploadPath(
        WithUploadInterface $withUpload,
        ?string $imageName = null
    ): WithUploadInterface;

    /**
     * Additive equivalent of prepareUploadPath() for entities with more than one upload
     * property, implementing MultiUploadInterface instead of WithUploadInterface.
     */
    public function prepareMultiUploadPath(
        MultiUploadInterface $withUpload,
        string $field,
        ?string $imageName = null
    ): MultiUploadInterface;

    /**
     * Return the path to save file
     */
    public function getPath(File $file, string $dirName): string;

    /**
     * Upload File of the Entity
     */
    public function uploadEntity(WithUploadInterface $withUpload): WithUploadInterface;

    /**
     * Additive equivalent of uploadEntity() for entities implementing MultiUploadInterface.
     */
    public function uploadMultiEntity(MultiUploadInterface $withUpload, string $field): MultiUploadInterface;

    /**
     * removeUpload
     */
    public function removeUpload(WithUploadInterface $withUpload): void;

    /**
     * Additive equivalent of removeUpload() for entities implementing MultiUploadInterface.
     */
    public function removeMultiUpload(MultiUploadInterface $withUpload, string $field): void;

    /**
     * Upload File in the path
     *
     * @throws ConflictHttpException
     */
    public function upload(File $file, string $path): void;

    /**
     * removeFile
     */
    public function removeFile(string $path): void;
}
