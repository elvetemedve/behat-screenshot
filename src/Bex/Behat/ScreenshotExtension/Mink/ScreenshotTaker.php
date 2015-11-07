<?php

namespace Bex\Behat\ScreenshotExtension\Mink;

use Behat\Mink\Mink;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Symfony\Component\Filesystem\Filesystem;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Driver\Container as ImageDriverContainer;

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

    /** @var ImageDriverContainer $driverContainer */
    private $driverContainer;

    /**
     * Constructor
     *
     * @param Mink $mink
     * @param OutputPrinter $output
     * @param ImageDriverContainer $driverContainer
     */
    public function __construct(Mink $mink, OutputPrinter $output, ImageDriverContainer $driverContainer)
    {
        $this->mink = $mink;
        $this->output = $output;
        $this->driverContainer = $driverContainer;
    }

    /**
     * Save the screenshot as the given filename
     *
     * @param string $fileName
     */
    public function takeScreenshot($fileName = 'failure.png')
    {
        $screenshot = $this->mink->getSession()->getScreenshot();

        foreach ($this->driverContainer->getActiveDrivers() as $imageDriver) {
            $imageUrl = $imageDriver->upload($screenshot, $fileName);
            $this->output->writeln('Screenshot has been taken. Open image at ' . $imageUrl);
        }
    }
}
