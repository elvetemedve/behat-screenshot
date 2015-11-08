<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Driver\DriverInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ImageDriverInterface extends DriverInterface
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

    /**
     * @param string $binaryImage
     * @param string $filename
     *
     * @return string URL to the image
     */
    public function upload($binaryImage, $filename);
}