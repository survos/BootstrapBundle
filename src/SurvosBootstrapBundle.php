<?php

namespace Survos\BootstrapBundle;

use Survos\BootstrapBundle\Components\AccordionComponent;
use Survos\BootstrapBundle\Components\AlertComponent;
use Survos\BootstrapBundle\Components\BadgeComponent;
use Survos\BootstrapBundle\Components\BrandComponent;
use Survos\BootstrapBundle\Components\ButtonComponent;
use Survos\BootstrapBundle\Components\CardComponent;
use Survos\BootstrapBundle\Components\CarouselComponent;
use Survos\BootstrapBundle\Components\DividerComponent;
use Survos\BootstrapBundle\Components\DropdownComponent;
use Survos\BootstrapBundle\Components\LinkComponent;
use Survos\BootstrapBundle\Components\MenuBreadcrumbComponent;
use Survos\BootstrapBundle\Components\MenuComponent;
use Survos\BootstrapBundle\DependencyInjection\Compiler\TwigPass;
use Survos\BootstrapBundle\Event\KnpMenuEvent;
use Survos\BootstrapBundle\Menu\MenuBuilder;
use Survos\BootstrapBundle\Service\ContextService;
use Survos\BootstrapBundle\Service\MenuService;
use Survos\BootstrapBundle\Twig\Components\MiniCard;
use Survos\BootstrapBundle\Twig\Components\TablerHead;
use Survos\BootstrapBundle\Twig\Components\TablerIcon;
use Survos\BootstrapBundle\Twig\Components\TablerPageHeader;
use Survos\BootstrapBundle\Twig\TwigExtension;
use Survos\CoreBundle\HasAssetMapperInterface;
use Survos\CoreBundle\Traits\HasAssetMapperTrait;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SurvosBootstrapBundle extends AbstractBundle implements CompilerPassInterface, HasAssetMapperInterface
{
    use HasAssetMapperTrait;

    // protected string $extensionAlias = 'survos_bootstrap';

    // removed in favor of using the ContextService global
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Register this class as a pass, to eliminate the need for the extra DI class
        // https://stackoverflow.com/questions/73814467/how-do-i-add-a-twig-global-from-a-bundle-config
        $container->addCompilerPass($this);
    }

    // The compiler pass
    public function process(ContainerBuilder $container): void
    {
        if (false === $container->hasDefinition('twig')) {
            return;
        }
        $def = $container->getDefinition('twig');

        $eventClass = (new \ReflectionClass(KnpMenuEvent::class));
        foreach ($eventClass->getConstants() as $name => $value) {
            $def->addMethodCall('addGlobal', [$name, $value]);
        }
//        dd($def);

        $theme = $container->getParameter('my.theme');
        // I think we did this before we added ContextService, so use the twig function to get what we want, e.g. theme_option('theme')
        $def->addMethodCall('addGlobal', ['theme', $theme]);
    }

    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {

        // inject into parameters, so we can access it in the compiler pass and inject it globally.

        assert(is_array($config['routes']), json_encode($config));

        $builder->register(ContextService::class)
            ->setArgument('$config', $config)
            ->setArgument('$options', $config['options'])
            ->setAutowired(true);
        $container->parameters()->set('my.theme', $config['options']['theme']);


        foreach (
            [
                AlertComponent::class,
                AccordionComponent::class,
                AlertComponent::class,
                BrandComponent::class,
                BadgeComponent::class,
                ButtonComponent::class,
                CardComponent::class,
                CarouselComponent::class,
                DropdownComponent::class,
                DividerComponent::class,
                LinkComponent::class,

                MiniCard::class,
                TablerIcon::class,
                TablerHead::class,
                TablerPageHeader::class,
            ] as $componentClass
        ) {
            $builder->register($componentClass)->setAutowired(true)->setAutoconfigured(true);
        }

        foreach ([MenuComponent::class, MenuBreadcrumbComponent::class] as $c) {
            $builder->register($c)->setAutowired(true)->setAutoconfigured(true)
                ->setArgument('$menuOptions', $config['menu_options'])
                ->setArgument('$helper', new Reference('knp_menu.helper'))
                ->setArgument('$factory', new Reference('knp_menu.factory'))
                ->setArgument('$eventDispatcher', new Reference('event_dispatcher'));
        }

        $builder
            ->autowire('survos.bootstrap_twig', TwigExtension::class)
            ->addTag('twig.extension')
            ->setArgument('$config', $config)
            ->setArgument('$routes', $config['routes'])
            ->setArgument('$options', $config['options'])
            ->setArgument('$contextService', new Reference(ContextService::class))
//            ->setArgument('$container', new Reference('service_container'))
//            ->setArgument('$componentRenderer', new Reference('ux.twig_component.component_renderer'))
        ;

//        $builder
//            ->autowire('survos.bootstrap_page_top_renderer', PageTopRenderer::class)
//            ->addTag('knp_menu.renderer', ['alias' =>  'custom'])
//            ->setArgument('$matcher', new Reference('knp_menu.matcher'))
//            ->setArgument('$charset', '%kernel.charset%')
//        ;


        $builder->register(MenuService::class)
            ->setAutowired(true)
            ->setArgument('$authorizationChecker', new Reference('security.authorization_checker', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        // since the configuration is short, we can add it here
        $definition->rootNode()
            ->children()
            ->append($this->getAppConfig())
            ->append($this->getRouteAliasesConfig())
            ->append($this->getContextConfig())
            ->arrayNode('menu_options')
//            ->isRequired()
//            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')->prototype('scalar')->end()
            ->end() // arrayNode
            ->end(); // rootNode
    }

    public function getPaths(): array
    {
        $dir = realpath(__DIR__ . '/../assets/');
        assert(file_exists($dir), 'asset path must exist for the assets in ' . __DIR__);
        return [$dir => '@survos/bootstrap'];
    }

    private function getAppConfig(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('app');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('social')->useAttributeAsKey('name')->prototype('scalar')->end()->info('links to facebook, etc.')->end()
                ->scalarNode('code')->defaultValue('my-project')->info('project code, default for repo, dokku deployment, etc.')->end()
                ->scalarNode('abbr')->defaultValue('my<b>Project</b>')->info('text abbreviation')->end()
            ->end();
        return $rootNode;
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
            ->scalarNode('layout_direction')->defaultValue('vertical')->end()
            ->scalarNode('offcanvas')->defaultValue('end')->info("Offcanvas position (top,bottom,start,end")->end()
            ->scalarNode('theme')->defaultValue('bs5')->info("theme name")->end()
//            ->scalarNode('offcanvas')
//                ->defaultValue('')
//                ->info("Offcanvas position (top,bottom,start,end")
//            ->end()
            ->booleanNode('allow_login')->defaultValue(false)->info("Login route exists")->end();
        return $rootNode;
    }

    /**
     * Merge available configuration options, so they are all available for the ContextHelper.
     *
     * @return array
     */
    protected function getContextOptions(array $config = [])
    {
        $sidebar = [];

        if (isset($config['control_sidebar']) && !empty($config['control_sidebar'])) {
            $sidebar = $config['control_sidebar'];
        }

        $contextOptions = (array)($config['options'] ?? []);
        $contextOptions['control_sidebar'] = $sidebar;
        $contextOptions['knp_menu'] = (array)$config['knp_menu'];
        $contextOptions = array_merge($contextOptions, $config['theme']);

        return $contextOptions;
    }
}
