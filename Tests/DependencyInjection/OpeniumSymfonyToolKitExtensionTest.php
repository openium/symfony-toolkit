<?php

namespace Openium\SymfonyToolKitBundle\Tests\DependencyInjection;

use Openium\SymfonyToolKitBundle\DependencyInjection\OpeniumSymfonyToolKitExtension;
use Openium\SymfonyToolKitBundle\EventListener\PathKernelExceptionListener;
use Openium\SymfonyToolKitBundle\Service\AtHelperInterface;
use Openium\SymfonyToolKitBundle\Service\DoctrineExceptionHandlerServiceInterface;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Openium\SymfonyToolKitBundle\Service\FileUploaderServiceInterface;
use Openium\SymfonyToolKitBundle\Service\ServerServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OpeniumSymfonyToolKitExtensionTest extends TestCase
{
    public function testLoadWithDefaults(): void
    {
        $container = new ContainerBuilder();
        (new OpeniumSymfonyToolKitExtension())->load([], $container);

        self::assertSame(
            '%kernel.project_dir%/public',
            $container->getParameter('openium_symfony_toolkit.public_dir')
        );
        self::assertSame('uploads', $container->getParameter('openium_symfony_toolkit.uploads_dir_name'));
        self::assertFalse($container->getParameter('openium_symfony_toolkit.kernel_exception_listener_enable'));
        self::assertSame('/api', $container->getParameter('openium_symfony_toolkit.kernel_exception_listener_path'));
        self::assertSame(
            PathKernelExceptionListener::class,
            $container->getParameter('openium_symfony_toolkit.kernel_exception_listener_class')
        );

        self::assertTrue($container->hasAlias(FileUploaderServiceInterface::class));
        self::assertTrue($container->hasAlias(ExceptionFormatServiceInterface::class));
        self::assertTrue($container->hasAlias(DoctrineExceptionHandlerServiceInterface::class));
        self::assertTrue($container->hasAlias(ServerServiceInterface::class));
        self::assertTrue($container->hasAlias(AtHelperInterface::class));
    }

    public function testLoadWithCustomConfig(): void
    {
        $container = new ContainerBuilder();
        (new OpeniumSymfonyToolKitExtension())->load([
            [
                'uploads' => [
                    'public_dir' => '/var/www/public',
                    'dir_name' => 'medias',
                ],
                'kernel_exception_listener' => [
                    'enabled' => true,
                    'path' => '/api/v2',
                ],
            ],
        ], $container);

        self::assertSame('/var/www/public', $container->getParameter('openium_symfony_toolkit.public_dir'));
        self::assertSame('medias', $container->getParameter('openium_symfony_toolkit.uploads_dir_name'));
        self::assertTrue($container->getParameter('openium_symfony_toolkit.kernel_exception_listener_enable'));
        self::assertSame(
            '/api/v2',
            $container->getParameter('openium_symfony_toolkit.kernel_exception_listener_path')
        );
    }
}
