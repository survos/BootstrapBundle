<?php

namespace Survos\BootstrapBundle\Components;

use Survos\BootstrapBundle\Service\ContextService;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('card', template: '@SurvosBootstrap/components/card.html.twig')]
class CardComponent
{
    public ?string $image;
    public ?int $h;
    public ?string $alt;
    public ?string $body;
    public ?string $title;
    public ?string $text;
    public ?array $links;

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'image' => null,
            'alt' => null,
            'body' => null,
            'title' => null,
            'text' => null,
            'links' => null,
            'h' => null
            ]);

        return $resolver->resolve($data);
    }

}
