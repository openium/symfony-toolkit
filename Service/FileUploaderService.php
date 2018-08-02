<?php

/**
 * FileUploaderService
 *
 * PHP Version 7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Thomas LEDUC <thomaslmoi15@hotmail.fr>, Alexandre Caillot <a.caillot@openium.fr>
 * @link     https://openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Openium\SymfonyToolKitBundle\Entity\WithUploadInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class FileUploaderService
 *
 * @package Openium\SymfonyToolKitBundle\Service\File
 */
class FileUploaderService implements FileUploaderServiceInterface
{
    /** @var string */
    protected $publicDirPath;

    /** @var string */
    protected $uploadDirPath;

    /**
     * ThumbnailFileUploaderService constructor.
     *
     * @param string $publicDir
     * @param string $uploadDirName
     */
    public function __construct(string $publicDir, string $uploadDirName)
    {
        $this->publicDirPath = $publicDir;
        $this->uploadDirPath = "/{$uploadDirName}";
    }

    /**
     * Prepare upload path for file
     *
     * @param WithUploadInterface $uploadEntity
     * @param mixed $imageName
     *
     * @return WithUploadInterface
     */
    public function prepareUploadPath(WithUploadInterface $uploadEntity, $imageName = null): WithUploadInterface
    {
        if (is_null($uploadEntity->getFile())) {
            return $uploadEntity;
        }

        if (!is_null($uploadEntity->getImagePath())) {
            $this->removeEntity($uploadEntity);
        }

        /** @var UploadedFile $file */
        $file = $uploadEntity->getFile();

        $path = $this->getPath($file, $uploadEntity->getUploadsDir(), $imageName);
        $uploadEntity->setImagePath($path);

        return $uploadEntity;
    }

    /**
     * Return the path to save file
     *
     * @param UploadedFile $file
     * @param string $dirName
     * @param mixed $imageName
     *
     * @return string
     */
    public function getPath(UploadedFile $file, string $dirName, $imageName = null): string
    {
        // Root Dir
        $filePath = "{$this->uploadDirPath}/{$dirName}";

        // File Name
        if (is_null($imageName)) {
            $randString = sha1(uniqid(mt_rand(), true));
            $fileName = substr($randString, 0, 32);
        } else {
            $fileName = $imageName;
        }

        // File Format
        $fileExtension = strtolower($file->guessClientExtension());

        if (is_null($fileExtension)) {
            throw new HttpException(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, 'The file extension is empty.');
        }

        $fileName .= ".{$fileExtension}";

        // Set path
        $filePath .= DIRECTORY_SEPARATOR . $fileName;

        return $filePath;
    }

    /**
     * Upload File of the Entity
     *
     * @param WithUploadInterface $uploadEntity
     *
     * @return WithUploadInterface
     */
    public function uploadEntity(WithUploadInterface $uploadEntity): WithUploadInterface
    {
        /** @var UploadedFile $file */
        $file = $uploadEntity->getFile();

        // Upload for Icon
        if (!is_null($file)) {
            if (empty($uploadEntity->getImagePath())) {
                throw new \UnexpectedValueException("Call prepareUploadPath method on the entity before upload.");
            }

            $this->upload($file, $uploadEntity->getImagePath());
            $uploadEntity->setFile(null);
        }

        return $uploadEntity;
    }

    /**
     * Remove File of the Entity
     *
     * @param WithUploadInterface $uploadEntity
     */
    public function removeEntity(WithUploadInterface $uploadEntity)
    {
        $path = $uploadEntity->getImagePath();
        $this->remove($path);
    }

    /**
     * Upload File in the path
     *
     * @param File $file
     * @param string $path
     *
     * @throws ConflictHttpException
     */
    public function upload(File $file, string $path)
    {
        $uploadPath = explode('/', $path);
        $fileName = array_pop($uploadPath);

        $folder = $this->publicDirPath . implode(DIRECTORY_SEPARATOR, $uploadPath);
        try {
            $file->move($folder, $fileName);
        } catch (FileException $e) {
            throw new ConflictHttpException($e->getMessage(), $e, $e->getCode());
        }
    }

    /**
     * Remove File
     *
     * @param null|string $path
     */
    public function remove(?string $path)
    {
        $file = $this->publicDirPath . $path;

        if (!is_null($path) && file_exists($file)) {
            unlink($file);
        }
    }
}
