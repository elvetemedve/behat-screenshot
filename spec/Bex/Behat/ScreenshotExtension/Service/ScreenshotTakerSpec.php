<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use Behat\Mink\Mink;
use Behat\Mink\Session;
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
    function let(Mink $mink, OutputInterface $output, Session $session)
    {
        $this->beConstructedWith($mink, $output);

        $this->initializeOutputStub($output);
        $this->initializeMinkStub($mink, $session);
        $this->initializeSessionStub($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Service\ScreenshotTaker');
    }

    function it_takes_screenshot(Session $session)
    {
        $session->getScreenshot()->shouldBeCalled();

        $this->takeScreenshot();
    }

    function it_reports_errors_on_screen(OutputInterface $output, Session $session)
    {
        $output->writeln(Argument::cetera())->shouldBeCalled();

        $session->getScreenshot()->willThrow(new \Exception());

        $this->takeScreenshot();
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
