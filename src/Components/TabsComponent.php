<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use function Symfony\Component\String\u;

#[AsTwigComponent('tabs', template: '@SurvosBootstrap/components/tabs.html.twig')]
class TabsComponent
{
    public array $tabs = [];
    public ?string $active = null; // default to first?
    public ?string $caller = null;

}
