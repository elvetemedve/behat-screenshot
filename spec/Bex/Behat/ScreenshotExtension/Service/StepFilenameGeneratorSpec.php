<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use Behat\Gherkin\Node\StepNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Unit test of the class StepFilenameGenerator
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class StepFilenameGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Service\StepFilenameGenerator');
    }

    function it_generates_a_sanitized_filename_for_a_scenario_step(StepNode $step)
    {
        $step->getText()->willReturn('When I 1st click the link wi-fi on "fsf.org".');
        $this->convertStepToFileName($step)->shouldReturn('when_i_1st_click_the_link_wi-fi_on__fsf_org__.png');
    }
}
