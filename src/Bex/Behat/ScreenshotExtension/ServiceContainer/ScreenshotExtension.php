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
use Bex\Behat\ScreenshotExtension\ServiceContainer\Driver\Locator as DriverLocator;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Driver\NodeBuilder as DriverNodeBuilder;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension as Event;

/**
 * This class is the entry point of the screenshot extension
 *
 * It provides debugging functionality by taking a screenshot of a failed tests.
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
final class ScreenshotExtension implements Extension
{
    const DRIVER_NAMESPACE = 'Bex\\Behat\\ScreenshotExtension\\Driver';
    const DRIVER_PARENT = 'Bex\\Behat\\ScreenshotExtension\\Driver\\ImageDriver';

    /**
     * @var DriverLocator
     */
    private $driverLocator;

    /**
     * @var DriverNodeBuilder
     */
    private $driverNodeBuilder;

    /**
     * Constuctor: init extension
     */
    public function __construct()
    {
        $this->driverLocator = DriverLocator::getInstance(self::DRIVER_NAMESPACE, self::DRIVER_PARENT);
        $this->driverNodeBuilder = DriverNodeBuilder::getInstance(self::DRIVER_NAMESPACE, self::DRIVER_PARENT);
    }

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
                ->booleanNode('enabled')
                    ->defaultValue(true)
                ->end()
            ->end();

        $this->driverNodeBuilder->buildDriverNodes($builder, 'active_image_drivers', 'image_drivers', ['local']);
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

        $container->setParameter('bex.screenshot_extension.active_image_drivers', []);

        if ($config['enabled']) {
            $drivers = $this->driverLocator->findDrivers(
                $container,
                $config['active_image_drivers'],
                $config['image_drivers']
            );
            $container->setParameter('bex.screenshot_extension.active_image_drivers', $drivers);
            $container->getDefinition('bex.screenshot_extension.screenshot_listener')->addTag(Event::SUBSCRIBER_TAG);
        }
    }
}
