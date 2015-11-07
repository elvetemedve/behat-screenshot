<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;

abstract class ImageDriver
{
    /**
     * @return string
     */
    public function getConfigKey()
    {
        $fullclass = static::class;
        return strtolower(Container::underscore(substr($fullclass, strrpos($fullclass, '\\')+1)));
    }

    /**
     * @param  ArrayNodeDefinition $builder
     *
     * @return void
     */
    abstract public function configure(ArrayNodeDefinition $builder);

    /**
     * @param  ContainerBuilder $container
     * @param  array            $config
     *
     * @return void
     */
    abstract public function load(ContainerBuilder $container, array $config);

    /**
     * @param string $binaryImage
     * @param string $filename
     *
     * @return string URL to the image
     */
    abstract public function upload($binaryImage, $filename);
}