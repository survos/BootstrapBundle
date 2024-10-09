<?php

namespace Survos\BootstrapBundle\Components;

use Survos\BootstrapBundle\Service\ContextService;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('alert', template: '@SurvosBootstrap/components/alert.html.twig')]
class AlertComponent
{
    public string $type;
    public string $color='green';
    public string $size='md';

    public string $message;

    public bool $dismissible;

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'type' => 'success',
            'dismissible' => false,
        ]);
        $resolver->setAllowedValues('type', ContextService::THEME_COLORS);
        $resolver->setRequired('message');
        $resolver->setAllowedTypes('message', 'string');

        return $resolver->resolve($data);
    }
}
