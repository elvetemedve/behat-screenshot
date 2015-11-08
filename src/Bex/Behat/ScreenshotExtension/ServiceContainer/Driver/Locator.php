<?php

namespace Bex\Behat\ScreenshotExtension\ServiceContainer\Driver;

use Bex\Behat\ScreenshotExtension\Driver\DriverInterface;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Driver\ClassNameResolver;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Driver\ClassValidator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Container as DIContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Locator
{
    /**
     * @var ClassNameResolver
     */
    private $classNameResolver;

    /**
     * @var DriverInterface[]
     */
    private $drivers = [];

    /**
     * @param ClassNameResolver $classNameResolver
     */
    public function __construct(ClassNameResolver $classNameResolver)
    {
        $this->classNameResolver = $classNameResolver;
    }

    /**
     * @param  string $namespace
     * @param  string $parent
     *
     * @return Locator
     */
    public static function getInstance($namespace, $parent = '')
    {
        return new self(new ClassNameResolver($namespace, new ClassValidator($parent)));
    }

    /**
     * @param  ContainerBuilder $container
     * @param  array            $configs
     *
     * @return DriverInterface[]
     */
    public function findDrivers(ContainerBuilder $container, array $activeDrivers, array $driverConfigs)
    {
        $this->createDrivers($activeDrivers);
        $configTree = $this->configureDrivers($driverConfigs);
        $driverConfigs = $this->processDriverConfiguration($configTree, $driverConfigs);
        $this->loadDrivers($container, $driverConfigs);

        return $this->drivers;
    }

    /**
     * @return DriverInterface[]
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * @param array            $driverKeys 
     *
     * @return DriverInterface[]
     */
    private function createDrivers($driverKeys)
    {
        $this->drivers = [];

        foreach ($driverKeys as $driverKey) {
            $driverClass = $this->classNameResolver->getClassNameByDriverKey($driverKey);
            $this->drivers[$driverKey] = new $driverClass();
        }

        return $this->drivers;
    }

    /**
     * @param  array $driverConfigs
     *
     * @return NodeInterface
     */
    private function configureDrivers($driverConfigs)
    {
        $tree = new TreeBuilder();
        $root = $tree->root('drivers');

        foreach ($this->drivers as $driver) {
            $driver->configure($root->children()->arrayNode($driver->getConfigKey()));
        }

        return $tree->buildTree();
    }

    /**
     * @param  NodeInterface $configTree
     * @param  array         $configs
     *
     * @return array The processed configuration
     */
    private function processDriverConfiguration(NodeInterface $configTree, array $configs)
    {
        $configProcessor = new Processor();

        foreach ($this->drivers as $key => $driver) {
            $configs[$key] = isset($configs[$key]) ? $configs[$key] : [];
        }

        return $configProcessor->process($configTree, ['drivers' => $configs]);
    }

    /**
     * @param  ContainerBuilder $container
     * @param  array            $driverConfigs
     *
     * @return DriverInterface[]
     */
    private function loadDrivers(ContainerBuilder $container, array $driverConfigs)
    {
        foreach ($this->drivers as $driverKey => $driver) {
            $driver->load($container, $driverConfigs[$driverKey]);
        }

        return $this->drivers;
    }
}