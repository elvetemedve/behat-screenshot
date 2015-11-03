<?php

namespace spec\Bex\Behat\ScreenshotExtension\Listener;

use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use Bex\Behat\ScreenshotExtension\Mink\ScreenshotTaker;
use Bex\Behat\ScreenshotExtension\String\StepFilenameGenerator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Unit test of the class ScreenshotListener
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotListenerSpec extends ObjectBehavior
{
    function let(ScreenshotTaker $screenshotTaker, StepFilenameGenerator $filenameGenerator)
    {
        $this->beConstructedWith($screenshotTaker, $filenameGenerator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Listener\ScreenshotListener');
    }

    function it_is_subscribed_for_after_step_event()
    {
        $this->getSubscribedEvents()->shouldHaveKeyWithValue('tester.step_tested.after', ['checkAfterStep']);
    }

    function it_does_not_take_screenshot_on_success_event(
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
        $result->getResultCode()->willReturn(TestResult::PASSED);
        $screenshotTaker->takeScreenshot(Argument::any())->shouldNotBeCalled();

        $this->checkAfterStep($event);
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
        $screenshotTaker->takeScreenshot(Argument::any())->shouldBeCalled();

        $this->checkAfterStep($event);
    }

    function it_generates_filename_from_step_name(
        ScreenshotTaker $screenshotTaker,
        StepFilenameGenerator $filenameGenerator,
        Environment $env,
        FeatureNode $feature,
        StepNode $step,
        StepResult $result,
        Teardown $tearDown
    )
    {
        $event = new AfterStepTested(
            $env->getWrappedObject(),
            $feature->getWrappedObject(),
            $step->getWrappedObject(),
            $result->getWrappedObject(),
            $tearDown->getWrappedObject()
        );
        $result->getResultCode()->willReturn(TestResult::FAILED);
        $filenameGenerator->convertStepToFileName($step)->willReturn('test.jpg')->shouldBeCalled();
        $screenshotTaker->takeScreenshot('test.jpg')->shouldBeCalled();

        $this->checkAfterStep($event);
    }
}
