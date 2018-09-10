<?php

namespace AuthBundle;

use AuthBundle\DependencyInjection\Compiler\OverrideServiceCompilerPass;
use AuthBundle\DependencyInjection\Compiler\SocialProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AuthBundle.
 */
class AuthBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new OverrideServiceCompilerPass());
        $container->addCompilerPass(new SocialProviderCompilerPass());
    }
}
