<?php
/**
 * FileUploaderService
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use LogicException;
use Openium\SymfonyToolKitBundle\Entity\WithUploadInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use UnexpectedValueException;

/**
 * Class FileUploaderService
 *
 * @package Openium\SymfonyToolKitBundle\Service\File
 */
class FileUploaderService implements FileUploaderServiceInterface
{
    protected string $uploadDirPath;

    /**
     * ThumbnailFileUploaderService constructor.
     */
    public function __construct(protected string $publicDirPath, protected string $uploadDirName)
    {
        $this->uploadDirPath = $publicDirPath . DIRECTORY_SEPARATOR . $uploadDirName;
    }

    /**
     * prepareUploadPath
     *
     * @throws BadRequestHttpException
     * @throws LogicException
     */
    public function prepareUploadPath(WithUploadInterface $uploadEntity, ?string $imageName = null): WithUploadInterface
    {
        $file = $uploadEntity->getFile();
        if (is_null($file)) {
            return $uploadEntity;
        }
        if ($uploadEntity->getImagePath() !== null) {
            $this->removeUpload($uploadEntity);
        }
        $path = $this->getPath($file, $uploadEntity->getUploadsDir(), $imageName);
        $uploadEntity->setImagePath($path);
        return $uploadEntity;
    }

    /**
     * getPath
     *
     * @throws BadRequestHttpException
     * @throws LogicException
     */
    public function getPath(File $file, string $dirName, ?string $imageName = null): string
    {
        if (is_null($imageName)) {
            $randString = sha1(uniqid((string)random_int(0, mt_getrandmax()), true));
            $fileName = substr($randString, 0, 32);
        } else {
            $fileName = $imageName;
        }
        $fileExtension = null;
        if ($file instanceof UploadedFile) {
            $fileExtension = strtolower($file->guessClientExtension() ?? '');
        } else {
            $fileNameParts = explode('.', $file->getFilename());
            if (count($fileNameParts) > 1) {
                $fileExtension = trim($fileNameParts[count($fileNameParts) - 1]);
            }
        }
        if (is_null($fileExtension) || $fileExtension === '') {
            throw new BadRequestHttpException(
                'The file extension is empty.',
                null,
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            );
        }
        return sprintf("%s/%s/%s.%s", $this->uploadDirName, $dirName, $fileName, $fileExtension);
    }

    /**
     * uploadEntity
     *
     * @throws ConflictHttpException
     * @throws UnexpectedValueException
     */
    public function uploadEntity(WithUploadInterface $uploadEntity): WithUploadInterface
    {
        /** @var UploadedFile|null $file */
        $file = $uploadEntity->getFile();
        if (!is_null($file)) {
            if (is_null($uploadEntity->getImagePath())) {
                throw new UnexpectedValueException("Call prepareUploadPath method on the entity before upload.");
            }
            $this->upload($file, $uploadEntity->getImagePath());
            $uploadEntity->setFile(null);
        }
        return $uploadEntity;
    }

    /**
     * removeUpload
     */
    public function removeUpload(WithUploadInterface $uploadEntity): void
    {
        $path = $uploadEntity->getImagePath();
        if (!is_null($path)) {
            $this->removeFile($path);
        }
    }

    /**
     * upload
     *
     * @throws ConflictHttpException
     */
    public function upload(File $file, string $path): void
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
     */
    public function removeFile(string $path): void
    {
        $file = $this->publicDirPath . DIRECTORY_SEPARATOR . $path;
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
