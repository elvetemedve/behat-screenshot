<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Config;
use Bex\Behat\ScreenshotExtension\Service\PlaceholderReplacer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Unit test of the class FilenameGenerator
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class FilenameGeneratorSpec extends ObjectBehavior
{
    function let(Config $config, PlaceholderReplacer $placeholderReplacer)
    {
        $this->beConstructedWith($config, $placeholderReplacer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Service\FilenameGenerator');
    }

    function it_should_return_a_nice_filename(
        Config $config,
        PlaceholderReplacer $placeholderReplacer,
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

        $filePattern = '%FEATURE_FILE_PATH%_%SCENARIO_LINE_NUMBER%';
        $config->getScreenshotFilenamePattern()->willReturn($filePattern);
        $placeholderReplacer->replaceAll($filePattern, $event)->willReturn('example_feature_42');

        $this->generateFileName($event)->shouldReturn('example_feature_42.png');
    }
}
