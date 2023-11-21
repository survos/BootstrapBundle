<?php

namespace Survos\BootstrapBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Collect all MenuItemInterface objects that should be rendered in the menu/navigation section.
 */
class KnpMenuEvent extends Event
{
    public const NAVBAR_MENU = 'NAVBAR_MENU';
    public const NAVBAR_MENU2 = 'NAVBAR_MENU2';
    public const NAVBAR_MENU3 = 'NAVBAR_MENU3';
    public const PAGE_MENU = 'PAGE_MENU';
    public const SIDEBAR_MENU = 'SIDEBAR_MENU';
    public const FOOTER_MENU = 'FOOTER_MENU';
    public const AUTH_MENU = 'AUTH_MENU';
    public const PROFILE_MENU = 'PROFILE_MENU';

//    public const MENU = KnpMenuEvent::class;

    public function __construct(
        protected ItemInterface $menu,
        protected FactoryInterface $factory,
        private array $options = [],
        private array $childOptions = [],
    ) {
    }

    static public function getConstants(): array
    {
        return (new \ReflectionClass(__CLASS__))->getConstants();
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $key): mixed
    {
        // @todo: validate with keys from $config
        assert(array_key_exists($key, $this->options), "option '$key' is invalid, use " . join(', ', $this->options));
        return $this->options[$key];
    }

    public function getChildOptions(): array
    {
        return $this->childOptions;
    }
}
