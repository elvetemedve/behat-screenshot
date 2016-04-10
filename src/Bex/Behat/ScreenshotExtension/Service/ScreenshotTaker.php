<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Mink\Mink;
use Behat\Testwork\Output\Printer\StreamOutputPrinter;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Behat\Testwork\Output\Printer\Factory\ConsoleOutputFactory;
/**
 * This class is responsible for taking screenshot by using the Mink session
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotTaker
{
    /** @var Mink $mink */
    private $mink;

    /** @var ConsoleOutputFactory $output */
    private $output;

    /** @var ImageDriverInterface[] $imageDrivers */
    private $imageDrivers;

    /** @var StreamOutputPrinter $outputStream */
    private $outputStream;
    
    /**
     * Constructor
     *
     * @param Mink $mink
     * @param StreamOutputPrinter $output
     * @param ImageDriverInterface[] $imageDrivers
     */
    public function __construct(Mink $mink, ConsoleOutputFactory $output, array $imageDrivers)
    {
        $this->mink = $mink;
        $this->output = $output;
        $this->imageDrivers = $imageDrivers;
        $this->outputStream = new StreamOutputPrinter ($output);
    }

    /**
     * Save the screenshot as the given filename
     *
     * @param string $fileName
     */
    public function takeScreenshot($fileName = 'failure.png')
    {
        try {
            $screenshot = $this->mink->getSession()->getScreenshot();

            foreach ($this->imageDrivers as $imageDriver) {
                $imageUrl = $imageDriver->upload($screenshot, $fileName);
                $this->outputStream->writeln('Screenshot has been taken. Open image at ' . $imageUrl);
            }
        } catch (\Exception $e) {
            $this->outputStream->writeln($e->getMessage());
        }
    }
}