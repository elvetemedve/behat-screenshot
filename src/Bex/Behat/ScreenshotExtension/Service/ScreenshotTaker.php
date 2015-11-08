<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Mink\Mink;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;

/**
 * This class is responsible for taking screenshot by using the Mink session
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotTaker
{
    /** @var Mink $mink */
    private $mink;

    /** @var OutputPrinter $output */
    private $output;

    /** @var ImageDriverInterface[] $imageDrivers */
    private $imageDrivers;

    /**
     * Constructor
     *
     * @param Mink $mink
     * @param OutputPrinter $output
     * @param ImageDriverInterface[] $imageDrivers
     */
    public function __construct(Mink $mink, OutputPrinter $output, array $imageDrivers)
    {
        $this->mink = $mink;
        $this->output = $output;
        $this->imageDrivers = $imageDrivers;
    }

    /**
     * Save the screenshot as the given filename
     *
     * @param string $fileName
     */
    public function takeScreenshot($fileName = 'failure.png')
    {
        $screenshot = $this->mink->getSession()->getScreenshot();

        foreach ($this->imageDrivers as $imageDriver) {
            $imageUrl = $imageDriver->upload($screenshot, $fileName);
            $this->output->writeln('Screenshot has been taken. Open image at ' . $imageUrl);
        }
    }
}
