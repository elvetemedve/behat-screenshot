<?php

namespace Bex\Behat\ScreenshotExtension\Mink;

use Behat\Mink\Driver\Selenium2Driver;
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

    /** @var Parameters $parameters */
    private $parameters;

    /** @var OutputPrinter $output */
    private $output;

    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var ImageDriver $imageDriver */
    private $imageDriver;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param Parameters $parameters
     * @param Mink $mink
     * @param OutputPrinter $output
     */
    public function __construct(Filesystem $filesystem, Parameters $parameters, Mink $mink, OutputPrinter $output)
    {
        $this->mink = $mink;
        $this->parameters = $parameters;
        $this->output = $output;
        $this->filesystem = $filesystem;
        //$this->imageDriver = $imageDriver;
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
        $this->filesystem->dumpFile($targetFile, $this->mink->getSession()->getScreenshot());
        if ($this->parameters->isImageUploadEnabled()) {
            $imageUrl = $this->imageDriver->upload($targetFile);
            $this->output->writeln('Screenshot has been taken. Open image at ' . $imageUrl);
        }
        $this->output->writeln('Screenshot has been taken. Open image at ' . $targetFile);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getTargetPath($fileName)
    {
        $path = rtrim($this->parameters->getScreenshotDirectory(), DIRECTORY_SEPARATOR);
        return empty($path) ? $fileName : $path . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @param string $directory
     *
     * @throws \RuntimeException
     */
    private function ensureDirectoryExists($directory)
    {
        try {
            if (!$this->filesystem->exists($directory)) {
                $this->filesystem->mkdir($directory, 0770);
            }
        } catch (IOException $e) {
            throw new \RuntimeException(sprintf('Cannot create screenshot directory "%s".', $directory));
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
