<?php

/**
 * FileUploaderService
 *
 * PHP Version >=7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Openium\SymfonyToolKitBundle\Entity\WithUploadInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Class FileUploaderService
 *
 * @package Openium\SymfonyToolKitBundle\Service\File
 */
class FileUploaderService implements FileUploaderServiceInterface
{
    /**
     * @var string
     */
    protected $publicDirPath;

    /**
     * @var string
     */
    protected $uploadDirPath;

    /**
     * @var string
     */
    protected $uploadDirName;

    /**
     * ThumbnailFileUploaderService constructor.
     *
     * @param string $publicDir
     * @param string $uploadDirName
     */
    public function __construct(string $publicDir, string $uploadDirName)
    {
        $this->publicDirPath = $publicDir;
        $this->uploadDirName = $uploadDirName;
        $this->uploadDirPath = $publicDir . DIRECTORY_SEPARATOR . $uploadDirName;
    }

    /**
     * prepareUploadPath
     *
     * @param WithUploadInterface $uploadEntity
     * @param null $imageName
     *
     * @throws BadRequestHttpException
     *
     * @return WithUploadInterface
     */
    public function prepareUploadPath(WithUploadInterface $uploadEntity, $imageName = null): WithUploadInterface
    {
        if (is_null($uploadEntity->getFile())) {
            return $uploadEntity;
        }
        if (!is_null($uploadEntity->getImagePath())) {
            $this->removeUpload($uploadEntity);
        }
        $file = $uploadEntity->getFile();
        $path = $this->getPath($file, $uploadEntity->getUploadsDir(), $imageName);
        $uploadEntity->setImagePath($path);
        return $uploadEntity;
    }

    /**
     * getPath
     *
     * @param File $file
     * @param string $dirName
     * @param null $imageName
     *
     * @throws BadRequestHttpException
     *
     * @return string
     */
    public function getPath(File $file, string $dirName, $imageName = null): string
    {
        if (is_null($imageName)) {
            $randString = sha1(uniqid(mt_rand(), true));
            $fileName = substr($randString, 0, 32);
        } else {
            $fileName = $imageName;
        }
        $fileExtension = null;
        if ($file instanceof UploadedFile) {
            $fileExtension = strtolower($file->guessClientExtension());
        } else {
            $fileNameParts = explode('.', $file->getFilename());
            if (sizeof($fileNameParts) > 0) {
                $fileExtension = $fileNameParts[sizeof($fileNameParts) - 1];
            }
        }
        if (empty($fileExtension)) {
            throw new BadRequestHttpException(
                'The file extension is empty.',
                null,
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            );
        }
        if ($fileExtension !== null) {
            $filePath = sprintf("%s/%s/%s.%s", $this->uploadDirName, $dirName, $fileName, $fileExtension);
        } else {
            $filePath = sprintf("%s/%s/%s", $this->uploadDirName, $dirName, $fileName);
        }
        return $filePath;
    }

    /**
     * uploadEntity
     *
     * @param WithUploadInterface $uploadEntity
     *
     * @throws ConflictHttpException
     * @throws \UnexpectedValueException
     *
     * @return WithUploadInterface
     */
    public function uploadEntity(WithUploadInterface $uploadEntity): WithUploadInterface
    {
        /** @var UploadedFile $file */
        $file = $uploadEntity->getFile();
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
     * removeUpload
     *
     * @param WithUploadInterface $uploadEntity
     *
     * @return void
     */
    public function removeUpload(WithUploadInterface $uploadEntity)
    {
        $path = $uploadEntity->getImagePath();
        if (!empty($path)) {
            $this->removeFile($path);
        }
    }

    /**
     * upload
     *
     * @param File $file
     * @param string $path
     *
     * @throws ConflictHttpException
     *
     * @return void
     */
    public function upload(File $file, string $path)
    {
        $uploadPath = explode('/', $path);
        $fileName = array_pop($uploadPath);
        $folder = $this->publicDirPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $uploadPath);
        try {
            $file->move($folder, $fileName);
        } catch (FileException $e) {
            throw new ConflictHttpException($e->getMessage(), $e, $e->getCode());
        }
    }

    /**
     * removeFile
     *
     * @param string $path
     *
     * @return void
     */
    public function removeFile(string $path)
    {
        if (!empty($path)) {
            $file = $this->publicDirPath . DIRECTORY_SEPARATOR . $path;
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
