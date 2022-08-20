<?php

namespace Survos\BootstrapBundle\Twig;

use Survos\BootstrapBundle\Components\BadgeComponent;
use Survos\BootstrapBundle\Service\ContextService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\UX\TwigComponent\ComponentFactory;
use Symfony\UX\TwigComponent\ComponentRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension implements ServiceSubscriberInterface
{

    public function __construct(
        private ContainerInterface $container,
                                private ComponentRenderer $componentRenderer,
                                private array $routes,
                                private array $options,
                                ContextService $contextService,
    )
    {
    }

    public static function getSubscribedServices(): array
    {
        return [
            ComponentRenderer::class,
            ComponentFactory::class,
        ];
    }

    public function render(string $name, array $props = []): string
    {
        return $this->componentRenderer->createAndRender($name, $props);
//        return $this->container->get(ComponentRenderer::class)->createAndRender($name, $props);
    }

    public function embeddedContext(string $name, array $props, array $context): array
    {
        return $this->container->get(ComponentRenderer::class)->embeddedContext($name, $props, $context);
    }
    public function getFilters(): array
    {
        // consider something like https://github.com/a-r-m-i-n/font-awesome-bundle
        return [
            new TwigFilter('icon', [$this, 'icon'], ['is_safe' => ['html']]),
            new TwigFilter('route_alias', fn(string $routeName): string  => $this->routes[$routeName] ?? $routeName ),
        ];
    }

    public function getFunctions(): array
    {
        return [
//            new TwigFunction('component', [$this, 'render'], ['is_safe' => ['all']]),

            new TwigFunction('bootstrap_theme_colors', fn() => ContextService::THEME_COLORS),
            new TwigFunction('admin_context_is_enabled', [$this, 'isEnabled']),
            new TwigFunction('badge', [$this, 'badge']),
            new TwigFunction('img', fn(string $src) => sprintf('img src="%s"', $src)),
        ];
    }

    public function badge(array $props=[]): bool
    {
        return $this->render('badge', $props);
        dd($value);
    }
    public function isEnabled(string $value): bool
    {
        return $this->options[$value] ?? false;

    }
    public function icon(string $value): string
    {
        return sprintf('<span class="fas fa-%s"></span>', $value);
    }

}
