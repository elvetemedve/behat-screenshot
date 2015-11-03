<?php

namespace spec\Bex\Behat\ScreenshotExtension\Config;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Unit test of the class Parameters
 *
 * @license http://opensource.org/licenses/MIT The MIT License
 */
class ParametersSpec extends ObjectBehavior
{
    function let(ArrayNodeDefinition $builder, NodeBuilder $nodeBuilder)
    {
        $this->beConstructedWith(
            [
                'screenshot_directory' => '/tmp',
                'screenshot_directory_path_on_http' => '/foo/bar',
                'base_url' => 'https://server.org/',
            ]
        );

        $this->initBuilderDouble($builder, $nodeBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Config\Parameters');
    }

    function it_returns_screenshot_directory_in_local_filesystem()
    {
        $this->getScreenshotDirectory()->shouldReturn('/tmp');
    }

    function it_returns_directory_path_on_http()
    {
        $this->getScreenshotDirectoryPathOnHttp()->shouldReturn('/foo/bar');
    }

    function it_returns_base_url_used_by_mink_extension()
    {
        $this->getBaseUrl()->shouldReturn('https://server.org/');
    }

    function it_defines_available_configuration_parameters(ArrayNodeDefinition $builder, NodeBuilder $nodeBuilder)
    {
        $nodeBuilder->scalarNode('screenshot_directory')->willReturn($builder)->shouldBeCalled();
        $nodeBuilder->scalarNode('screenshot_directory_path_on_http')->willReturn($builder)->shouldBeCalled();

        $this::configure($builder);
    }

    private function initBuilderDouble(ArrayNodeDefinition $builder, NodeBuilder $nodeBuilder)
    {
        $builder->children()->willReturn($nodeBuilder);
        $nodeBuilder->end()->willReturn($nodeBuilder);
        $nodeBuilder->scalarNode(Argument::any())->willReturn($builder);
        $builder->defaultValue(Argument::any())->willReturn($nodeBuilder);
    }
}
