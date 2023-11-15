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

    public function __construct(
        private array $options = [],
        private array $config = []

    )
    {
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): ContextService
    {
        $this->config = $config;
        return $this;
    }

    public function getOption(string $option, string $default=null): mixed
    {
        return $this->options[$option]??$default;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
