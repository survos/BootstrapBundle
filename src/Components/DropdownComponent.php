<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use function Symfony\Component\String\u;

#[AsTwigComponent('bs:dropdown', template: '@SurvosBootstrap/components/dropdown.html.twig')]
class DropdownComponent
{
    public string $viewAllLabel = 'View All';
    public string $title = 'Components';
    public ?string $viewAllLink = null;
    public array $components = [];
    public string $widgetIcon = 'bx bx-category-alt fs-22';

//    #[PreMount]
    public function xxpreMount(array $data): array
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
        if (empty($this->id)) {
            $slugger = new AsciiSlugger();
            $data['id'] = $slugger->slug($data['header']);
        }
        return $data;
    }
}
