<?php

namespace spec\Bex\Behat\ScreenshotExtension\Service;

use Bex\Behat\ScreenshotExtension\Driver\Local;
use Bex\Behat\ScreenshotExtension\ServiceContainer\Config;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;

class ScreenshotUploaderSpec extends ObjectBehavior
{
    function let(OutputInterface $output, Config $config, Local $localDriver)
    {
        $this->beConstructedWith($output, $config);

        $this->initializeOutputStub($output);
        $this->initializeConfigStub($config, $localDriver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Service\ScreenshotUploader');
    }

    function it_should_call_the_image_upload_with_correct_params(Local $localDriver)
    {
        $localDriver->upload('binary-image', 'test.png')->shouldBeCalled();

        $this->upload('binary-image', 'test.png');
    }

    function it_should_print_coloured_message_by_default(OutputInterface $output)
    {
        $output->writeln(Argument::containingString('<comment>'), OutputInterface::OUTPUT_NORMAL)->shouldBeCalled();

        $this->upload('binary-image', 'test.png');
    }

    function it_should_not_return_coloured_message_when_ansi_mode_is_disabled(OutputInterface $output)
    {
        $output->isDecorated()->willReturn(false);

        $output->writeln(Argument::type('string'), OutputInterface::OUTPUT_NORMAL)->shouldNotBeCalled();
        $output->writeln(Argument::type('string'), OutputInterface::OUTPUT_PLAIN)->shouldBeCalled();

        $this->upload('binary-image', 'test.png');
    }

    private function initializeOutputStub(OutputInterface $output)
    {
        $output->isDecorated()->willReturn(true);
        $output->writeln(Argument::type('string'), Argument::any())->willReturn(null);
    }

    private function initializeConfigStub($config, $localDriver)
    {
        $config->getImageDrivers()->willReturn([$localDriver]);
    }
}
