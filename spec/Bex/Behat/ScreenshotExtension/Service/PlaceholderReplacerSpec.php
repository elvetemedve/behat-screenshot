<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use Bex\Behat\ScreenshotExtension\Placeholder\PlaceholderInterface;
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
    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Service\PlaceholderReplacer');
    }

    function it_replaces_all_placeholder(
        Environment $env,
        FeatureNode $featureNode,
        ScenarioInterface $scenarioNode,
        TestResult $result,
        Teardown $teardown,
        Suite $suite,
        PlaceholderInterface $fooPlaceholder,
        PlaceholderInterface $barPlaceholder
    ) {
        $this->beConstructedWith([$fooPlaceholder, $barPlaceholder]);

        $text = 'lorem_%FOO%_ipsum_%BAR%';
        $event = new AfterScenarioTested(
            $env->getWrappedObject(),
            $featureNode->getWrappedObject(),
            $scenarioNode->getWrappedObject(),
            $result->getWrappedObject(),
            $teardown->getWrappedObject()
        );

        $fooPlaceholder->getKey()->willReturn('%FOO%');
        $fooPlaceholder->getValue($event)->willReturn('x');

        $barPlaceholder->getKey()->willReturn('%BAR%');
        $barPlaceholder->getValue($event)->willReturn('y');

        $this->replaceAll($text, $event)->shouldReturn('lorem_x_ipsum_y');
    }
}
