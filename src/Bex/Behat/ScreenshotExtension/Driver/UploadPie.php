<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class UploadPie implements ImageDriver
{

    /**
     * @param  NodeDefinition $builder
     *
     * @return void
     */
    public static function configure(NodeDefinition $builder)
    {
        echo 'configure '. __CLASS__;exit;
        // TODO: Implement configure() method.
    }

    /**
     * @param  string $filePath
     *
     * @return string Image url
     */
    public function upload($filePath)
    {
        // TODO: Implement upload() method.
    }
}