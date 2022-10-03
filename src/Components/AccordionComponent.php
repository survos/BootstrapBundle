<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use function Symfony\Component\String\u;

#[AsTwigComponent('accordion', template: '@SurvosBootstrap/components/accordion.html.twig')]
class AccordionComponent
{
    public string $header;

    public ?string $accordion_body;

    public ?string $bsParent;

    public ?string $id;

    public bool $open;

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'header' => null,
            'id' => null,
            'accordion_body' => null,
            'bsParent' => null,
            'open' => false,
        ]);
        //        $resolver->setRequired('body');
        $resolver->setAllowedTypes('header', 'string');

        $data = $resolver->resolve($data);
        if (empty($id)) {
            $slugger = new AsciiSlugger();
            $data['id'] = $slugger->slug($data['header']);
        }
        return $data;
    }
}
