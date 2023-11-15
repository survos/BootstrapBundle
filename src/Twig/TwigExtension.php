<?php

namespace Survos\BootstrapBundle\Twig;

use Survos\BootstrapBundle\Service\ContextService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\UX\TwigComponent\ComponentRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension // implements ServiceSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'ux.twig_component.component_renderer')]
        private ComponentRenderer $componentRenderer,
        private array $routes,
        private array $options,
        private array $config,
        private ContextService $contextService,
    ) {
    }

    public function render(string $name, array $props = []): string
    {
        $renderedContent = $this->componentRenderer->createAndRender($name, $props);
        return $renderedContent;
        dd($renderedContent);
    }

    public function getFilters(): array
    {
        // consider something like https://github.com/a-r-m-i-n/font-awesome-bundle
        return [
            new TwigFilter('icon', [$this, 'icon'], ['is_safe' => ['html']]),
            new TwigFilter('fas_icon',
                // candidate for component
                fn(string $value, string $extra=''): string => sprintf('<span class="fas fa-%s %s"></span>', $value, $extra),
                ['is_safe' => ['html']]),
            new TwigFilter('bx_icon', [$this, 'bx_icon'], ['is_safe' => ['html']]),
            new TwigFilter('route_alias', fn (string $routeName): string => $this->routes[$routeName] ?? $routeName),
        ];
    }

    public function getFunctions(): array
    {
        return [
            //            new TwigFunction('component', [$this, 'render'], ['is_safe' => ['all']]),

            new TwigFunction('bootstrap_theme_colors', fn () => ContextService::THEME_COLORS),
            new TwigFunction('theme_option', fn (string $option) => $this->contextService->getOption($option)),
            new TwigFunction('config', fn () => $this->config),
            new TwigFunction('theme_options', fn () => $this->contextService->getOptions()),
            new TwigFunction('hasOffcanvas', fn () => false && $this->contextService->getOption('offcanvas')),
            new TwigFunction('admin_context_is_enabled', [$this, 'isEnabled']),
            new TwigFunction('badge', [$this, 'badge']),
            new TwigFunction('img', fn (string $src) => sprintf('img src="%s"', $src)),
        ];
    }

    public function badge(array $props = []): string
    {
        return $this->render('badge', $props);
    }

    public function isEnabled(string $value): bool
    {
        return $this->options[$value] ?? false;
    }

    // @todo: replace with component
    public function icon(string $value, string $extra='', string $title=''): string
    {
        return sprintf('<span class="%s %s" title="%s" ></span>', $value, $extra, $title);
    }
}
