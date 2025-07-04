<?php

namespace Openium\SymfonyToolKitBundle\DependencyInjection;

use Exception;
use Openium\SymfonyToolKitBundle\Service\AtHelperInterface;
use Openium\SymfonyToolKitBundle\Service\DoctrineExceptionHandlerServiceInterface;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Openium\SymfonyToolKitBundle\Service\FileUploaderServiceInterface;
use Openium\SymfonyToolKitBundle\Service\ServerServiceInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class OpeniumSymfonyToolKitExtension
 *
 * @package Openium\SymfonyToolKitBundle\DependencyInjection
 */
class OpeniumSymfonyToolKitExtension extends Extension
{
    /**
     * Load services
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        if ($configuration instanceof ConfigurationInterface) {
            $this->processConfiguration($configuration, $configs);
        }

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        $container->setAlias(
            FileUploaderServiceInterface::class,
            new Alias('openium_symfony_toolkit.file_uploader')
        );
        $container->setAlias(
            ExceptionFormatServiceInterface::class,
            new Alias('openium_symfony_toolkit.exception_format')
        );
        $container->setAlias(
            DoctrineExceptionHandlerServiceInterface::class,
            new Alias('openium_symfony_toolkit.doctrine_exception_handler')
        );
        $container->setAlias(
            ServerServiceInterface::class,
            new Alias('openium_symfony_toolkit.server')
        );
        $container->setAlias(
            AtHelperInterface::class,
            new Alias('openium_symfony_toolkit.at_helper')
        );
    }
}
