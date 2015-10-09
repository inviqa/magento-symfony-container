<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Container;

use Inviqa_SymfonyContainer_Model_InjectableCompilerPass as InjectableCompilerPass;

class Inviqa_SymfonyContainer_Model_ServiceInjectorSpec extends ObjectBehavior
{
    private $referenceToControllerWithoutDependenciesMethod;

    private $referenceToControllerWithDependenciesMethod;

    private $referenceWithoutController;

    function let(Container $container)
    {
        $this->referenceWithoutController = (object) [
            'references' => []
        ];

        $this->referenceToControllerWithoutDependenciesMethod = (object) [
            'references' => [
                'spec\Acme_Catalog1_IndexController' => ['acme.product.catalog']
            ]
        ];

        $this->referenceToControllerWithDependenciesMethod = (object) [
            'references' => [
                'spec\Acme_Catalog2_IndexController' => ['acme.product.catalog']
            ]
        ];

        $this->beConstructedWith(['container' => $container]);
    }

    function it_does_not_set_up_dependencies_if_injectables_services_doesnt_contain_requested_class(Container $container)
    {
        $container->get(InjectableCompilerPass::INJECTABLES_SERVICE_ID)->willReturn(
            $this->referenceWithoutController
        );

        $class = new Acme_Catalog1_IndexController();

        $container->get('acme.product.catalog')->shouldNotBeCalled();

        $this->setupDependencies($class);
    }

    function it_does_not_set_up_dependencies_if_class_does_not_contain_dependencies_method(Container $container)
    {
        $container->get(InjectableCompilerPass::INJECTABLES_SERVICE_ID)->willReturn(
            $this->referenceToControllerWithoutDependenciesMethod
        );

        $class = new Acme_Catalog1_IndexController();

        $container->get('acme.product.catalog')->shouldNotBeCalled();

        $this->setupDependencies($class);
    }

    function it_sets_up_dependencies_if_class_does_contains_dependencies_method(Container $container)
    {
        $container->get(InjectableCompilerPass::INJECTABLES_SERVICE_ID)->willReturn(
            $this->referenceToControllerWithDependenciesMethod
        );

        $class = new Acme_Catalog2_IndexController();

        $container->get('acme.product.catalog')->shouldBeCalled();

        $this->setupDependencies($class);
    }
}

class Acme_Catalog1_IndexController
{

}

class Acme_Catalog2_IndexController
{
    public function __dependencies()
    {
    }
}