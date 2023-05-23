<?php
/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Controller
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Controller;

use Openium\SymfonyToolKitBundle\Exception\InvalidContentFormatException;
use Openium\SymfonyToolKitBundle\Exception\MissingContentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class AbstractController
 *
 * @package Openium\SymfonyToolKitBundle\Controller
 */
class AbstractController extends BaseController
{
    /**
     * getContentFromRequest
     *
     * @throws BadRequestHttpException
     * @return array<string, mixed>|array<int, mixed>
     */
    protected function getContentFromRequest(Request $request): array
    {
        /** @var string|resource $bodyContent */
        $bodyContent = $request->getContent();
        if ($bodyContent === null) {
            throw new MissingContentException();
        }
        if (!is_string($bodyContent)) {
            $bodyContent = strval($bodyContent);
        }
        try {
            $content = json_decode($bodyContent, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new MissingContentException();
        }
        if (!is_array($content)) {
            throw new InvalidContentFormatException();
        }
        return $content;
    }
}
