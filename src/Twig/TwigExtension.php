<?php

namespace Survos\BootstrapBundle\Twig;

use Survos\BootstrapBundle\Model\Tab;
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
    }

    public function getFilters(): array
    {
        // consider something like https://github.com/a-r-m-i-n/font-awesome-bundle
        return [
            new TwigFilter('attributes', [$this, 'attributes'], ['is_safe' => ['html']]),
            new TwigFilter('icon', [$this, 'icon'], ['is_safe' => ['html']]),
            new TwigFilter('fas_icon',
                // candidate for component
                fn(string $value, string $extra=''): string => sprintf('<span class="fas fa-%s %s"></span>', $value, $extra),
                ['is_safe' => ['html']]),
            new TwigFilter('bx_icon', [$this, 'bx_icon'], ['is_safe' => ['html']]),
            // if you're calling route_alias and it doesn't exist, maybe it should be '#'?
            new TwigFilter('route_alias', fn (string $routeName): ?string =>
//            dd($this->routes[$routeName]) &&
            ($this->routes[$routeName] === false)
                ? null
                : $this->routes[$routeName] ?? $routeName),
        ];
    }

    public function getFunctions(): array
    {
        return [
            //            new TwigFunction('component', [$this, 'render'], ['is_safe' => ['all']]),

            new TwigFunction('bootstrap_theme_colors', fn () => ContextService::THEME_COLORS),
            new TwigFunction('theme_option', fn (string $option) => $this->contextService->getOption($option)),
            new TwigFunction('config', fn () => $this->config),
            new TwigFunction('tab', fn (string $label, ?string $content=null, ?string $translationDomain=null) => new Tab($label, $content, $translationDomain)),
            new TwigFunction('theme_options', fn () => $this->contextService->getOptions()),
            new TwigFunction('hasOffcanvas', fn () => $this->contextService->getOption('offcanvas')),
            new TwigFunction('admin_context_is_enabled', [$this, 'isEnabled']),
            new TwigFunction('badge', [$this, 'badge']),
            new TwigFunction('attributes', [$this, 'attributes'], ['is_safe' => ['html']]),
            new TwigFunction('img', fn (string $src) => sprintf('img src="%s"', $src)),
//            new TwigFunction('config', fn (string $el) => $this->config[$el]),
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

    public function attributes(array $value): string
    {
        $attrs = [];
        foreach ($value as $k => $v) {
            if (is_string($v) && $v) {
                $attrs[] = sprintf(' %s="%s"', $k, $v); // @todo: escape quotes
            }
        }
        $string = join("\n", $attrs);
        return $string;
    }


}
