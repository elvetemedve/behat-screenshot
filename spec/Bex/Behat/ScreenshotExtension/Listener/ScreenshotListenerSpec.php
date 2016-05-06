<?php

namespace spec\Bex\Behat\ScreenshotExtension\Listener;

use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use Bex\Behat\ScreenshotExtension\Service\ScreenshotTaker;
use Bex\Behat\ScreenshotExtension\Service\ScreenshotUploader;
use Bex\Behat\ScreenshotExtension\Service\StepFilenameGenerator;
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
        ScreenshotTaker $screenshotTaker,
        StepFilenameGenerator $filenameGenerator,
        ScreenshotUploader $screenshotUploader
    ) {
        $this->beConstructedWith($screenshotTaker, $filenameGenerator, $screenshotUploader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Listener\ScreenshotListener');
    }

    function it_is_subscribed_for_after_step_event()
    {
        $this->getSubscribedEvents()->shouldHaveKeyWithValue('tester.step_tested.after', 'checkAfterStep');
    }

    function it_does_not_take_screenshot_on_success_event(
        ScreenshotUploader $screenshotUploader,
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
        $screenshotUploader->upload(Argument::cetera())->shouldNotBeCalled();

        $this->checkAfterStep($event);
    }

    function it_takes_a_screenshot_after_a_failed_step(
        ScreenshotTaker $screenshotTaker,
        ScreenshotUploader $screenshotUploader,
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
        $screenshotTaker->getCombinedImage()->shouldBeCalled();
        $screenshotUploader->upload(Argument::cetera())->shouldBeCalled();

        $this->checkAfterStep($event);
    }

    function it_generates_filename_from_step_name(
        ScreenshotTaker $screenshotTaker,
        ScreenshotUploader $screenshotUploader,
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
        $screenshotTaker->takeScreenshot()->shouldBeCalled();
        $screenshotTaker->getCombinedImage()->shouldBeCalled();
        $screenshotUploader->upload(Argument::cetera())->shouldBeCalled();

        $this->checkAfterStep($event);
    }
}
