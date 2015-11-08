<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface DriverInterface
{
    /**
     * @param  ArrayNodeDefinition $builder
     *
     * @return void
     */
    public function configure(ArrayNodeDefinition $builder);

    /**
     * @param  ContainerBuilder $container
     * @param  array            $config
     *
     * @return void
     */
    public function load(ContainerBuilder $container, array $config);
}