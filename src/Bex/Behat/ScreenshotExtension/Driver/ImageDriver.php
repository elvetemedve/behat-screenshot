<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;

interface ImageDriver
{
    /**
     * @param  NodeDefinition $builder
     *
     * @return void
     */
    public static function configure(NodeDefinition $builder);

    /**
     * @param  string $filePath
     *
     * @return string Image url
     */
    public function upload($filePath);
}