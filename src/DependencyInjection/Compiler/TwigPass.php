<?php


namespace Survos\BootstrapBundle\DependencyInjection\Compiler;

use Symfony\Bundle\FrameworkBundle\DataCollector\TemplateAwareDataCollectorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds Twig Globals (e.g. theme)
 *
 */
class TwigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('twig')) {
            return;
        }

        return;

//        $theme = $container->getParameter('theme');
//        $def = $container->getExtension('survos_bootstrap');

        $twigDefinition = $container->getDefinition('twig');
        $twigDefinition->addMethodCall('addGlobal', ['app_locales', $locales]);

        $theme = 'theme_from_config';
        $def = $container->getDefinition('twig');
        $def->addMethodCall('addGlobal', ['theme', $theme]);

        $bootstrapDef = $container->getExtension('survos_bootstrap');
        dd($bootstrapDef);

        dd($container->getExtensionConfig('survos_bootstrap'));



//        $container->setParameter('twig.globals', $templates);
    }
}
