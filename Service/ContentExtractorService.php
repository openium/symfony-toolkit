<?php

namespace Openium\SymfonyToolKitBundle\Service;

use DateTimeInterface;
use InvalidArgumentException;
use Openium\SymfonyToolKitBundle\Utils\DateStringUtils;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ContentExtractorService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class ContentExtractorService implements ContentExtractorServiceInterface
{
    protected TranslatorInterface $translator;

    /**
     * ContentUtils constructor.
     *
     * @param TranslatorInterface $TranslatorInterface
     */
    public function __construct(TranslatorInterface $TranslatorInterface)
    {
        $this->translator = $TranslatorInterface;
    }

    /**
     * checkKeyNotEmpty
     *
     * @param array $content
     * @param string $key
     * @param bool|null $nullable
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return void
     */
    public function checkKeyNotEmpty(array $content, string $key, ?bool $nullable = false): void
    {
        if (!array_key_exists($key, $content) || (!$nullable && empty($content[$key]))) {
            throw new BadRequestHttpException(
                $this->translator->trans(
                    'openium_symfony_toolkit.missing_parameters',
                    ['_parameter' => $key],
                    'openium_symfony_toolkit'
                )
            );
        }
    }

    /**
     * checkKeyIsBoolean
     *
     * @param array $content
     * @param string $key
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return void
     */
    public function checkKeyIsBoolean(array $content, string $key): void
    {
        if (!array_key_exists($key, $content) || !is_bool($content[$key])) {
            throw new BadRequestHttpException(
                $this->translator->trans(
                    'openium_symfony_toolkit.boolean_property',
                    ['_parameter' => $key],
                    'openium_symfony_toolkit'
                )
            );
        }
    }

    /**
     * checkKeyIsInt
     *
     * @param array $content
     * @param string $key
     * @param bool $nullable
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return void
     */
    public function checkKeyIsInt(array $content, string $key, bool $nullable = false): void
    {
        if (
            !array_key_exists($key, $content)
            || (!$nullable && !is_int($content[$key]))
            || ($nullable && !is_null($content[$key]) && !is_int($content[$key]))
        ) {
            throw new BadRequestHttpException(
                $this->translator->trans(
                    'openium_symfony_toolkit.integer_property',
                    ['_parameter' => $key],
                    'openium_symfony_toolkit'
                )
            );
        }
    }

    /**
     * checkKeyIsFloat
     *
     * @param array $content
     * @param string $key
     * @param bool $nullable
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return void
     */
    public function checkKeyIsFloat(array $content, string $key, bool $nullable = false): void
    {
        if (
            !array_key_exists($key, $content)
            || (!$nullable && !is_float($content[$key]))
            || ($nullable && !is_null($content[$key]) && !is_float($content['key']))
        ) {
            throw new BadRequestHttpException(
                $this->translator->trans(
                    'openium_symfony_toolkit.float_property',
                    ['_parameter' => $key],
                    'openium_symfony_toolkit'
                )
            );
        }
    }

    /**
     * checkKeyIsArray
     *
     * @param array $content
     * @param string $key
     * @param bool $allowEmpty
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return void
     */
    public function checkKeyIsArray(array $content, string $key, bool $allowEmpty = false): void
    {
        if (
            !array_key_exists($key, $content)
            || (!$allowEmpty && empty($content[$key]))
            || (!empty($content[$key]) && !is_array($content[$key]))
        ) {
            throw new BadRequestHttpException(
                $this->translator->trans(
                    'openium_symfony_toolkit.array_property',
                    ['_parameter' => $key],
                    'openium_symfony_toolkit'
                )
            );
        }
    }

    /**
     * getString
     *
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param string|null $default
     * @param bool $nullable
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return string|null
     */
    public function getString(
        array $content,
        string $key,
        bool $required = true,
        ?string $default = null,
        bool $nullable = false
    ): ?string {
        try {
            $this->checkKeyNotEmpty($content, $key, $nullable);
        } catch (BadRequestHttpException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        if ($content[$key] !== null) {
            return strval($content[$key]);
        } else {
            return null;
        }
    }

    /**
     * getBool
     *
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param bool|null $default
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return bool|null
     */
    public function getBool(array $content, string $key, bool $required = true, ?bool $default = true): ?bool
    {
        try {
            $this->checkKeyIsBoolean($content, $key);
        } catch (BadRequestHttpException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        return $content[$key];
    }

    /**
     * getInt
     *
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param int|null $default
     * @param bool $nullable
     *
     * @throws BadRequestHttpException
     * @return int|null
     */
    public function getInt(
        array $content,
        string $key,
        bool $required = true,
        ?int $default = 0,
        bool $nullable = false
    ): ?int {
        try {
            $this->checkKeyIsInt($content, $key, $nullable);
        } catch (BadRequestHttpException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        return $content[$key];
    }

    /**
     * getFloat
     *
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param float|null $default
     * @param bool $nullable
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return float|null
     */
    public function getFloat(
        array $content,
        string $key,
        bool $required = true,
        ?float $default = 0.0,
        bool $nullable = false
    ): ?float {
        try {
            $this->checkKeyIsFloat($content, $key, $nullable);
        } catch (BadRequestHttpException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        return $content[$key];
    }

    /**
     * getDateTimeInterface
     *
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param DateTimeInterface|null $default
     * @param bool $nullable
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return DateTimeInterface|null
     */
    public function getDateTimeInterface(
        array $content,
        string $key,
        bool $required = true,
        ?DateTimeInterface $default = null,
        bool $nullable = false
    ): ?DateTimeInterface {
        try {
            $this->checkKeyNotEmpty($content, $key, $nullable);
        } catch (BadRequestHttpException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        if ($content[$key] === null) {
            return null;
        }
        $dateTimeResult = DateStringUtils::getDateTimeFromString($content[$key]);
        // => false if wrong date format
        if ($dateTimeResult === false) {
            throw new BadRequestHttpException(
                $this->translator->trans('openium_symfony_toolkit.date_format', [], 'openium_symfony_toolkit')
            );
        }
        return $dateTimeResult;
    }

    /**
     * getArray
     *
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param array|null $default
     * @param bool $allowEmpty
     *
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @return array|null
     */
    public function getArray(
        array $content,
        string $key,
        bool $required = true,
        ?array $default = [],
        bool $allowEmpty = true
    ): ?array {
        try {
            $this->checkKeyIsArray($content, $key, $allowEmpty);
        } catch (BadRequestHttpException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        if ($content[$key] !== null) {
            return $content[$key];
        } else {
            return null;
        }
    }
}
