<?php

namespace Openium\SymfonyToolKitBundle\Controller;

use InvalidArgumentException;
use JsonException;
use Openium\SymfonyToolKitBundle\Exception\InvalidContentFormatException;
use Openium\SymfonyToolKitBundle\Exception\MissingContentException;
use Openium\SymfonyToolKitBundle\Utils\FilterParameters;
use Openium\SymfonyToolKitBundle\Utils\FilterUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
        return $this->extractObjectFromString($request->getContent());
    }

    /**
     * getMultipartContent
     *
     *
     * @throws BadRequestException
     * @throws InvalidContentFormatException
     * @throws JsonException
     * @throws MissingContentException
     * @throws InvalidArgumentException
     * @return array<string, mixed>|array<int, mixed>
     */
    protected function getMultipartContent(Request $request, string $key = 'json'): array
    {
        if (!$request->request->has($key)) {
            throw new MissingContentException();
        }

        return $this->extractObjectFromString($request->request->get($key));
    }

    /**
     * extractObjectFromString
     *
     * @param string $json
     *
     * @throws InvalidContentFormatException
     * @throws MissingContentException
     * @return array<string, mixed>|array<int, mixed>
     */
    protected function extractObjectFromString(?string $json): array
    {
        try {
            $content = json_decode((string) $json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new MissingContentException();
        }

        if (!is_array($content)) {
            throw new InvalidContentFormatException();
        }

        return $content;
    }

    /**
     * getFilterParameters
     *
     *
     */
    protected function getFilterParameters(Request $request): FilterParameters
    {
        return FilterUtils::generateFromRequest($request);
    }
}
