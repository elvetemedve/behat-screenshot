<?php

namespace spec\Bex\Behat\ScreenshotExtension\Listener;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use Bex\Behat\ScreenshotExtension\Service\FilenameGenerator;
use Bex\Behat\ScreenshotExtension\Service\ScreenshotTaker;
use Bex\Behat\ScreenshotExtension\Service\ScreenshotUploader;
use Bex\Behat\ScreenshotExtension\Service\StepFilenameGenerator;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Config;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Unit test of the class ScreenshotListener
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotListenerSpec extends ObjectBehavior
{
    function let(
        Config $config,
        ScreenshotTaker $screenshotTaker,
        FilenameGenerator $filenameGenerator,
        ScreenshotUploader $screenshotUploader
    ) {
        $this->beConstructedWith($config, $screenshotTaker, $filenameGenerator, $screenshotUploader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Listener\ScreenshotListener');
    }

    function it_is_subscribed_for_after_step_event()
    {
        $this->getSubscribedEvents()->shouldHaveKeyWithValue('tester.step_tested.after', 'takeScreenshot');
    }

    function it_is_subscribed_for_after_scenario_event()
    {
        $this->getSubscribedEvents()->shouldHaveKeyWithValue('tester.scenario_tested.after', 'saveScreenshot');
    }

    function it_is_subscribed_for_after_example_event()
    {
        $this->getSubscribedEvents()->shouldHaveKeyWithValue('tester.example_tested.after', 'saveScreenshot');
    }

    function it_takes_a_screenshot_after_a_failed_step(
        ScreenshotTaker $screenshotTaker,
        Environment $env,
        FeatureNode $feature,
        StepNode $step,
        StepResult $result,
        Teardown $tearDown
    ) {
        $event = new AfterStepTested(
            $env->getWrappedObject(),
            $feature->getWrappedObject(),
            $step->getWrappedObject(),
            $result->getWrappedObject(),
            $tearDown->getWrappedObject()
        );
        $result->getResultCode()->willReturn(TestResult::FAILED);
        $screenshotTaker->takeScreenshot()->shouldBeCalled();

        $this->takeScreenshot($event);
    }

    function it_takes_a_screenshot_after_a_passed_step(
        Config $config,
        ScreenshotTaker $screenshotTaker,
        Environment $env,
        FeatureNode $feature,
        StepNode $step,
        StepResult $result,
        Teardown $tearDown
    ) {
        $event = new AfterStepTested(
            $env->getWrappedObject(),
            $feature->getWrappedObject(),
            $step->getWrappedObject(),
            $result->getWrappedObject(),
            $tearDown->getWrappedObject()
        );
        $config->shouldRecordAllSteps()->willReturn(true);
        $result->getResultCode()->willReturn(TestResult::PASSED);
        $screenshotTaker->takeScreenshot()->shouldBeCalled();

        $this->takeScreenshot($event);
    }

    function it_does_not_take_a_screenshot_after_a_passed_step_if_not_enabled(
        Config $config,
        ScreenshotTaker $screenshotTaker,
        Environment $env,
        FeatureNode $feature,
        StepNode $step,
        StepResult $result,
        Teardown $tearDown
    ) {
        $event = new AfterStepTested(
            $env->getWrappedObject(),
            $feature->getWrappedObject(),
            $step->getWrappedObject(),
            $result->getWrappedObject(),
            $tearDown->getWrappedObject()
        );
        $config->shouldRecordAllSteps()->willReturn(false);
        $result->getResultCode()->willReturn(TestResult::PASSED);
        $screenshotTaker->takeScreenshot()->shouldNotBeCalled();

        $this->takeScreenshot($event);
    }

    function it_does_not_take_a_screenshot_after_a_skipped_step(
        ScreenshotTaker $screenshotTaker,
        Environment $env,
        FeatureNode $feature,
        StepNode $step,
        StepResult $result,
        Teardown $tearDown
    ) {
        $event = new AfterStepTested(
            $env->getWrappedObject(),
            $feature->getWrappedObject(),
            $step->getWrappedObject(),
            $result->getWrappedObject(),
            $tearDown->getWrappedObject()
        );
        $result->getResultCode()->willReturn(TestResult::SKIPPED);
        $screenshotTaker->takeScreenshot()->shouldNotBeCalled();

        $this->takeScreenshot($event);
    }

    function it_generates_filename_and_saves_screenshot(
        ScreenshotTaker $screenshotTaker,
        ScreenshotUploader $screenshotUploader,
        FilenameGenerator $filenameGenerator,
        Environment $env,
        FeatureNode $feature,
        ScenarioInterface $scenario,
        TestResult $result,
        Teardown $tearDown
    ) {
        $event = new AfterScenarioTested(
            $env->getWrappedObject(),
            $feature->getWrappedObject(),
            $scenario->getWrappedObject(),
            $result->getWrappedObject(),
            $tearDown->getWrappedObject()
        );
        $result->getResultCode()->willReturn(TestResult::FAILED);
        $filenameGenerator->generateFileName($feature, $scenario)->willReturn('test.jpg')->shouldBeCalled();
        $screenshotTaker->hasScreenshot()->willReturn(true)->shouldBeCalled();
        $screenshotTaker->reset()->willReturn(null)->shouldBeCalled();
        $screenshotTaker->getImage()->willReturn(null)->shouldBeCalled();
        $screenshotUploader->upload(Argument::any(), 'test.jpg')->shouldBeCalled();

        $this->saveScreenshot($event);
    }

    function it_does_not_save_screenshot_if_there_isnt_any(
        ScreenshotTaker $screenshotTaker,
        ScreenshotUploader $screenshotUploader,
        FilenameGenerator $filenameGenerator,
        Environment $env,
        FeatureNode $feature,
        ScenarioInterface $scenario,
        TestResult $result,
        Teardown $tearDown
    ) {
        $event = new AfterScenarioTested(
            $env->getWrappedObject(),
            $feature->getWrappedObject(),
            $scenario->getWrappedObject(),
            $result->getWrappedObject(),
            $tearDown->getWrappedObject()
        );
        $result->getResultCode()->willReturn(TestResult::FAILED);
        $screenshotTaker->hasScreenshot()->willReturn(false)->shouldBeCalled();
        $filenameGenerator->generateFileName($feature, $scenario)->shouldNotBeCalled();
        $screenshotTaker->getImage()->shouldNotBeCalled();
        $screenshotUploader->upload(Argument::any(), Argument::any())->shouldNotBeCalled();
        $screenshotTaker->reset()->willReturn(null)->shouldBeCalled();

        $this->saveScreenshot($event);
    }
}
