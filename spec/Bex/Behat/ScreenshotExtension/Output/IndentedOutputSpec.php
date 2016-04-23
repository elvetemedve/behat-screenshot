<?php

namespace spec\Bex\Behat\ScreenshotExtension\Output;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;

class IndentedOutputSpec extends ObjectBehavior
{
    function it_indents_message_with_six_space_by_default(OutputInterface $decoratedOutput)
    {
        $this->beConstructedWith($decoratedOutput);

        $decoratedOutput->write('      Hello!', Argument::cetera())->shouldBeCalled();
        $decoratedOutput->writeln('      Hello!', Argument::cetera())->shouldBeCalled();

        $this->write('Hello!');
        $this->writeln('Hello!');
    }

    function it_indents_message_with_custom_levels_and_character(OutputInterface $decoratedOutput)
    {
        $this->beConstructedWith($decoratedOutput, 2, '-');

        $decoratedOutput->write('----Hello!', Argument::cetera())->shouldBeCalled();
        $decoratedOutput->writeln('----Hello!', Argument::cetera())->shouldBeCalled();

        $this->write('Hello!');
        $this->writeln('Hello!');
    }
}
