<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class Local implements ImageDriverInterface
{
    const DEFAULT_DIRECTORY = 'behat-screenshot';
    const CONFIG_PARAM_SCREENSHOT_DIRECTORY = 'screenshot_directory';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $screenshotDirectory;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem = null)
    {
        $this->filesystem = $filesystem ?: new Filesystem();
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
            ->end();
    }

    /**
     * @param  ContainerBuilder $container
     * @param  array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->screenshotDirectory = $config[self::CONFIG_PARAM_SCREENSHOT_DIRECTORY];
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
}