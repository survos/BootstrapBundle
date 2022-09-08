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
    const NAVBAR_MENU_EVENT = 'survos_navbar_menu';
    const PAGE_MENU_EVENT = 'page_menu';
    const SIDEBAR_MENU_EVENT = 'sidebar_menu';
    const FOOTER_MENU_EVENT = 'survos_footer_menu';
    const AUTH_MENU_EVENT = 'survos_auth_menu';
    const MENU_EVENT = KnpMenuEvent::class;


    public function __construct(
        protected ItemInterface $menu,
        protected FactoryInterface $factory,
        private array $options = [],
        private array $childOptions = [],
    )
    {
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
        assert(array_key_exists($key, $this->options), "$key is invalid, use " . join(', ', $this->options));
        return $this->options[$key];
    }

    public function getChildOptions(): array
    {
        return $this->childOptions;
    }
}
