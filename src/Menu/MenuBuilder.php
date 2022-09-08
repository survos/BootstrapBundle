<?php

/*
 * This file is based on the MenuBuilder for KnpMenu in Kevin Papst's AdminLTE bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with that source code.
 */

namespace Survos\BootstrapBundle\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\FactoryInterface;
use Survos\BootstrapBundle\Event\KnpMenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuBuilder
{

    public function __construct(private FactoryInterface $factory, private EventDispatcherInterface $eventDispatcher)
    {
    }

//    public function createMenu(array $options): ItemInterface
//    {
////        $options = (new OptionsResolver())
////            ->setDefaults([
////                'event' => KnpMenuEvent::MENU_EVENT,
////                'name' => KnpMenuEvent::MENU_EVENT
////            ])->resolve($options);
//        $menu = $this->factory->createItem($options['name'] ?? KnpMenuEvent::MENU_EVENT );
//        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options), $options['eventName'] ?? KnpMenuEvent::MENU_EVENT);
////        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options));
//        return $menu;
//    }
//
//    public function createAppMenu(array $options): ItemInterface
//    {
//        $menu = $this->factory->createItem('menuroot');
//        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options));
//        return $menu;
//    }
//
//    public function createAuthMenu(array $options): ItemInterface
//    {
//        // $options is what is passed in from the twig call to GET the menu, NOT the MenuBuilder (for rendering)
////        dd($options);
//        $menu = $this->factory->createItem('authroot', [
//            'label' => "Auth",
//            'first' => 'FIRSTCLASS',
//            'currentClass' => 'text-danger current-class active show',
//            'ancestorClass' => 'text-warning ancestor-class active show',
//            'attributes' => [
//                'class' => 'dropdown-menu dropdown-menu-end',
//            ]
//        ]);
//        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options), KnpMenuEvent::AUTH_MENU_EVENT);
//        return $menu;
//    }
//
//    public function createNavbarMenu(array $options): ItemInterface
//    {
//        $menu = $this->factory->createItem('navbar_root');
//
////        $menu = $this->factory->createItem('menuroot', [
////            'attributes' => [
////                'class' => "navbar-nav me-auto mb-2 mb-lg-0"
////            ]
////        ]);
//
//        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options), KnpMenuEvent::NAVBAR_MENU_EVENT);
//        return $menu;
//    }
//
//    public function createFooterMenu(array $options): ItemInterface
//    {
//        // options are passed in from knp_menu_GET.  So they really shouldn't be rendering options.
//        //
//        $menu = $this->factory->createItem('footer');
//        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options), KnpMenuEvent::FOOTER_MENU_EVENT);
//        return $menu;
//
//
//        // this builds the root item
//        $menu = $this->factory->createItem('footer', [
//            'listAttributes' => [
//                'class' => "footer-listAttributes-class"
////                'class' => "list-unstyled ul-class footer-listAttributes-class"
//            ],
//            'attributes' => [
//                'class' => "footer-attributes-class"
////                'class' => "navbar-nav nav me-auto mb-2 mb-lg-0 list-unstyled footer-attributes-class"
//            ],
////            NOTE For the root element, only the children attributes are used as only the <ul> element is displayed.
//            'childrenAttributes' => [
////                'class' => 'list-unstyled ul-rool-class footer-childrenAttributes-class '
//                'class' => 'footer-childrenAttributes-class '
//            ],
//        ]);
//
//        $childOptions = [
////            'class' => 'list-item footer-child'
//        ];
//
//    }
//
//    public function createSidebarMenu(array $options): ItemInterface
//    {
//
//
////        assert(count($options), "Missing get options");
//        $menu = $this->factory->createItem('sidebar');
//        $childOptions = [];
//        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options, $childOptions),
//            KnpMenuEvent::SIDEBAR_MENU_EVENT);
//
//        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options, $childOptions));
//
//
//        $menu = $this->factory->createItem('menuroot',
//            [
//                'label' => "Menu Root",
//                'first' => 'FIRSTCLASS',
//                'currentClass' => 'text-danger current-class active show',
//                'ancestorClass' => 'text-warning ancestor-class active show',
//
//                'attributes' => [
//                    'class' => 'nav nav-sidebar flex-column menuroot-attributes-class',
//                ],
//// @todo: pass these, so they an depend on what theme is being used.
//                'listAttributes' => [
//                    'class' => 'nav nav-treeviewXX listAttributes-class'
//                ],
//                'childrenAttributes' => [
//                    'class' => 'nav-link nav-treeview',
//                    'data-widget' => 'treeview',
//                    'data-accordion' => 'false',
//                    'role' => 'menu'
//                ],
//            ]);
//
//        $childOptions = [
//            'attributes' => ['class' => 'nav-treeview'],
//            'childrenAttributes' => ['class' => 'list-unstyled nav-treeview show menu-open branch'],
//            'labelAttributes' => ['safe_html' => true, 'data-bs-toggle' => 'collapse'],
//        ];
//
//        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options, $childOptions),
//            KnpMenuEvent::SIDEBAR_MENU_EVENT);
//
//        return $menu;
//    }
//
//    public function createPageMenu(array $options): ItemInterface
//    {
//        $menu = $this->factory->createItem('root', [
//            'class' => 'float-right',
//            'childrenAttributes' => [
//                'class' => 'nav nav-pills childrenAttributes-class pageMenuClass',
//                // 'data-widget' => 'nav',
//                'data-accordion' => false,
//                // 'role' => 'menu'
//            ],
//        ]);
//
//        /* this doesnt seem to work well.
//        $menu
//            ->addChild('title', ['label' => $options['title'], 'uri' => '#'])
//            ->setAttribute('class', 'h6 float-right');
//        */
//
//        $childOptions = [
//            'attributes' =>
//                ['class' => 'show childOptions-class'],
//            'childrenAttributes' => ['class' => 'list-unstyled '],
//            'labelAttributes' => ['safe_html' => true, 'data-toggle' => 'xxcollapse'],
//        ];
//
//        $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options, $childOptions), KnpMenuEvent::PAGE_MENU_EVENT);
//
//        return $menu;
//    }

}
