<?php

namespace Bex\Behat\ScreenshotExtension\ServiceContainer;

use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Bex\Behat\ScreenshotExtension\Config\Parameters;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriver;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

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
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        Parameters::configure($builder);
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        // Load dependency injection container from XML
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/config'));
        $loader->load('services.xml');

        // Register configuration parameters service
        $parameters = new Parameters($config);
        $container->set('bex.screenshot_extension.configuration_parameters', $parameters);

        $imageDriverDefinition = $container->getDefinition($parameters->getActiveImageDriver());
        $container->setDefinition('bex.screenshot_extension.image_driver.current', $imageDriverDefinition);
    }
}
