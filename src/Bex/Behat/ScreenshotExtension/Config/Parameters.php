<?php

namespace Bex\Behat\ScreenshotExtension\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This class represents the configurable parameters of the Behat extension
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class Parameters
{
    /** @var string $screenshotDirectory */
    private $screenshotDirectory;

    /** @var boolean $isImageUploadEnabled */
    private $isImageUploadEnabled;

    /** @var string $baseUrl */
    private $baseUrl;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->screenshotDirectory = $config['screenshot_directory'];
        $this->isImageUploadEnabled = $config['enable_image_upload'];
        $this->baseUrl = $config['base_url'];
    }

    /**
     * Defines the list of parameters in behat.yml parameters allowed by this extension
     *
     * @param ArrayNodeDefinition $builder
     */
    public static function configure(ArrayNodeDefinition $builder)
    {
        $builder->children()
            ->scalarNode('screenshot_directory')->defaultValue(sys_get_temp_dir())->end()
            ->scalarNode('enable_image_upload')->defaultValue(false)->end()
            ->scalarNode('image_driver')->defaultValue('uploadpie')->end()
            ->end();
    }

    /**
     * Returns the location of the screenshot directory in the local filesystem
     *
     * @return mixed
     */
    public function getScreenshotDirectory()
    {
        return $this->screenshotDirectory;
    }

    /**
     * Tells whether the image driver should be used
     * 
     * @return boolean
     */
    public function isImageUploadEnabled()
    {
        return $this->isImageUploadEnabled;
    }

    /**
     * Returns the base URL used by the Mink extension
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}