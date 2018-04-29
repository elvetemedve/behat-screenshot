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

class SuiteNameSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Placeholder\SuiteName');
    }

    function it_returns_the_placeholder()
    {
        $this->getKey()->shouldReturn('%SUITE_NAME%');
    }

    function it_returns_the_suite_name(
        Environment $env,
        FeatureNode $featureNode,
        ScenarioInterface $scenarioNode,
        TestResult $result,
        Teardown $teardown,
        Suite $suite
    ) {
        $event = new AfterScenarioTested(
            $env->getWrappedObject(),
            $featureNode->getWrappedObject(),
            $scenarioNode->getWrappedObject(),
            $result->getWrappedObject(),
            $teardown->getWrappedObject()
        );
        $env->getSuite()->willReturn($suite);

        $suite->getName()->shouldBeCalled()->willReturn('my_suite');

        $this->getValue($event)->shouldReturn('my_suite');
    }
}
