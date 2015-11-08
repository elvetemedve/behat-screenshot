<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Bex\Behat\ScreenshotExtension\Driver\Local;

/**
 * Unit test of the class ScreenshotTaker
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotTakerSpec extends ObjectBehavior
{
    function let(Mink $mink, OutputPrinter $output, Local $localImageDriver)
    {
        $this->beConstructedWith($mink, $output, [$localImageDriver]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Service\ScreenshotTaker');
    }

    function it_should_call_the_image_upload_with_correct_params(Mink $mink, Session $session, Local $localImageDriver)
    {
        $mink->getSession()->willReturn($session);
        $session->getScreenshot()->willReturn('binary-image');
        $localImageDriver->upload('binary-image', 'test.png')->shouldBeCalled();

        $this->takeScreenshot('test.png');
    }
}
