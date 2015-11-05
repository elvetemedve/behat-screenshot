<?php

namespace spec\Bex\Behat\ScreenshotExtension\Mink;

use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Bex\Behat\ScreenshotExtension\Config\Parameters;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Unit test of the class ScreenshotTaker
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ScreenshotTakerSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem, Parameters $parameters, Mink $mink, OutputPrinter $output, Session $session)
    {
        $this->beConstructedWith($filesystem, $parameters, $mink, $output);

        $this->initMinkDouble($mink, $session);
        $this->initFilesystemDouble($filesystem);
        $this->initParametersDouble($parameters);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Mink\ScreenshotTaker');
    }

    function it_saves_screenshot_into_a_file(Filesystem $filesystem)
    {
        $filesystem->dumpFile(Argument::containingString('test.jpg'), 'binary-image')->shouldBeCalled();

        $this->takeScreenshot('test.jpg');
    }

    function it_saves_screenshot_into_the_configured_directory(Filesystem $filesystem, Parameters $parameters)
    {
        $parameters->getActiveImageDriver()->willReturn('/foo/bar/');
        $filesystem->dumpFile('/foo/bar/test.jpg', Argument::any())->shouldBeCalled();

        $this->takeScreenshot('test.jpg');
    }

    function it_creates_non_existent_directories(Filesystem $filesystem, Parameters $parameters)
    {
        $parameters->getActiveImageDriver()->willReturn('/foo/bar/baz/');
        $filesystem->exists('/foo/bar/baz')->willReturn(false);
        $filesystem->mkdir('/foo/bar/baz', Argument::any())->shouldBeCalled();

        $this->takeScreenshot(Argument::any());
    }

    function it_prints_the_path_to_the_saved_file_on_screen(OutputPrinter $output)
    {
        $output->writeln('Screenshot has been taken. Open image at /tmp/test2.jpg')->shouldBeCalled();

        $this->takeScreenshot('test2.jpg');
    }

    private function initMinkDouble(Mink $mink, Session $session)
    {
        $mink->getSession()->willReturn($session);
        $session->getScreenshot()->willReturn('binary-image');
    }

    private function initFilesystemDouble(Filesystem $filesystem)
    {
        $filesystem->exists(Argument::any())->willReturn(true);
        $filesystem->mkdir(Argument::type('string'), Argument::any())->willReturn(null);
        $filesystem->dumpFile(Argument::type('string'), Argument::any())->willReturn(null);
    }

    private function initParametersDouble(Parameters $parameters)
    {
        $parameters->getActiveImageDriver()->willReturn('/tmp');
        $parameters->getScreenshotDirectoryPathOnHttp()->willReturn('');
    }
}
