<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('tabs', template: '@SurvosBootstrap/components/tabs.html.twig')]
class TabsComponent
{
    public array $tabs = [];
    public ?string $active = null; // default to first?
    public ?string $caller = null;
}
