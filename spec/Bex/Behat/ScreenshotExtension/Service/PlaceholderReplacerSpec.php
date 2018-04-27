<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use Bex\Behat\ScreenshotExtension\Service\PlaceholderReplacer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Unit test of the class PlaceholderReplacer
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class PlaceholderReplacerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('base-path');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Service\PlaceholderReplacer');
    }

    function it_repleaces_suite_placeholder_with_suite_name(
        Environment $env,
        FeatureNode $featureNode,
        ScenarioInterface $scenarioNode,
        TestResult $result,
        Teardown $teardown,
        Suite $suite
    ) {
        $env->getSuite()->willReturn($suite);
        $suite->getName()->willReturn('default');

        $this->replacePlaceholders(
            'fix_%SUITE%_thing',
            $this->createEvent($env, $featureNode, $scenarioNode, $result, $teardown)
        )->shouldReturn('fix_default_thing');
    }

    private function createEvent(
        Environment $env,
        FeatureNode $featureNode,
        ScenarioInterface $scenarioNode,
        TestResult $result,
        Teardown $teardown
    ) {
        return new AfterScenarioTested(
            $env->getWrappedObject(),
            $featureNode->getWrappedObject(),
            $scenarioNode->getWrappedObject(),
            $result->getWrappedObject(),
            $teardown->getWrappedObject()
        );
    }
}
