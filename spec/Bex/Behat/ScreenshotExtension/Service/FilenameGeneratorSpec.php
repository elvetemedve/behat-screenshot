<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
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

    function it_should_return_a_nice_filename(FeatureNode $featureNode, ScenarioInterface $scenarioNode)
    {
        $featureNode->getFile()->willReturn('base-path/example.feature');
        $scenarioNode->getLine()->willReturn(42);

        $this->generateFileName($featureNode, $scenarioNode)->shouldReturn('example_feature_42.png');
    }
}
