<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Mink;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Config;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This class is responsible for taking screenshot by using the Mink session
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotTaker
{
    /** @var Mink $mink */
    private $mink;

    /** @var OutputInterface $output */
    private $output;

    /**
     * @var array $screenshots
     */
    private $screenshots;

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @param Mink            $mink
     * @param OutputInterface $output
     * @param Config          $config
     */
    public function __construct(Mink $mink, OutputInterface $output, Config $config)
    {
        $this->mink = $mink;
        $this->output = $output;
        $this->config = $config;
        $this->screenshots = [];
    }

    /**
     * Save the screenshot into a local buffer
     */
    public function takeScreenshot()
    {
        try {
            if (!$this->mink->getSession()->isStarted()) {
                return;
            }
            $this->screenshots[] = $this->mink->getSession()->getScreenshot();
        } catch (UnsupportedDriverActionException $e) {
            return;
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
        }        
    }

    /**
     * @return boolean
     */
    public function hasScreenshot()
    {
        return !empty($this->screenshots);
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->config->shouldCombineImages() ? $this->getCombinedImage() : $this->getLastImage();
    }
    
    /**
     * @return string
     */
    private function getCombinedImage()
    {
        $im = new \Imagick();

        foreach ($this->screenshots as $screenshot) {
            $im->readImageBlob($screenshot);
        }

        /* Append the images into one */
        $im->resetIterator();
        $combined = $im->appendImages(true);

        /* Output the image */
        $combined->setImageFormat("png");

        return (string) $combined;
    }

    /**
     * @return string
     */
    private function getLastImage()
    {
        return end($this->screenshots);
    }

    /**
     * Remove previous images
     */
    public function reset()
    {
        $this->screenshots = [];
    }
}
