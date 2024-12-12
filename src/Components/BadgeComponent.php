<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('badge', template: '@SurvosBootstrap/components/badge.html.twig')]
class BadgeComponent
{
    public ?string $alignment;

    public ?string $message;

    public ?string $style;

    public ?string $color; // background color

    public ?string $text; // text color, e.g. primary, white

    public ?bool $label;

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'alignment' => null,
            'color' => null,
            'text' => null,
            'label' => null,
            'style' => null, // pill
            'message' => null,
        ]);

        $data = $resolver->resolve($data);
        if (empty($data['message'])) {
            $data['message'] = json_encode($data);
        }

        return $data;
    }
}
