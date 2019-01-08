<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\Fixtures\Controller
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\Controller;

use Openium\SymfonyToolKitBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request
     *
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return array
     */
    public function test(Request $request): array
    {
        return $this->getContentFromRequest($request);
    }
}
