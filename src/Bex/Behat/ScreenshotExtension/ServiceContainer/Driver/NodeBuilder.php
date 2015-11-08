<?php

namespace Bex\Behat\ScreenshotExtension\ServiceContainer\Driver;

use Bex\Behat\ScreenshotExtension\ServiceContainer\Driver\ClassNameResolver;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Driver\ClassValidator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class NodeBuilder
{
    /**
     * @var ClassNameResolver
     */
    private $driverClassNameResolver;

    /**
     * @param ClassNameResolver $driverClassNameResolver
     */
    public function __construct(ClassNameResolver $driverClassNameResolver)
    {
        $this->driverClassNameResolver = $driverClassNameResolver;
    }

    /**
     * @param  string $namespace
     * @param  string $parent
     *
     * @return NodeBuilder
     */
    public static function getInstance($namespace, $parent = '')
    {
        return new self(new ClassNameResolver($namespace, new ClassValidator($parent)));
    }

    /**
     * @param  ArrayNodeDefinition $builder
     * @param  string              $activeDriversNodeName
     * @param  string              $driversNodeName
     * @param  array               $defaultActiveDrivers
     *
     * @return void
     */
    public function buildDriverNodes(
        ArrayNodeDefinition $builder,
        $activeDriversNodeName,
        $driversNodeName,
        $defaultActiveDrivers
    ) {
        $builder
            ->children()
                ->arrayNode($activeDriversNodeName)
                    ->defaultValue($defaultActiveDrivers)
                    ->beforeNormalization()
                        ->ifString()
                        ->then($this->getDefaultValueInitializer())
                    ->end()
                    ->validate()
                        ->ifTrue($this->getDriverKeyValidator())
                        ->thenInvalid('%s')
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->fixXmlConfig($driversNodeName . '_child', $driversNodeName)
            ->children()
                ->arrayNode($driversNodeName)
                    ->prototype('array')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @return \Closure
     */
    private function getDefaultValueInitializer()
    {
        return function ($value) {
            return [$value];
        };
    }

    /**
     * @return \Closure
     */
    private function getDriverKeyValidator()
    {
        $classNameResolver = $this->driverClassNameResolver;
        
        return function ($driverKeys) use ($classNameResolver) {
            foreach ($driverKeys as $driverKey) {
                $classNameResolver->getClassNameByDriverKey($driverKey);
            }

            return false;
        };
    }
}