<?php

namespace Bex\Behat\ScreenshotExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Bex\Behat\ExtensionDriverLocator\DriverLocator;
use Bex\Behat\ExtensionDriverLocator\DriverNodeBuilder;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Config;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
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
     * @var DriverNodeBuilder
     */
    private $driverNodeBuilder;

    /**
     * Constuctor: init extension
     */
    public function __construct()
    {
        $this->driverNodeBuilder = DriverNodeBuilder::getInstance(
            Config::IMAGE_DRIVER_NAMESPACE,
            Config::IMAGE_DRIVER_PARENT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return Config::EXTENSION_CONFIG_KEY;
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
        $this->configureExtensionParams($builder);
        $this->configureDriverParams($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $extensionConfig = new Config($config);
        
        if ($extensionConfig->isEnabled()) {
            $extensionConfig->loadServices($container);
            $container->set(Config::CONFIG_CONTAINER_ID, $extensionConfig);
        }
    }

    private function configureExtensionParams(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->booleanNode(Config::CONFIG_PARAM_EXTENSTION_ENABLED)
                    ->defaultTrue()
                ->end()
                ->enumNode(Config::CONFIG_PARAM_SCREENSHOT_TAKING_MODE)
                    ->values(Config::getScreenshotTakingModes())
                    ->defaultValue(Config::DEFAULT_SCREENSHOT_TAKING_MODE)
                    ->validate()
                        ->ifTrue(Config::getScreenshotTakingModeValidator())
                        ->thenInvalid(Config::ERROR_MESSAGE_IMAGICK_NOT_FOUND)
                    ->end()
                ->end()
            ->end();
    }

    private function configureDriverParams(ArrayNodeDefinition $builder)
    {
        $this->driverNodeBuilder->buildDriverNodes(
            $builder,
            Config::CONFIG_PARAM_ACTIVE_IMAGE_DRIVERS,
            Config::CONFIG_PARAM_IMAGE_DRIVER_CONFIGS,
            [Config::DEFAULT_IMAGE_DRIVER_KEY]
        );
    }
}
