<?php

namespace Survos\BootstrapBundle\Twig;

use Survos\BootstrapBundle\Service\ContextService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    /**
     * @param $routes array<string, string|null>
     * @param $options array<string, mixed>
     */
    public function __construct(
        private array $routes,
        private array $options,
        ContextService $contextService
    )
    {

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
            new TwigFunction('bootstrap_theme_colors', fn() => ContextService::THEME_COLORS),
            new TwigFunction('admin_context_is_enabled', [$this, 'isEnabled']),
        ];
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
