<?php

/**
 * FileUploaderServiceInterface
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
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Interface FileUploaderServiceInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 */
interface FileUploaderServiceInterface
{
    /**
     * Prepare the uploaf path for the file
     *
     * @param WithUploadInterface $withUploadEntity
     *
     * @return WithUploadInterface
     */
    public function prepareUploadPath(WithUploadInterface $withUploadEntity): WithUploadInterface;

    /**
     * Upload the file
     *
     * @param WithUploadInterface $uploadEntity
     *
     * @throws FileException
     *
     * @return WithUploadInterface
     */
    public function upload(WithUploadInterface $uploadEntity): WithUploadInterface;

    /**
     * Remove the file
     *
     * @param WithUploadInterface $uploadEntity
     */
    public function remove(WithUploadInterface $uploadEntity);
}
