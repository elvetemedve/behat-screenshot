<?php

namespace Bex\Behat\ScreenshotExtension\ServiceContainer;

use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Bex\Behat\ScreenshotExtension\Config\Parameters;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriver;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * This class is the entry point of the screenshot extension
 *
 * It provides debugging functionality by taking a screenshot of a failed tests.
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
final class ScreenshotExtension implements Extension
{
    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'screenshot';
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {

    }

    /**
     * @param  ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('active_image_drivers')
                    ->defaultValue(['local'])
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function($value) { return array($value); } )
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->fixXmlConfig('image_driver')
            ->children()
                ->arrayNode('image_drivers')
                    ->prototype('array')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/config'));
        $loader->load('services.xml');

        $driverContainer = $container->get('bex.screenshot_extension.image_driver_container');
        $driverContainer->loadDrivers($container, $config['active_image_drivers'], $config['image_drivers']);
        $container->set('bex.screenshot_extension.image_driver_container', $driverContainer);
    }
}
