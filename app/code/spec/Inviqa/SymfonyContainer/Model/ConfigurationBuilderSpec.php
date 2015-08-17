<?php

namespace spec;

use ContainerTools\Configuration;
use Inviqa_SymfonyContainer_Model_ConfigurationBuilder;

use Bridge\MageApp;
use Bridge\MageConfig;
use Bridge\MageConfigNode;
use Bridge\MageConfigOptions;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Inviqa_SymfonyContainer_Model_ConfigurationBuilderSpec extends ObjectBehavior
{
    function let(MageApp $app, MageConfig $config, MageConfigOptions $configOptions, MageConfigNode $configNode)
    {
        $configNode->children()->willReturn([]);
        $configOptions->getEtcDir()->willReturn('app/etc');


        $config->getOptions()->willReturn($configOptions);
        $config->getNode('modules')->willReturn($configNode);

        $services = [
            'app' => $app,
            'config' => $config,
            'baseDir' => 'cache'
        ];

        $this->beConstructedWith($services);
    }

    function it_builds_a_generator_configuration_using_mage_cache(MageApp $app)
    {
        $app->useCache(\Inviqa_SymfonyContainer_Model_ConfigurationBuilder::MODEL_ALIAS)->willReturn(true);
        $configuration = $this->build();

        $configuration->getDebug()->shouldBe(false);
    }


    function it_builds_a_generator_configuration_without_mage_cache(MageApp $app)
    {
        $app->useCache(\Inviqa_SymfonyContainer_Model_ConfigurationBuilder::MODEL_ALIAS)->willReturn(false);
        $configuration = $this->build();

        $configuration->getDebug()->shouldBe(true);
    }

    function it_creates_configuraiton_with_only_main_etc_folder_by_default()
    {
        $configuration = $this->build();

        $configuration->getServicesFolders()->shouldBe(['app/etc']);
    }

    function it_creates_configuration_with_additional_module_etc_folders_if_active(MageConfigNode $configNode, MageConfig $config)
    {
        $configNode->children()->willReturn([
            'module1' => (object)[
                'active' => true
            ],
            'module2' => (object)[
                'active' => true
            ]
        ]);

        $config->getModuleDir('etc', 'module1')->willReturn('app/module1/etc');
        $config->getModuleDir('etc', 'module2')->willReturn('app/module2/etc');

        $configuration = $this->build();

        $configuration->getServicesFolders()->shouldBe(['app/etc', 'app/module1/etc', 'app/module2/etc']);
    }

    function it_creates_configuration_omitting_inactive_modules(MageConfigNode $configNode, MageConfig $config)
    {
        $configNode->children()->willReturn([
            'module1' => (object)[
                'active' => true
            ],
            'module2' => (object)[
                'active' => false
            ]
        ]);

        $config->getModuleDir('etc', 'module1')->willReturn('app/module1/etc');

        $configuration = $this->build();

        $configuration->getServicesFolders()->shouldBe(['app/etc', 'app/module1/etc']);
    }
}
