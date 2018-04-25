<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Unit test of the class StepFilenameGenerator
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class FilenameGeneratorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('base-path');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Service\FilenameGenerator');
    }

    function it_should_return_a_nice_filename(
        Environment $env,
        FeatureNode $featureNode,
        ScenarioInterface $scenarioNode,
        TestResult $result,
        Teardown $teardown
    ) {
        $featureNode->getFile()->willReturn('base-path/example.feature');
        $scenarioNode->getLine()->willReturn(42);

        $event = new AfterScenarioTested(
            $env->getWrappedObject(),
            $featureNode->getWrappedObject(),
            $scenarioNode->getWrappedObject(),
            $result->getWrappedObject(),
            $teardown->getWrappedObject()
        );

        $this->generateFileName($event)->shouldReturn('example_feature_42.png');
    }
}
