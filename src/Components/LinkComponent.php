<?php

namespace Survos\BootstrapBundle\Components;

use Survos\BootstrapBundle\Service\ContextService;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('link', template: '@SurvosBootstrap/components/link.html.twig')]
class LinkComponent
{
    public ?string $href;
    public ?string $path;
    public ?string $route;
    public array $rp = [];
    public ?string $body=null;
    public ?string $class;
    public mixed $if = true;

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {

    }

    #[PreMount]
    public function preMount(array $data): array
    {
        // validate data
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'href' => null,
            'if' => true,
            // deprecated.
            'path' => null,
            'route' => null,
            'rp' => [],
            'body' => null, // what's inside the 'a' tag
            'class' => null, // string or array
        ]);

        $params = $resolver->resolve($data);

        if (!empty($data['path'])) {
            if (empty($data['route'])) {
                $data['route'] = $data['path'];
            }
        }

        //
        if (empty($data['body'])) {
            // could also say "add body or a body block
            $data['body'] = json_encode($data);
        }


        // if the path doesn't exist, e.g. no homepage, use # (or don't generate the href?)
        if (empty($data['href'])) {
            try {
                $href = $this->urlGenerator->generate($data['route'], $params['rp']);
            } catch (RouteNotFoundException $exception) {
                $href = '#';
            }
            $data['href'] = $href;
        }

        return $resolver->resolve($data);
    }
}
