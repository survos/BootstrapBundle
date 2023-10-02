<?php

namespace Survos\BootstrapBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'tabler:head', template: '@SurvosBootstrap/components/tabler/head.html.twig')]
final class TablerHead
{
}
