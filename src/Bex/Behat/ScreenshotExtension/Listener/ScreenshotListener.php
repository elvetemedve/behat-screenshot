<?php

namespace Bex\Behat\ScreenshotExtension\Listener;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Testwork\Tester\Result\TestResult;
use Bex\Behat\ScreenshotExtension\Service\ScreenshotTaker;
use Bex\Behat\ScreenshotExtension\Service\ScreenshotUploader;
use Bex\Behat\ScreenshotExtension\Service\StepFilenameGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This class is responsible to decide when to make a screenshot
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
final class ScreenshotListener implements EventSubscriberInterface
{
    /**
     * @var ScreenshotTaker
     */
    private $screenshotTaker;

    /**
     * @var StepFilenameGenerator
     */
    private $filenameGenerator;
    
    /**
     * @var ScreenshotUploader
     */
    private $screenshotUploader;

    /**
     * Constructor
     *
     * @param ScreenshotTaker $screenshotTaker
     * @param StepFilenameGenerator $filenameGenerator
     * @param ScreenshotUploader $screenshotUploader
     */
    public function __construct(
        ScreenshotTaker $screenshotTaker, 
        StepFilenameGenerator $filenameGenerator, 
        ScreenshotUploader $screenshotUploader
    ) {
        $this->screenshotTaker = $screenshotTaker;
        $this->filenameGenerator = $filenameGenerator;
        $this->screenshotUploader = $screenshotUploader;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            StepTested::AFTER => 'checkAfterStep',
            ScenarioTested::AFTER => 'cleanupAfterScenario',
        ];
    }

    /**
     * Take screenshot after a failed step
     *
     * @param AfterStepTested $event
     */
    public function checkAfterStep(AfterStepTested $event)
    {
        $this->screenshotTaker->takeScreenshot();
        if ($event->getTestResult()->getResultCode() === TestResult::FAILED) {
            $stepFileName = $this->filenameGenerator->convertStepToFileName($event->getStep());
            $image = $this->screenshotTaker->getImage();
            $this->screenshotUploader->upload($image, $stepFileName);
        }
    }
    
    public function cleanupAfterScenario()
    {
        $this->screenshotTaker->reset();
    }
}
