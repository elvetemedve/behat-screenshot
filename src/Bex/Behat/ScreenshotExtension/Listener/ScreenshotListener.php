<?php

namespace Bex\Behat\ScreenshotExtension\Listener;

use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Testwork\Tester\Result\TestResult;
use Bex\Behat\ScreenshotExtension\Mink\ScreenshotTaker;
use Bex\Behat\ScreenshotExtension\String\StepFilenameGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This class is responsible to decide when to make a screenshot
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
final class ScreenshotListener implements EventSubscriberInterface
{
    /** @var ScreenshotTaker $screenshotTaker */
    private $screenshotTaker;

    /** @var StepFilenameGenerator $filenameGenerator */
    private $filenameGenerator;

    /**
     * Constructor
     *
     * @param ScreenshotTaker $screenshotTaker
     * @param StepFilenameGenerator $filenameGenerator
     */
    public function __construct(ScreenshotTaker $screenshotTaker, StepFilenameGenerator $filenameGenerator)
    {
        $this->screenshotTaker = $screenshotTaker;
        $this->filenameGenerator = $filenameGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'tester.step_tested.after' => ['checkAfterStep']
        ];
    }

    /**
     * Take screenshot after a failed step
     *
     * @param AfterStepTested $event
     */
    public function checkAfterStep(AfterStepTested $event)
    {
        if ($event->getTestResult()->getResultCode() === TestResult::FAILED) {
            $stepFileName = $this->filenameGenerator->convertStepToFileName($event->getStep());
            $this->screenshotTaker->takeScreenshot($stepFileName);
        }
    }
}
