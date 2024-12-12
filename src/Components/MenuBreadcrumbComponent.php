<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('menu_breadcrumb', template: '@SurvosBootstrap/components/menu_breadcrumb.html.twig')]
class MenuBreadcrumbComponent extends MenuComponent
{
    public string|bool|null $translationDomain = false;
}
