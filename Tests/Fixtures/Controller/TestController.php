<?php

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\Controller;

use Openium\SymfonyToolKitBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class TestController
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Fixtures\Controller
 */
class TestController extends AbstractController
{
    /**
     * test
     *
     * @throws BadRequestHttpException
     */
    public function test(Request $request): array
    {
        return $this->getContentFromRequest($request);
    }
}
