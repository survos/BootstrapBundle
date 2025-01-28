<?php

namespace Survos\BootstrapBundle\Components;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'LocaleSwitcherDropdown', template: '@SurvosBootstrap/components/LocaleSwitcherDropdown.html.twig')]
final class LocaleSwitcherDropdown
{
//    public bool $putLocaleInSubdomain = false;
    public string $localeInRequest; // the local that's in the current request
    public array $localLinks=[];

    public function __construct(
        private RequestStack $requestStack,
        #[Autowire('%kernel.enabled_locales%')] private array $enabledLocales,
        )
    {
    }

    private function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }


    public function mount(array $localeLinks=[]): void
    {
        $request = $this->getRequest();
        $host = $this->getRequest()->getHttpHost();
        $hostParts = explode(".", $host);
        $alt = [];
        if (count($hostParts) === 3) {
            $uri = $request->getUri();
            $this->localeInRequest = array_shift($hostParts);
            foreach ($this->enabledLocales as $subdomain) {
                $search = "https://{$this->localeInRequest}.";
                $replace = "https://$subdomain.";
//                dump($uri, $search, $subdomain);
//                dump($this->localeInRequest, $subdomain);
                $this->localeLinks[$subdomain] = str_replace($search, $replace, $uri);
            }
//            dd($this->localeLinks);
        }
    }

}
