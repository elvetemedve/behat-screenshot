<?php

namespace Bex\Behat\ScreenshotExtension\ServiceContainer\Driver;

use Bex\Behat\ScreenshotExtension\Driver\ImageDriver;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container as DIContainer;

class Container
{
    const DRIVER_TAG = 'bex.screenshot_extension.image_driver';

    /**
     * @var ImageDriver[]
     */
    private $drivers = [];

    /**
     * @var ImageDriver[]
     */
    private $activeDrivers = [];

    /**
     * @param  ContainerBuilder $container
     * @param  array            $configs
     */
    public function loadDrivers(ContainerBuilder $container, array $activeDrivers, array $driverConfigs)
    {
        $driverKeys = (empty($activeDrivers)) ? ['local'] : $activeDrivers;
        $this->drivers = $this->findDrivers($container, $driverKeys);

        $driverConfigs = $this->configureDrivers($driverConfigs);
        
        foreach ($this->drivers as $driverKey => $driver) {
            $driver->load($container, $driverConfigs[$driverKey]);
        }
    }

    /**
     * @return ImageDriver[]
     */
    public function getActiveDrivers()
    {
        return $this->drivers;
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $driverKeys 
     *
     * @return ImageDriver[]
     */
    private function findDrivers(ContainerBuilder $container, $driverKeys)
    {
        $drivers = [];

        // this will only works if the driver is in the loaded service.xml and tagged
        /*$driverIds = array_keys($container->findTaggedServiceIds(self::DRIVER_TAG));
        foreach ($driverIds as $driverId) {
            $driver = $container->get($driverId);
            $drivers[$driver->getConfigKey()] = $driver;
        }*/

        // this will always work if the driver is under this namespace:
        $namespace = 'Bex\\Behat\\ScreenshotExtension\\Driver';
        foreach ($driverKeys as $driverKey) {
            echo $driverKey . PHP_EOL;
            $driverClass = $namespace . '\\' . ucfirst(DIContainer::camelize($driverKey));
            if (class_exists($driverClass)) {
                $drivers[$driverKey] = new $driverClass();
            }
        }

        return $drivers;
    }

    /**
     * @param  array $driverConfigs
     *
     * @return array
     */
    private function configureDrivers($driverConfigs)
    {
        $tree = new TreeBuilder();
        $root = $tree->root('image_drivers');

        foreach ($this->drivers as $driver) {
            $driver->configure($root->children()->arrayNode($driver->getConfigKey()));
            if (!isset($driverConfigs[$driver->getConfigKey()])) {
                $driverConfigs[$driver->getConfigKey()] = [];
            }
        }

        $configProcessor = new Processor();

        return $configProcessor->process($tree->buildTree(), ['image_drivers' => $driverConfigs]);
    }
}