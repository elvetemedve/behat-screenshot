<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Gherkin\Node\StepNode;

/**
 * This class generates a filename for the given Behat scenario step
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class StepFilenameGenerator
{
    /**
     * Returns a valid filename without path
     *
     * @param StepNode $step
     *
     * @return string
     */
    public function convertStepToFileName(StepNode $step)
    {
        return preg_replace('/[^A-Za-z0-9\-]/', '_', mb_strtolower($step->getText())) . '.png';
    }
}