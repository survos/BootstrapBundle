<?php

namespace Survos\BootstrapBundle\Components;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Twig\Helper;
use Survos\BootstrapBundle\Event\KnpMenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use function Symfony\Component\String\u;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsTwigComponent('menu_breadcrumb', template: '@SurvosBootstrap/components/menu_breadcrumb.html.twig')]
class MenuBreadcrumbComponent extends MenuComponent
{

}
