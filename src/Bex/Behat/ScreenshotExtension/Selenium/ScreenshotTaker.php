<?php

namespace Bex\Behat\ScreenshotExtension\Selenium;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Mink;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Bex\Behat\ScreenshotExtension\Config\Parameters;

/**
 * This class is responsible for taking screenshot by using the Mink session
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotTaker
{

    /** @var Mink $mink */
    private $mink;

    /** @var Parameters $parameters */
    private $parameters;

    /** @var OutputPrinter $output */
    private $output;

    /**
     * Constructor
     *
     * @param Parameters $parameters
     * @param Mink $mink
     * @param OutputPrinter $output
     */
    public function __construct(Parameters $parameters, Mink $mink, OutputPrinter $output)
    {
        $this->mink = $mink;
        $this->parameters = $parameters;
        $this->output = $output;
    }

    /**
     * Save the screenshot as the given filename
     *
     * Only Selenium driver is supported.
     *
     * @param string $fileName
     */
    public function takeScreenshot($fileName = 'failure.png')
    {
        $targetFile = $this->getTargetPath($fileName);
        $this->ensureDirectoryExists(dirname($targetFile));
        file_put_contents($targetFile, $this->mink->getSession()->getScreenshot());
        $this->output->writeln('Screenshot has been taken. Open image at ' . $this->getImagePath($targetFile));
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getTargetPath($fileName)
    {
        return rtrim($this->parameters->getScreenshotDirectory(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @param string $directory
     *
     * @throws \RuntimeException
     */
    private function ensureDirectoryExists($directory)
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0770, true)) {
                throw new \RuntimeException(sprintf('Cannot create screenshot directory "%s".', $directory));
            }
        }
    }

    /**
     * @param string $targetFile
     *
     * @return string
     */
    private function getImagePath($targetFile)
    {
        return strlen($this->parameters->getScreenshotDirectoryPathOnHttp()) > 0
            ? rtrim($this->parameters->getBaseUrl(), DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . trim($this->parameters->getScreenshotDirectoryPathOnHttp(), DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . basename($targetFile)
            : $targetFile;
    }
}
