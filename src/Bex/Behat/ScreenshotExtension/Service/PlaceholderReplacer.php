<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;

class PlaceholderReplacer
{
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

    /**
     * @param string $text
     * @param AfterScenarioTested $event
     *
     * @return string
     */
    public function replacePlaceholders($text, AfterScenarioTested $event)
    {
        $map = [
          '%SUITE%' => $event->getSuite()->getName(),
          '%FEATURE_FILE_PATH%' => $this->relativizePaths($event->getFeature()->getFile()),
          '%SCENARIO_LINE_NUMBER%' => $event->getScenario()->getLine()
        ];

        foreach ($map as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
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
