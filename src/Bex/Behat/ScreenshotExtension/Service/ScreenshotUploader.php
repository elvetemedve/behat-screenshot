<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Config;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Bex\Behat\ScreenshotExtension\Event\ScreenshotUploadCompleteEvent;

class ScreenshotUploader
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     *   Used to statically store the list of files uploaded.
     */
    private static $imageUrls;

    /**
     * @param OutputInterface $output
     * @param Config          $config
     */
    public function __construct(OutputInterface $output, Config $config)
    {
      $this->output = $output;
      $this->config = $config;
    }

    /**
     * Return a list of uploaded files.
     *
     * @return array
     *   The list of image Urls.
     */
    public function getImages() {
      return static::$imageUrls;
    }

    /**
     * @param  string $screenshot
     * @param  string $fileName
     */
    public function upload($screenshot, $fileName = 'failure.png')
    {
        foreach ($this->config->getImageDrivers() as $imageDriver) {
            $imageUrl = $imageDriver->upload($screenshot, $fileName);

            static::$imageUrls[] = $imageUrl;

            $this->printImageLocation($imageUrl);
        }
    }

    /**
     * @param string $imageUrl
     */
    private function printImageLocation($imageUrl)
    {
        $this->output->writeln(
            sprintf('<comment>Screenshot has been taken. Open image at <error>%s</error></comment>', $imageUrl),
            $this->output->isDecorated() ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_PLAIN
        );
    }
}
