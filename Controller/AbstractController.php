<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Controller
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Controller;

use Openium\SymfonyToolKitBundle\Exception\InvalidContentFormatException;
use Openium\SymfonyToolKitBundle\Exception\MissingContentException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class AbstractController
 *
 * @package Openium\SymfonyToolKitBundle\Controller
 */
class AbstractController extends Controller
{
    /**
     * getContentFromRequest
     *
     * @param Request $request
     *
     * @throws \LogicException
     * @throws BadRequestHttpException
     *
     * @return array
     */
    protected function getContentFromRequest(Request $request): array
    {
        $content = json_decode($request->getContent(), true);
        if (empty($content)) {
            throw new MissingContentException();
        }
        if (!is_array($content)) {
            throw new InvalidContentFormatException();
        }
        return $content;
    }
}
