<?php

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
    #[\Override]
    public function prepareUploadPath(
        WithUploadInterface $withUpload,
        ?string $imageName = null
    ): WithUploadInterface {
        $file = $withUpload->getFile();
        if (is_null($file)) {
            return $withUpload;
        }

        if ($withUpload->getImagePath() !== null) {
            $this->removeUpload($withUpload);
        }

        $path = $this->getPath($file, $withUpload->getUploadsDir(), $imageName);
        $withUpload->setImagePath($path);
        return $withUpload;
    }

    /**
     * getPath
     *
     * @throws BadRequestHttpException
     * @throws LogicException
     */
    #[\Override]
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
    #[\Override]
    public function uploadEntity(WithUploadInterface $withUpload): WithUploadInterface
    {
        /** @var UploadedFile|null $file */
        $file = $withUpload->getFile();
        if (!is_null($file)) {
            if (is_null($withUpload->getImagePath())) {
                throw new UnexpectedValueException(
                    "Call prepareUploadPath method on the entity before upload."
                );
            }

            $this->upload($file, $withUpload->getImagePath());
            $withUpload->setFile(null);
        }

        return $withUpload;
    }

    /**
     * removeUpload
     */
    #[\Override]
    public function removeUpload(WithUploadInterface $withUpload): void
    {
        $path = $withUpload->getImagePath();
        if (!is_null($path)) {
            $this->removeFile($path);
        }
    }

    /**
     * upload
     *
     * @throws ConflictHttpException
     */
    #[\Override]
    public function upload(File $file, string $path): void
    {
        $uploadPath = explode('/', $path);
        $fileName = array_pop($uploadPath);
        $folder = sprintf(
            "%s%s%s",
            $this->publicDirPath,
            DIRECTORY_SEPARATOR,
            implode(DIRECTORY_SEPARATOR, $uploadPath)
        );
        try {
            $file->move($folder, $fileName);
        } catch (FileException $fileException) {
            throw new ConflictHttpException($fileException->getMessage(), $fileException, $fileException->getCode());
        }
    }

    /**
     * removeFile
     */
    #[\Override]
    public function removeFile(string $path): void
    {
        $file = $this->publicDirPath . DIRECTORY_SEPARATOR . $path;
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
