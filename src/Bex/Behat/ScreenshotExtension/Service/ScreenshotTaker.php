<?php

namespace Bex\Behat\ScreenshotExtension\Service;

use Behat\Mink\Mink;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
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
     * @var boolean $recordAllSteps
     */
    private $recordAllSteps;

    /**
     * Constructor
     *
     * @param Mink $mink
     * @param OutputInterface $output
     * @param boolean $recordAllSteps
     */
    public function __construct(Mink $mink, OutputInterface $output, $recordAllSteps)
    {
        $this->mink = $mink;
        $this->output = $output;
        $this->recordAllSteps = $recordAllSteps;
    }

    /**
     * Save the screenshot as the given filename
     */
    public function takeScreenshot()
    {
        try {
            $this->screenshots[] = $this->mink->getSession()->getScreenshot();
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
        }        
    }

    public function getImage()
    {
        return $this->recordAllSteps ? $this->getCombinedImage() : $this->getLastImage();
    }
    
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

        return (string)$combined;
    }

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
