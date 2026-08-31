<?php

namespace Openium\SymfonyToolKitBundle\Tests\DependencyInjection;

use Openium\SymfonyToolKitBundle\DependencyInjection\Configuration;
use Openium\SymfonyToolKitBundle\EventListener\PathKernelExceptionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), []);

        self::assertSame('%kernel.project_dir%/public', $config['uploads']['public_dir']);
        self::assertSame('uploads', $config['uploads']['dir_name']);
        self::assertFalse($config['kernel_exception_listener']['enabled']);
        self::assertSame('/api', $config['kernel_exception_listener']['path']);
        self::assertSame(PathKernelExceptionListener::class, $config['kernel_exception_listener']['class']);
    }

    public function testCustomConfiguration(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [
            [
                'uploads' => [
                    'public_dir' => '/var/www/public',
                    'dir_name' => 'medias',
                ],
                'kernel_exception_listener' => [
                    'enabled' => true,
                    'path' => '/api/v2',
                    'class' => 'App\\Listener\\CustomListener',
                ],
            ],
        ]);

        self::assertSame('/var/www/public', $config['uploads']['public_dir']);
        self::assertSame('medias', $config['uploads']['dir_name']);
        self::assertTrue($config['kernel_exception_listener']['enabled']);
        self::assertSame('/api/v2', $config['kernel_exception_listener']['path']);
        self::assertSame('App\\Listener\\CustomListener', $config['kernel_exception_listener']['class']);
    }
}
