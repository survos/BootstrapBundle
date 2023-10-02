<?php

namespace Survos\BootstrapBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'tabler:page-header', template: '@SurvosBootstrap/components/tabler/page_header.html.twig')]
final class TablerPageHeader
{
}
