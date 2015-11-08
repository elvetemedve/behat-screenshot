<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ExtensionDriverLocator\DriverInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ImageDriverInterface extends DriverInterface
{
    /**
     * @param string $binaryImage
     * @param string $filename
     *
     * @return string URL to the image
     */
    public function upload($binaryImage, $filename);
}