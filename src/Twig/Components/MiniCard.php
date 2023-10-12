<?php

namespace Survos\BootstrapBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'tabler:mini-card', template: '@SurvosBootstrap/components/MiniCard.html.twig')]
final class MiniCard
{
    public string $msg = '';
        public string $tagline='';
    public string $icon='';
    public string $bgColor='green';
    public string $textColor='white';
}
