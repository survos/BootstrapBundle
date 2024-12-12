<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('brand', template: '@SurvosBootstrap/components/brand.html.twig')]
class BrandComponent
{
    public ?string $logoLink;
    public ?string $smLogoHtml;
    public ?string $lgLogoHtml;
    public ?string $smImage = null;
    public ?string $darkImage = null;
    public ?string $lightImage = null;

    //    #[PreMount]
    //    public function preMount(array $data): array
    //    {
    //        // validate data
    //        $resolver = new OptionsResolver();
    //        $resolver->setDefaults([
    //            'alignment' => null,
    //            'color' => null,
    //            'text' => null,
    //            'label' => null,
    //            'style' => null, // pill
    //            'message' => null,
    //        ]);
    //
    //        $data = $resolver->resolve($data);
    //        if (empty($data['message'])) {
    //            $data['message'] = json_encode($data);
    //        }
    //        return $data;
    //    }
}
