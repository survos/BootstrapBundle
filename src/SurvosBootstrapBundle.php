<?php

namespace Survos\BootstrapBundle;

use Survos\BootstrapBundle\Components\AccordionComponent;
use Survos\BootstrapBundle\Components\AlertComponent;
use Survos\BootstrapBundle\Components\BadgeComponent;
use Survos\BootstrapBundle\Components\ButtonComponent;
use Survos\BootstrapBundle\Components\CardComponent;
use Survos\BootstrapBundle\Components\DividerComponent;
use Survos\BootstrapBundle\Components\MenuBreadcrumbComponent;
use Survos\BootstrapBundle\Components\MenuComponent;
use Survos\BootstrapBundle\Event\KnpMenuEvent;
use Survos\BootstrapBundle\Menu\MenuBuilder;
use Survos\BootstrapBundle\Service\ContextService;
use Survos\BootstrapBundle\Service\MenuService;
use Survos\BootstrapBundle\Twig\TwigExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SurvosBootstrapBundle extends AbstractBundle
{

//    protected string $extensionAlias = 'survos_bootstrap';

    /** @param array<mixed> $config */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
//        if ($twig = $container->import('twig')) {
//            $twig->addGlobal('backend', array(
//                'title' => $this->container->getParameter('backend.title')
//            ));
//        }
        assert(is_array($config['routes']), json_encode($config));

//        $config = $this->getContextOptions($config);
//        dd($config);
        $builder->register(ContextService::class)
            ->setArgument('$options', $config['options'])
            ->setAutowired(true);

        $builder->register(AlertComponent::class)->setAutowired(true)->setAutoconfigured(true);
        $builder->register(AccordionComponent::class)->setAutowired(true)->setAutoconfigured(true);
        $builder->register(DividerComponent::class)->setAutowired(true)->setAutoconfigured(true);
        $builder->register(CardComponent::class)->setAutowired(true)->setAutoconfigured(true);
        $builder->register(ButtonComponent::class)->setAutowired(true)->setAutoconfigured(true);
        $builder->register(BadgeComponent::class)->setAutowired(true)->setAutoconfigured(true);
        foreach ([MenuComponent::class, MenuBreadcrumbComponent::class] as $c) {
            $builder->register($c)->setAutowired(true)->setAutoconfigured(true)
                ->setArgument('$menuOptions', $config['menu_options'])
                ->setArgument('$helper', new Reference('knp_menu.helper'))
                ->setArgument('$factory', new Reference('knp_menu.factory'))
                ->setArgument('$eventDispatcher', new Reference('event_dispatcher'))
            ;
        }

        $definition = $builder
            ->autowire('survos.bootstrap_twig', TwigExtension::class)
            ->addTag('twig.extension')
            ->setArgument('$routes', $config['routes'])
            ->setArgument('$options', $config['options'])
            ->setArgument('$contextService', new Reference(ContextService::class))
//            ->setArgument('$container', new Reference('service_container'))
//            ->setArgument('$componentRenderer', new Reference('ux.twig_component.component_renderer'))
        ;


        $builder->register(MenuService::class)
            ->setAutowired(true)
            ->setArgument('$authorizationChecker', new Reference('security.authorization_checker'))
            ;

//        $builder->register(MenuBuilder::class)
//            ->setArgument('$factory', new Reference('knp_menu.factory'))
//            ->setArgument('$eventDispatcher', new Reference('event_dispatcher'))
////            ->addTag('knp_menu.menu_builder', ['method' => 'createSidebarMenu', 'alias' => KnpMenuEvent::SIDEBAR_MENU_EVENT])
////            ->addTag('knp_menu.menu_builder', ['method' => 'createNavbarMenu', 'alias' => KnpMenuEvent::NAVBAR_MENU_EVENT])
////            ->addTag('knp_menu.menu_builder', ['method' => 'createAuthMenu', 'alias' => KnpMenuEvent::AUTH_MENU_EVENT])
////            ->addTag('knp_menu.menu_builder', ['method' => 'createFooterMenu', 'alias' => KnpMenuEvent::FOOTER_MENU_EVENT])
////            ->addTag('knp_menu.menu_builder', ['method' => 'createMenu', 'alias' => KnpMenuEvent::PAGE_MENU_EVENT])
//            ->addTag('knp_menu.menu_builder', ['method' => 'createMenu', 'alias' => KnpMenuEvent::MENU_EVENT])
//        ;
//

//        Survos\BaseBundle\Menu\MenuBuilder:
//    class: Survos\BaseBundle\Menu\MenuBuilder
//    arguments:
//      - "@knp_menu.factory"
//      - "@event_dispatcher"
//    tags:
//      - { name: knp_menu.menu_builder, method: createSidebarMenu, alias: survos_sidebar_menu }
//      - { name: knp_menu.menu_builder, method: createPageMenu, alias: survos_page_menu }

//        dd($config['routes']);

    }

    public function configure(DefinitionConfigurator $definition): void
    {
        // since the configuration is short, we can add it here
        $definition->rootNode()
            ->children()
            ->append($this->getRouteAliasesConfig())
            ->append($this->getContextConfig())
            ->scalarNode('auth_menu')->defaultValue(2)->end()
            ->arrayNode('menu_options')
//            ->isRequired()
//            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('scalar')->end()
            ->end()
            ->end();
        ;
    }

    // inspired by AdminLTEBundle
    private function getRouteAliasesConfig(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('routes');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('home')
                ->defaultValue('app_homepage')
                ->info('name of the homepage route')
                ->end()

            ->scalarNode('login')->defaultValue('app_login')->info('name of the login')->end()
            ->scalarNode('homepage')->defaultValue('app_homepage')->info('name of the home routes')->end()

            ->scalarNode('logout')
            ->defaultValue('app_logout')
            ->info('name of the logout route')
            ->end()

            ->scalarNode('offcanvas')
            ->defaultValue('app_settings')
            ->info('name of the offcanvas route (e.g. a settings sidebar)')
            ->end()

            ->scalarNode('register')
            ->defaultValue('app_register')
            ->info('name of the register route')
            ->end()

            ->end();
        return $rootNode;
    }

    private function getContextConfig(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('options');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('offcanvas')->defaultValue('')->info("Offcanvas position (top,bottom,start,end")->end()
//            ->scalarNode('offcanvas')
//                ->defaultValue('')
//                ->info("Offcanvas position (top,bottom,start,end")
//            ->end()
            ->booleanNode('allow_login')
                ->defaultValue(false)
                ->info("Login route exists")
            ->end();
        return $rootNode;
    }

    /**
     * Merge available configuration options, so they are all available for the ContextHelper.
     *
     * @param array $config
     * @return array
     */
    protected function getContextOptions(array $config = [])
    {
        $sidebar = [];

        if (isset($config['control_sidebar']) && !empty($config['control_sidebar'])) {
            $sidebar = $config['control_sidebar'];
        }

        $contextOptions = (array) ($config['options'] ?? []);
        $contextOptions['control_sidebar'] = $sidebar;
        $contextOptions['knp_menu'] = (array) $config['knp_menu'];
        $contextOptions = array_merge($contextOptions, $config['theme']);

        return $contextOptions;
    }

}
