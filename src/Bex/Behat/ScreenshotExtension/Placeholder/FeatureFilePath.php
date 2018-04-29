<?php

namespace Bex\Behat\ScreenshotExtension\Placeholder;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Bex\Behat\ScreenshotExtension\Placeholder\PlaceholderInterface;

class FeatureFilePath implements PlaceholderInterface
{
    const KEY = '%FEATURE_FILE_PATH%';

    /**
     * @var string
     */
    private $basePath;
    
    /**
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    public function getKey()
    {
        return self::KEY;   
    }

    public function getValue(AfterScenarioTested $event)
    {
        return $this->relativizePaths($event->getFeature()->getFile());
    }

    /**
     * Transforms path to relative.
     *
     * @param string $path
     *
     * @return string
     */
    private function relativizePaths($path)
    {
        if (!$this->basePath) {
            return $path;
        }

        return str_replace($this->basePath . DIRECTORY_SEPARATOR, '', $path);
    }
}
