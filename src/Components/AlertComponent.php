<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('snead_alert', template: '@SurvosBootstrap/components/alert.html.twig')]
class AlertComponent
{
    public string $type;
    public string $message;
    public bool $dismissible;

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults(['type' => 'success', 'dismissible' => false]);
        $resolver->setAllowedValues('type', ['success', 'danger', 'info', 'primary']);
        $resolver->setRequired('message');
        $resolver->setAllowedTypes('message', 'string');

        return $resolver->resolve($data);
    }

}
