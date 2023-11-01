<?php

namespace Survos\BootstrapBundle\Components;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Twig\Helper;
use Survos\BootstrapBundle\Event\KnpMenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use function Symfony\Component\String\u;

#[AsTwigComponent('menu', template: '@SurvosBootstrap/components/menu.html.twig')]
class MenuComponent
{
    public function __construct(
        private array $menuOptions,
        protected Helper $helper,
        protected FactoryInterface $factory,
        protected EventDispatcherInterface $eventDispatcher
    ) {
        //    public function __construct(private Helper $helper) {
    }

    public ?string $title;

    #[ExposeInTemplate]
    public string $type; // shortcut

    public string $eventName; // this is the real event name, but shortcuts are easier.

    public string $menuAlias = KnpMenuEvent::class;

    public array $path = [];

    public array $options = [];

    public bool $print = false;

    public ItemInterface $menuItem;

    public string|bool|null $translationDomain  = false;

    protected const SHORTCUTS = [
        'sidebar' => KnpMenuEvent::SIDEBAR_MENU_EVENT,
        'top_navbar' => KnpMenuEvent::NAVBAR_MENU_EVENT,
        'top_auth' => KnpMenuEvent::AUTH_MENU_EVENT,
        'footer' => KnpMenuEvent::FOOTER_MENU_EVENT,
        'top_page' => KnpMenuEvent::PAGE_MENU_EVENT,
        'profile_dropdown' => KnpMenuEvent::PROFILE_DROPDOWN_MENU_EVENT,
    ];

    public function mount(string $type, ?string $eventName = null, array $path = [], array $options = [])
    {
        $this->type = $type;
        $this->path = $path;
        $this->options = $options;
        assert(
            array_key_exists($type, self::SHORTCUTS),
            "Invalid menu shortcut $type, use " . join(',', array_keys(self::SHORTCUTS))
        );
        //        $data['menuCode'] = $shortcuts[$data['type']];
        if (! $eventName) {
            $eventName = self::SHORTCUTS[$type];
        }

        $menu = $this->factory->createItem($options['name'] ?? KnpMenuEvent::MENU_EVENT);

        $options = (new OptionsResolver())
            ->setDefaults($this->menuOptions)
            ->resolve($options);

        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options), $eventName);
        $this->menuItem = $this->helper->get($menu, $path, $options);
    }

//    #[PreMount]
    public function xpreMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'title' => null,
            'type' => null,
            'path' => [],
            'print' => false,
            'options' => [],
        ]);
        //        $resolver->setRequired('body');
        $resolver->setAllowedTypes('title', ['null', 'string']);

        $data = $resolver->resolve($data);
        $type = $data['type'];
        $data['options']['type'] = $type; // so it's passed into the MenuBuilder, mostly for debugging
        $data['menuCode'] = KnpMenuEvent::class;
        //        $data['menuAlias'] = KnpMenuEvent::class;
        //        $menuItem = $this->helper->get($shortcuts[$data['type']], $this->path, $data['options']);
        //        $data['menuItem'] = $menuItem;
        return $data;
    }
}
