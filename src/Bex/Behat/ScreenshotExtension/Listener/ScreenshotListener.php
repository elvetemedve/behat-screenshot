<?php

namespace Bex\Behat\ScreenshotExtension\Listener;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Testwork\EventDispatcher\Event\AfterTested;
use Behat\Testwork\Tester\Result\TestResult;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Config;
use Bex\Behat\ScreenshotExtension\Service\FilenameGenerator;
use Bex\Behat\ScreenshotExtension\Service\ScreenshotTaker;
use Bex\Behat\ScreenshotExtension\Service\ScreenshotUploader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This class is responsible to decide when to make a screenshot
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
final class ScreenshotListener implements EventSubscriberInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScreenshotTaker
     */
    private $screenshotTaker;

    /**
     * @var FilenameGenerator
     */
    private $filenameGenerator;
    
    /**
     * @var ScreenshotUploader
     */
    private $screenshotUploader;

    /**
     * @param Config             $config
     * @param ScreenshotTaker    $screenshotTaker
     * @param FilenameGenerator  $filenameGenerator
     * @param ScreenshotUploader $screenshotUploader
     */
    public function __construct(
        Config $config,
        ScreenshotTaker $screenshotTaker, 
        FilenameGenerator $filenameGenerator, 
        ScreenshotUploader $screenshotUploader
    ) {
        $this->config = $config;
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
            StepTested::AFTER => 'takeScreenshot',
            ScenarioTested::AFTER => 'saveScreenshot',
            ExampleTested::AFTER => 'saveScreenshot',
        ];
    }

    /**
     * Take screenshot after each real (not skipped) step
     *
     * @param AfterStepTested $event
     */
    public function takeScreenshot(AfterStepTested $event)
    {
        if ($this->shouldTakeScreenshot($event)) {
            $this->screenshotTaker->takeScreenshot();

            if (!$this->config->shouldCombineImages()) {
                $fileName = $this->filenameGenerator->generateFileName($event->getFeature(), $event->getNode());
                $image = $this->screenshotTaker->getImage();
                $this->screenshotUploader->upload($image, $fileName);
                $this->screenshotTaker->reset();
            }
        }
    }
    
    /**
     * Save screenshot after scenario if required
     * 
     * @param  AfterScenarioTested $event
     */
    public function saveScreenshot(AfterScenarioTested $event)
    {
        if ($this->shouldSaveScreenshot($event)) {
            $fileName = $this->filenameGenerator->generateFileName($event->getFeature(), $event->getScenario());
            $image = $this->screenshotTaker->getImage();
            $this->screenshotUploader->upload($image, $fileName);
        }

        $this->screenshotTaker->reset();
    }

    /**
     * @param  AfterTested $event
     *
     * @return boolean
     */
    private function shouldTakeScreenshot(AfterTested $event)
    {
        $isScenarioFailed = $event->getTestResult()->getResultCode() === TestResult::FAILED;
        $shouldRecordAllScenarios = $this->config->shouldRecordAllScenarios();

        return $isScenarioFailed || $shouldRecordAllScenarios;
    }

    /**
     * @param  AfterTested $event
     *
     * @return boolean
     */
    private function shouldSaveScreenshot(AfterTested $event)
    {
        $hasScreenshot = $this->screenshotTaker->hasScreenshot();
        $isScenarioFailed = $event->getTestResult()->getResultCode() === TestResult::FAILED;
        $shouldRecordAllScenarios = $this->config->shouldRecordAllScenarios();

        return $hasScreenshot && ($isScenarioFailed || $shouldRecordAllScenarios);
    }
}
