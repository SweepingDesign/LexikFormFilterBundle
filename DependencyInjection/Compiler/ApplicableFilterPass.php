<?php

namespace Lexik\Bundle\FormFilterBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApplicableFilterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('lexik_form_filter.applicable_filter_aggregator')) {
            $definition = $container->getDefinition('lexik_form_filter.applicable_filter_aggregator');

            foreach ($container->findTaggedServiceIds('lexik_form_filter.applicable_filter') as $id => $attributes) {
                $definition->addMethodCall('add', array($id, new Reference($id)));
            }
        }
    }
}