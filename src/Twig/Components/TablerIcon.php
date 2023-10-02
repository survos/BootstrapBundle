<?php

namespace Survos\BootstrapBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'tabler:icon', template: '@SurvosBootstrap/components/tabler/icon.html.twig')]
final class TablerIcon
{
}
