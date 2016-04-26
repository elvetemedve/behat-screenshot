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

    /** @var ImageDriverInterface[] $imageDrivers */
    private $imageDrivers;

    /**
     * Constructor
     *
     * @param Mink $mink
     * @param OutputInterface $output
     * @param ImageDriverInterface[] $imageDrivers
     */
    public function __construct(Mink $mink, OutputInterface $output, array $imageDrivers)
    {
        $this->mink = $mink;
        $this->output = $output;
        $this->imageDrivers = $imageDrivers;
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
                $this->printImageLocation($imageUrl);
            }
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
        }        
    }

    /**
     * @param string $imageUrl
     */
    private function printImageLocation($imageUrl)
    {
        $message = sprintf(
            '<comment>Screenshot has been taken. Open image at <error>%s</error></comment>',
            $imageUrl
        );
        $options = $this->output->isDecorated() ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_PLAIN;
        
        $this->output->writeln($message, $options);
    }
}
