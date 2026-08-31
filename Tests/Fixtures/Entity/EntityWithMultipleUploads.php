<?php

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\Entity;

use Openium\SymfonyToolKitBundle\Entity\MultiUploadInterface;
use Openium\SymfonyToolKitBundle\Entity\MultiUploadTrait;

/**
 * Class EntityWithMultipleUploads
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Fixtures\Entity
 */
class EntityWithMultipleUploads implements MultiUploadInterface
{
    use MultiUploadTrait;

    #[\Override]
    public function getUploadsDir(string $field): string
    {
        return 'multiUpload/' . $field;
    }
}
