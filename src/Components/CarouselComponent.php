<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('carousel', template: '@SurvosBootstrap/components/carousel.html.twig')]
final class CarouselComponent
{
    public array $slides = [];
}
