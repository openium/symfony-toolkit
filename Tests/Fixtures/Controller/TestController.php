<?php
/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\Fixtures\Controller
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

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
