<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Local implements ImageDriverInterface
{
    const DEFAULT_DIRECTORY = 'behat-screenshot';
    const CONFIG_PARAM_SCREENSHOT_DIRECTORY = 'screenshot_directory';
    const CONFIG_PARAM_CLEAR_SCREENSHOT_DIRECTORY = 'clear_screenshot_directory';
    const ERROR_MESSAGE_FINFO_NOT_FOUND = 'The fileinfo PHP extension is required, but not installed.';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $screenshotDirectory;

    /**
     * @var Finder
     */
    private $finder;
    
    /**
     * @var \finfo
     */
    private $fileInfo;

    /**
     * @param Filesystem $filesystem
     * @param Finder $finder
     * @param \finfo $fileInfo
     */
    public function __construct(Filesystem $filesystem = null, Finder $finder = null, $fileInfo = null)
    {
        $this->filesystem = $filesystem ?: new Filesystem();
        $this->finder = $finder ?: new Finder();
        $this->fileInfo = $fileInfo;
    }

    /**
     * @param  ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode(self::CONFIG_PARAM_SCREENSHOT_DIRECTORY)
                    ->defaultValue($this->getDefaultDirectory())
                ->end()
                ->booleanNode(self::CONFIG_PARAM_CLEAR_SCREENSHOT_DIRECTORY)
                    ->defaultValue(false)
                    ->validate()
                        ->ifTrue($this->getClearScreenshotDirectoryFeatureValidator())
                        ->thenInvalid(self::ERROR_MESSAGE_FINFO_NOT_FOUND)
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @return \Closure
     */
    private function getClearScreenshotDirectoryFeatureValidator()
    {
        return function ($isFeatureEnabled) {
            return $isFeatureEnabled && !class_exists('\finfo');
        };
    }


    /**
     * @param  ContainerBuilder $container
     * @param  array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->screenshotDirectory = str_replace(
            '%paths.base%',
            $container->getParameter('paths.base'),
            $config[self::CONFIG_PARAM_SCREENSHOT_DIRECTORY]
        );

        if ($config[self::CONFIG_PARAM_CLEAR_SCREENSHOT_DIRECTORY]) {
            $this->fileInfo = $this->fileInfo ?: new \finfo();
            $this->clearScreenshotDirectory();
        }
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
     * @return string
     */
    private function getDefaultDirectory()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::DEFAULT_DIRECTORY;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getTargetPath($fileName)
    {
        $path = rtrim($this->screenshotDirectory, DIRECTORY_SEPARATOR);
        return empty($path) ? $fileName : $path . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @param string $directory
     *
     * @throws IOException
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

    private function clearScreenshotDirectory()
    {
        if (!$this->filesystem->exists($this->getTargetPath(''))) {
            return;
        }

        $filesToDelete = [];

        /** @var SplFileInfo $file */
        foreach ($this->finder->files()->in($this->getTargetPath('')) as $file) {
            if (strpos($this->fileInfo->file($file->getRealPath(), FILEINFO_MIME_TYPE), 'image/') !== false) {
                $filesToDelete[] = $file->getRealPath();
            }
        }

        $this->filesystem->remove($filesToDelete);
    }
}
