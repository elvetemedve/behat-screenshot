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

class FeatureFilePathSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('base-path');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Placeholder\FeatureFilePath');
    }

    function it_returns_the_placeholder()
    {
        $this->getKey()->shouldReturn('%FEATURE_FILE_PATH%');
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

        $featureNode->getFile()->willReturn('base-path/a/b');

        $this->getValue($event)->shouldReturn('a/b');
    }
}
