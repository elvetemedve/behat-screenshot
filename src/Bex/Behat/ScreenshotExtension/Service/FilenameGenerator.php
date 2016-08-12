<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\StepNode;

/**
 * This class generates a filename for the given Behat scenario step
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class FilenameGenerator
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
     * @param  FeatureNode  $featureNode
     * @param  ScenarioInterface $scenarioNode
     *
     * @return string
     */
    public function generateFileName(FeatureNode $featureNode, ScenarioInterface $scenarioNode)
    {
        $feature = $this->relativizePaths($featureNode->getFile());
        $line = $scenarioNode->getLine();
        $fileName = join('_', [$feature, $line]);
        return preg_replace('/[^A-Za-z0-9\-]/', '_', mb_strtolower($fileName)) . '.png';
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
