<?php

namespace Bex\Behat\ScreenshotExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Bex\Behat\ExtensionDriverLocator\DriverLocator;
use Bex\Behat\ExtensionDriverLocator\DriverNodeBuilder;

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
    const DRIVER_PARENT = 'Bex\\Behat\\ScreenshotExtension\\Driver\\ImageDriverInterface';

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
     * {@inheritdoc}
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
        $builder
            ->children()
                ->booleanNode('enabled')
                    ->defaultTrue()
                ->end()
                ->booleanNode('record_all_steps')
                    ->defaultFalse()
                    ->validate()
                        ->ifTrue($this->getImageMagickValidator())
                        ->thenInvalid('Imagemagick PHP extension is required, but not installed.')
                    ->end()
                ->end()
            ->end();

        $this->driverNodeBuilder->buildDriverNodes($builder, 'active_image_drivers', 'image_drivers', ['local']);
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        if ($config['enabled']) {
            $this->loadExtension($container, $config);
        }
    }

    /**
     * @param  ContainerBuilder $container
     * @param array $config
     */
    private function loadExtension(ContainerBuilder $container, array $config)
    {
        $this->registerServices($container);
        $drivers = $this->driverLocator->findDrivers($container, $config['active_image_drivers'], $config['image_drivers']);
        $container->setParameter('bex.screenshot_extension.active_image_drivers', $drivers);
        $container->setParameter('bex.screenshot_extension.record_all_steps', $config['record_all_steps']);
    }

    /**
     * @param  ContainerBuilder $container
     */
    private function registerServices(ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.xml');
    }

    private function getImageMagickValidator()
    {
        return function ($enabled) {
            return $enabled && !class_exists('\Imagick');
        };
    }
}
