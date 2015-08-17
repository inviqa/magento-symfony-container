<?php

namespace spec;

use ContainerTools\Configuration;
use Symfony\Component\DependencyInjection\Container;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Inviqa_SymfonyContainer_Helper_ContainerProviderSpec extends ObjectBehavior
{
    function let(Configuration $generatorConfig)
    {
        $generatorConfig->getContainerFilePath()->willReturn('container.php');
        $generatorConfig->getDebug()->willReturn(true);
        $generatorConfig->getServicesFolders()->willReturn(['app/etc']);
        $generatorConfig->getServicesFormat()->willReturn('xml');
        $generatorConfig->getCompilerPasses()->willReturn([]);

        $services = [
            'generatorConfig' => $generatorConfig
        ];

        $this->beConstructedWith($services);
    }

    function it_generates_container(Configuration $generatorConfig)
    {
        $generatorConfig->addCompilerPass(Argument::any())->shouldBeCalled();

        $this->getContainer()->shouldBeAnInstanceOf(Container::class);
    }

    function it_memoizes_container(Configuration $generatorConfig)
    {
        $generatorConfig->addCompilerPass(Argument::any())->shouldBeCalledTimes(1);

        $container = $this->getContainer();
        $this->getContainer()->shouldBe($container);
    }
}
