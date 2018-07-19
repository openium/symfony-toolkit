<?php

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\Entity;

use Openium\SymfonyToolKitBundle\Entity\WithUploadInterface;
use Openium\SymfonyToolKitBundle\Entity\WithUploadTrait;

/**
 * Class EntityWithUpload
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Fixtures\Entity
 */
class EntityWithUpload implements WithUploadInterface
{
    use WithUploadTrait;

    public function getUploadsDir(): string
    {
        return 'withUpload';
    }
}