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
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * FileUploaderService constructor.
     *
     * @param string $publicDir
     * @param string $uploadDir
     */
    public function __construct(string $publicDir, string $uploadDir)
    {
        $this->publicDirPath = $publicDir;
        $this->uploadDirPath = $uploadDir;
    }

    /**
     * Prepare the uploaf path for the file
     *
     * @param WithUploadInterface $withUploadEntity
     *
     * @return WithUploadInterface
     */
    public function prepareUploadPath(WithUploadInterface $withUploadEntity, $imageName = null): WithUploadInterface
    {
        if (null === $withUploadEntity->getFile()) {
            return $withUploadEntity;
        }
        /** @var UploadedFile $file */
        $file = $withUploadEntity->getFile();
        $format = sprintf('.%s', strtolower($file->guessClientExtension()));
        if (!is_null($withUploadEntity->getImagePath())) {
            $this->remove($withUploadEntity);
        }
        if ($imageName == null) {
            $imageName = substr(sha1(uniqid(mt_rand(), true)), 0, 32) . $format;
        } elseif (strpos($imageName, '.') == false) {
            $imageName .= $format;
        }
        $imagePath = sprintf(
            '%s/%s/%s',
            $this->uploadDirPath,
            $withUploadEntity->getUploadsDir(),
            $imageName
        );
        $withUploadEntity->setImagePath($imagePath);
        return $withUploadEntity;
    }

    /**
     * Upload the file
     *
     * @param WithUploadInterface $uploadEntity
     *
     * @throws FileException
     *
     * @return WithUploadInterface
     */
    public function upload(WithUploadInterface $uploadEntity): WithUploadInterface
    {
        if (!is_null($uploadEntity->getFile())) {
            if (empty($uploadEntity->getImagePath())) {
                throw new \UnexpectedValueException("Entity must be pre-uploaded");
            }
            /** @var UploadedFile $file */
            $file = $uploadEntity->getFile();
            $imagePath = explode(DIRECTORY_SEPARATOR, $uploadEntity->getImagePath());
            $imageName = array_pop($imagePath);
            $folder = $this->publicDirPath . implode(DIRECTORY_SEPARATOR, $imagePath);
            $file->move($folder, $imageName);
            $uploadEntity->setFile(null);
        }
        return $uploadEntity;
    }

    /**
     * Remove the file
     *
     * @param WithUploadInterface $uploadEntity
     *
     * @return void
     */
    public function remove(WithUploadInterface $uploadEntity)
    {
        $removeFile = $this->publicDirPath . $uploadEntity->getImagePath();
        if (!is_null($uploadEntity->getImagePath()) && file_exists($removeFile)) {
            unlink($removeFile);
        }
    }
}
