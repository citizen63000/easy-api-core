<?php

namespace EasyApiCore\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EasyApiCoreExtension extends Extension
{
    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Load configuration
        $config = (new Processor())->processConfiguration(new Configuration(), $configs);

        // Convert config as parameters
        $this->loadParametersFromConfiguration($config, $container);
    }

    protected function loadParametersFromConfiguration(array $loadedConfig, ContainerBuilder $container, string $parentKey = 'easy_api_maker'): void
    {
        foreach ($loadedConfig as $parameter => $value) {
            if (is_array($value)) {
                $this->loadParametersFromConfiguration($value, $container, "{$parentKey}.{$parameter}");
            } else {
                $container->setParameter("{$parentKey}.{$parameter}", $value);
            }
        }
    }
}