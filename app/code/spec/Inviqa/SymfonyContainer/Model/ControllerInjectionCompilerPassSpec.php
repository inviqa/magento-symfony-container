<?php

namespace spec;

use Bridge\MageApp;
use Bridge\MageStore;
use Inviqa_SymfonyContainer_Model_ControllerInjectionCompilerPass as ControllerInjectionCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class Inviqa_SymfonyContainer_Model_ControllerInjectionCompilerPassSpec extends ObjectBehavior
{
    function let(ContainerBuilder $container)
    {
        $container->set(Argument::cetera())->shouldBeCalled();
    }

    function it_does_not_add_an_argument_to_service_def_if_tag_does_not_exist(ContainerBuilder $container)
    {
        $container->findTaggedServiceIds(ControllerInjectionCompilerPass::TAG_NAME)->willReturn([]);

        $container->findDefinition(Argument::any())->shouldNotBeCalled();

        $this->process($container);
    }


    function it_sets_services_to_array_of_controller_services_definitions(ContainerBuilder $container, Definition $definition1, Definition $definition2)
    {
        $container->findTaggedServiceIds(ControllerInjectionCompilerPass::TAG_NAME)->willReturn([
            'my.first.controller' => [ ControllerInjectionCompilerPass::TAG_NAME ],
            'my.second.controller' => [ ControllerInjectionCompilerPass::TAG_NAME ],
        ]);

        $controllersObject = (object) array(
            ControllerInjectionCompilerPass::CONTROLLERS_SERVICE_ID => array(
                'My_First_Controller' => [
                    0 => 'arg1',
                    1 => 'arg2'
                ],

                'My_Second_Controller' => [
                    0 => 'arg1'
                ]
            )
        );

        $definition1->getClass()->willReturn('My_First_Controller');
        $definition1->getArguments()->willReturn(['arg1', 'arg2']);
        $definition1->getArgument(0)->willReturn('arg1');
        $definition1->getArgument(1)->willReturn('arg2');

        $container->findDefinition('my.first.controller')->willReturn($definition1);


        $definition2->getClass()->willReturn('My_Second_Controller');
        $definition2->getArguments()->willReturn(['arg1']);
        $definition2->getArgument(0)->willReturn('arg1');

        $container->findDefinition('my.second.controller')->willReturn($definition2);

        $this->process($container);

        $container->set(ControllerInjectionCompilerPass::CONTROLLERS_SERVICE_ID, $controllersObject)->shouldHaveBeenCalled();

    }
}
