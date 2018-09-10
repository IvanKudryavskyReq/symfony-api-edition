<?php

namespace AuthBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SocialProviderCompilerPass
 */
class SocialProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        $socialGrantExtension = $container->getDefinition('auth.grant_extension.social');

        $socialProviders = $container->findTaggedServiceIds('auth.social_provider');

        foreach ($socialProviders as $id => $tags) {
            $provider = $container->getDefinition($id);
            $socialGrantExtension->addMethodCall('addProvider', [$provider]);
        }
    }
}
