<?php

namespace Bex\Behat\ScreenshotExtension\Mink;

use Behat\Mink\Mink;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Bex\Behat\ScreenshotExtension\Config\Parameters;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriver;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

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

    /** @var ImageDriver $imageDriver */
    private $imageDriver;

    /**
     * Constructor
     *
     * @param Mink $mink
     * @param OutputPrinter $output
     * @param ImageDriver $imageDriver
     */
    public function __construct(Mink $mink, OutputPrinter $output, ImageDriver $imageDriver)
    {
        $this->mink = $mink;
        $this->output = $output;
        $this->imageDriver = $imageDriver;
    }

    /**
     * Save the screenshot as the given filename
     *
     * @param string $fileName
     */
    public function takeScreenshot($fileName = 'failure.png')
    {
        $imageUrl = $this->imageDriver->upload($this->mink->getSession()->getScreenshot(), $fileName);

        $this->output->writeln('Screenshot has been taken. Open image at ' . $imageUrl);
    }
}
