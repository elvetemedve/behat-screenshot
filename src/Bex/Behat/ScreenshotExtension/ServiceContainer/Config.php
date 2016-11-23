<?php

namespace Bex\Behat\ScreenshotExtension\ServiceContainer;

use Bex\Behat\ExtensionDriverLocator\DriverLocator;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Config
{
    const EXTENSION_CONFIG_KEY = 'screenshot';
    const CONFIG_CONTAINER_ID = 'bex.screenshot_extension.config';

    const IMAGE_DRIVER_NAMESPACE = 'Bex\\Behat\\ScreenshotExtension\\Driver';
    const IMAGE_DRIVER_PARENT = 'Bex\\Behat\\ScreenshotExtension\\Driver\\ImageDriverInterface';

    const CONFIG_PARAM_EXTENSTION_ENABLED = 'enabled';
    const CONFIG_PARAM_ACTIVE_IMAGE_DRIVERS = 'active_image_drivers';
    const CONFIG_PARAM_IMAGE_DRIVER_CONFIGS = 'image_drivers';
    const CONFIG_PARAM_SCREENSHOT_TAKING_MODE = 'screenshot_taking_mode';

    const SCREENSHOT_TAKING_MODE_FAILED_STEPS = 'failed_steps';
    const SCREENSHOT_TAKING_MODE_FAILED_SCENARIOS = 'failed_scenarios';
    const SCREENSHOT_TAKING_MODE_ALL_SCENARIOS = 'all_scenarios';

    const DEFAULT_IMAGE_DRIVER_KEY = 'local';
    const DEFAULT_SCREENSHOT_TAKING_MODE = self::SCREENSHOT_TAKING_MODE_FAILED_STEPS;

    const ERROR_MESSAGE_IMAGICK_NOT_FOUND = 'Imagemagick PHP extension is required, but not installed.';
    
    /**
     * @var DriverLocator
     */
    private $driverLocator;

    /**
     * @var array
     */
    private $imageDrivers;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var string
     */
    private $screenshotTakingMode;

    /**
     * @var string[]
     */
    private $imageDriverKeys;

    /**
     * @var array
     */
    private $imageDriverConfigs;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->driverLocator = DriverLocator::getInstance(self::IMAGE_DRIVER_NAMESPACE, self::IMAGE_DRIVER_PARENT);
        $this->enabled = $config[self::CONFIG_PARAM_EXTENSTION_ENABLED];
        $this->screenshotTakingMode = $config[self::CONFIG_PARAM_SCREENSHOT_TAKING_MODE];
        $this->imageDriverKeys = $config[self::CONFIG_PARAM_ACTIVE_IMAGE_DRIVERS];
        $this->imageDriverConfigs = $config[self::CONFIG_PARAM_IMAGE_DRIVER_CONFIGS];
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getImageDrivers()
    {
        return $this->imageDrivers;
    }

    /**
     * @return boolean
     */
    public function shouldCombineImages()
    {
        return $this->screenshotTakingMode !== self::SCREENSHOT_TAKING_MODE_FAILED_STEPS;
    }

    /**
     * @return boolean
     */
    public function shouldRecordAllScenarios()
    {
        return $this->screenshotTakingMode == self::SCREENSHOT_TAKING_MODE_ALL_SCENARIOS;
    }

    /**
     * @return boolean
     */
    public function shouldRecordAllSteps()
    {
        return $this->screenshotTakingMode != self::SCREENSHOT_TAKING_MODE_FAILED_STEPS;
    }

    /**
     * Init service container and load image drivers
     * 
     * @param  ContainerBuilder $container
     */
    public function loadServices(ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.xml');
        $this->imageDrivers = $this->driverLocator->findDrivers(
            $container,
            $this->imageDriverKeys,
            $this->imageDriverConfigs
        );
    }

    /**
     * @return string[]
     */
    public static function getScreenshotTakingModes()
    {
        return [
            self::SCREENSHOT_TAKING_MODE_FAILED_STEPS,
            self::SCREENSHOT_TAKING_MODE_FAILED_SCENARIOS,
            self::SCREENSHOT_TAKING_MODE_ALL_SCENARIOS
        ];
    }

    /**
     * @return \Closure
     */
    public static function getScreenshotTakingModeValidator()
    {
        return function ($mode) {
            return ($mode !== 'failed_steps') && !class_exists('\Imagick');
        };
    }
}
