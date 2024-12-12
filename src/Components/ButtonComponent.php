<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('button', template: '@SurvosBootstrap/components/button.html.twig')]
class ButtonComponent
{
    public ?string $label;

    public ?string $color;

    public ?string $style;

    public ?string $icon;

    public ?string $size;

    public ?bool $outline;

    public ?array $links;

    public ?array $a;

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'label' => null,
            'icon' => null,
            'color' => null,
            'style' => null,
            'links' => null,
            'size' => null,
            'outline' => false,
            'a' => null, // href, target
        ]);

        $data = $resolver->resolve($data);
        if ($a = $data['a']) {
            $data['a'] = (new OptionsResolver())
                ->setDefaults([
                    'href' => '#',
                    'target' => '_blank',
                ])->resolve($a);
        }

        return $data;
    }
}
