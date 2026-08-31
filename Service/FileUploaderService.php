<?php

namespace Openium\SymfonyToolKitBundle\Service;

use LogicException;
use Openium\SymfonyToolKitBundle\Entity\MultiUploadInterface;
use Openium\SymfonyToolKitBundle\Entity\WithUploadInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    public function __construct(
        protected string $publicDirPath,
        protected string $uploadDirName,
        private readonly UploadFilenameGeneratorInterface $filenameGenerator = new RandomUploadFilenameGenerator()
    ) {
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
     * Additive equivalent of prepareUploadPath() for entities with more than one upload
     * property, implementing MultiUploadInterface instead of WithUploadInterface.
     *
     * @throws BadRequestHttpException
     * @throws LogicException
     */
    public function prepareMultiUploadPath(
        MultiUploadInterface $withUpload,
        string $field,
        ?string $imageName = null
    ): MultiUploadInterface {
        $file = $withUpload->getFile($field);
        if (is_null($file)) {
            return $withUpload;
        }

        if ($withUpload->getImagePath($field) !== null) {
            $this->removeMultiUpload($withUpload, $field);
        }

        $path = $this->getPath($file, $withUpload->getUploadsDir($field), $imageName);
        $withUpload->setImagePath($field, $path);
        return $withUpload;
    }

    /**
     * getPath
     *
     * @throws BadRequestHttpException
     */
    #[\Override]
    public function getPath(File $file, string $dirName, ?string $imageName = null): string
    {
        return sprintf(
            '%s/%s/%s',
            $this->uploadDirName,
            $dirName,
            $this->filenameGenerator->generate($file, $imageName)
        );
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
     * Additive equivalent of uploadEntity() for entities implementing MultiUploadInterface.
     *
     * @throws ConflictHttpException
     * @throws UnexpectedValueException
     */
    public function uploadMultiEntity(MultiUploadInterface $withUpload, string $field): MultiUploadInterface
    {
        /** @var UploadedFile|null $file */
        $file = $withUpload->getFile($field);
        if (!is_null($file)) {
            if (is_null($withUpload->getImagePath($field))) {
                throw new UnexpectedValueException(
                    "Call prepareMultiUploadPath method on the entity before upload."
                );
            }

            $this->upload($file, $withUpload->getImagePath($field));
            $withUpload->setFile($field, null);
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
     * Additive equivalent of removeUpload() for entities implementing MultiUploadInterface.
     */
    public function removeMultiUpload(MultiUploadInterface $withUpload, string $field): void
    {
        $path = $withUpload->getImagePath($field);
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
