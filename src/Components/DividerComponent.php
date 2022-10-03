<?php

namespace Survos\BootstrapBundle\Components;

use Survos\BootstrapBundle\Service\ContextService;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('divider', template: '@SurvosBootstrap/components/divider.html.twig')]
class DividerComponent
{
    public ?string $alignment;

    public ?string $color;

    public ?string $message;

    public ?string $style;

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'alignment' => null,
            'color' => null,
            'style' => null, // solid
            'message' => null,
        ]);

        return $resolver->resolve($data);
    }
}
