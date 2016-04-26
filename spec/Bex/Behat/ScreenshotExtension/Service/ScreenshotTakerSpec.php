<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use Behat\Mink\Mink;
use Behat\Mink\Session;
use Bex\Behat\ScreenshotExtension\Driver\Local;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Unit test of the class ScreenshotTaker
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotTakerSpec extends ObjectBehavior
{
    function let(Mink $mink, OutputInterface $output, Local $localImageDriver, Session $session)
    {
        $this->beConstructedWith($mink, $output, [$localImageDriver]);

        $this->initializeOutputStub($output);
        $this->initializeMinkStub($mink, $session);
        $this->initializeSessionStub($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Service\ScreenshotTaker');
    }

    function it_should_call_the_image_upload_with_correct_params(Local $localImageDriver)
    {
        $localImageDriver->upload('binary-image', 'test.png')->shouldBeCalled();

        $this->takeScreenshot('test.png');
    }

    function it_should_print_coloured_message_by_default(OutputInterface $output)
    {
        $output->writeln(Argument::containingString('<comment>'), OutputInterface::OUTPUT_NORMAL)->shouldBeCalled();

        $this->takeScreenshot('test.png');
    }

    function it_should_not_return_coloured_message_when_ansi_mode_is_disabled(OutputInterface $output)
    {
        $output->isDecorated()->willReturn(false);

        $output->writeln(Argument::type('string'), OutputInterface::OUTPUT_NORMAL)->shouldNotBeCalled();
        $output->writeln(Argument::type('string'), OutputInterface::OUTPUT_PLAIN)->shouldBeCalled();

        $this->takeScreenshot('test.png');
    }

    private function initializeOutputStub(OutputInterface $output)
    {
        $output->isDecorated()->willReturn(true);
        $output->writeln(Argument::type('string'), Argument::any())->willReturn(null);
    }

    private function initializeMinkStub(Mink $mink, Session $session)
    {
        $mink->getSession()->willReturn($session);
    }

    private function initializeSessionStub(Session $session)
    {
        $session->getScreenshot()->willReturn('binary-image');
    }
}
