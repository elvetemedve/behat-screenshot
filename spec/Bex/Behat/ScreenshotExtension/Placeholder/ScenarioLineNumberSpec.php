<?php

namespace spec\Bex\Behat\ScreenshotExtension\Placeholder;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use PhpSpec\ObjectBehavior;

class ScenarioLineNumberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Placeholder\ScenarioLineNumber');
    }

    function it_returns_the_placeholder()
    {
        $this->getKey()->shouldReturn('%SCENARIO_LINE_NUMBER%');
    }

    function it_returns_the_relative_feature_file_path(
        Environment $env,
        FeatureNode $featureNode,
        ScenarioInterface $scenarioNode,
        TestResult $result,
        Teardown $teardown
    ) {
        $event = new AfterScenarioTested(
            $env->getWrappedObject(),
            $featureNode->getWrappedObject(),
            $scenarioNode->getWrappedObject(),
            $result->getWrappedObject(),
            $teardown->getWrappedObject()
        );

        $scenarioNode->getLine()->willReturn(12);

        $this->getValue($event)->shouldReturn('12');
    }
}
