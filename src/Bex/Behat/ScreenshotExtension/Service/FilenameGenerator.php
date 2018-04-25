<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\StepNode;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Config;

/**
 * This class generates a filename for the given Behat scenario step
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class FilenameGenerator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @param string $basePath
     */
    public function __construct(Config $config, $basePath)
    {
        $this->config = $config;
        $this->basePath = $basePath;
    }

    /**
     * @param  AfterScenarioTested $event
     *
     * @return string
     */
    public function generateFileName(AfterScenarioTested $event)
    {
        $filename = $this->config->getScreenshotFilenamePattern();

        $filename = str_replace('%SUITE%', $event->getSuite()->getName(), $filename);
        $filename = str_replace('%FEATURE_FILE_PATH%', $this->relativizePaths($event->getFeature()->getFile()), $filename);
        $filename = str_replace('%SCENARIO_LINE_NUMBER%', $event->getScenario()->getLine(), $filename);

        return preg_replace('/[^A-Za-z0-9\-]/', '_', mb_strtolower($filename)) . '.png';
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
