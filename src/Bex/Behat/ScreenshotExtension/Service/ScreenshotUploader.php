<?php


namespace Bex\Behat\ScreenshotExtension\Service;
use Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Short description about the class
 *
 */
class ScreenshotUploader
{
    /**
     * @var OutputInterface
     */
    private $output;

    /** @var ImageDriverInterface[] $imageDrivers */
    private $imageDrivers;

    public function __construct(OutputInterface $output, array $imageDrivers)
    {
        $this->output = $output;
        $this->imageDrivers = $imageDrivers;
    }

    public function upload($screenshot, $fileName = 'failure.png')
    {
        foreach ($this->imageDrivers as $imageDriver) {
            $imageUrl = $imageDriver->upload($screenshot, $fileName);
            $this->printImageLocation($imageUrl);
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