<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Config\Parameters;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class Local implements ImageDriver
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * @param Filesystem $filesystem
     * @param Parameters $parameters
     */
    public function __construct(Filesystem $filesystem, Parameters $parameters)
    {
        $this->filesystem = $filesystem;
        $this->parameters = $parameters;
    }

    /**
     * @param string $binaryImage
     * @param string $filename
     *
     * @return string URL to the image
     */
    public function upload($binaryImage, $filename)
    {
        $targetFile = $this->getTargetPath($filename);
        $this->ensureDirectoryExists(dirname($targetFile));
        $this->filesystem->dumpFile($targetFile, $binaryImage);

        return $targetFile;
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
}