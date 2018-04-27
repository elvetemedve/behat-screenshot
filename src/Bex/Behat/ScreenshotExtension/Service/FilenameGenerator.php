<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\StepNode;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Config;
use Bex\Behat\ScreenshotExtension\Service\PlaceholderReplacer;

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
     * @var PlaceholderReplacer
     */
    private $placeholderReplacer;

    /**
     * @param Config              $config
     * @param PlaceholderReplacer $placeholderReplacer
     */
    public function __construct(Config $config, PlaceholderReplacer $placeholderReplacer)
    {
        $this->config = $config;
        $this->placeholderReplacer = $placeholderReplacer;
    }

    /**
     * @param  AfterScenarioTested $event
     *
     * @return string
     */
    public function generateFileName(AfterScenarioTested $event)
    {
        $filename = $this->config->getScreenshotFilenamePattern();

        $filename = $this->placeholderReplacer->replacePlaceholders($filename, $event);

        return preg_replace('/[^A-Za-z0-9\-]/', '_', mb_strtolower($filename)) . '.png';
    }
}
