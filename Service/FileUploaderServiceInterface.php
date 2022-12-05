<?php

/**
 * FileUploaderServiceInterface
 *
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

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
     *
     * @param WithUploadInterface $uploadEntity
     * @param string|null $imageName
     *
     * @return WithUploadInterface
     */
    public function prepareUploadPath(WithUploadInterface $uploadEntity, ?string $imageName = null): WithUploadInterface;

    /**
     * Return the path to save file
     *
     * @param File|UploadedFile $file
     * @param string $dirName
     *
     * @return string
     */
    public function getPath(File $file, string $dirName): string;

    /**
     * Upload File of the Entity
     *
     * @param WithUploadInterface $uploadEntity
     *
     * @return WithUploadInterface
     */
    public function uploadEntity(WithUploadInterface $uploadEntity): WithUploadInterface;

    /**
     * removeUpload
     *
     * @param WithUploadInterface $uploadEntity
     *
     * @return void
     */
    public function removeUpload(WithUploadInterface $uploadEntity): void;

    /**
     * Upload File in the path
     *
     * @param File $file
     * @param string $path
     *
     * @throws ConflictHttpException
     */
    public function upload(File $file, string $path): void;

    /**
     * removeFile
     *
     * @param string $path
     *
     * @return void
     */
    public function removeFile(string $path): void;
}
