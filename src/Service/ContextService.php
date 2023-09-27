<?php

// wrapper around a public class that can be used from twig or php

namespace Survos\BootstrapBundle\Service;

class ContextService
{
    public const THEME_COLORS = [
        "primary",
        "secondary",
        "success",
        "info",
        "warning",
        "danger",
        "light",
        "dark",
        ];

    public function __construct(private array $options = [])
    {
    }

    public function getOption(string $option): mixed
    {
        return $this->options[$option];
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
