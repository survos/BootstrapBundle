<?php

namespace Survos\BootstrapBundle\Components;

use Knp\Menu\Twig\Helper;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use function Symfony\Component\String\u;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsTwigComponent('menu', template: '@SurvosBootstrap/components/menu.html.twig')]
class MenuComponent
{
    public function __construct(private Helper $helper) {

    }
    public string $title;
    public string $type;

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'title' => null,
            'type' => null,
        ]);
//        $resolver->setRequired('body');
        $resolver->setAllowedTypes('title', 'string');

        $menuItem = $this->helper->get($data['type']);


        $data = $resolver->resolve($data);
        return $data;
    }

}
