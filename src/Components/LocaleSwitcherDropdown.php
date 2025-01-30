<?php

namespace Survos\BootstrapBundle\Components;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use function Symfony\Component\String\u;

#[AsTwigComponent(name: 'LocaleSwitcherDropdown', template: '@SurvosBootstrap/components/LocaleSwitcherDropdown.html.twig')]
final class LocaleSwitcherDropdown
{
//    public bool $putLocaleInSubdomain = false;
    public ?string $localeInRequest=null; // the local that's in the current request
    public array $localeLinks=[];

    public function __construct(
        private RequestStack $requestStack,
        private LoggerInterface $logger,
        #[Autowire('%kernel.enabled_locales%')] private array $enabledLocales,
        )
    {
    }

    private function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }


    public function mount(): void
    {
        $request = $this->getRequest();
        $host = $this->getRequest()->getHttpHost();
        $hostParts = explode(".", $host);
        if (count($hostParts) === 3)
        {
            $uri = $request->getUri(); // the full url
            $this->localeInRequest = array_shift($hostParts);
            $this->logger->warning("Host: $host, uri: $uri, locale:" . $this->localeInRequest);
            foreach ($this->enabledLocales as $subdomain) {

                $search = "https://{$this->localeInRequest}.";
                $replace = "https://$subdomain.";
//                dump($uri, $search, $subdomain);
//                dump($this->localeInRequest, $subdomain);
//                dd($uri, $this->getRequest()->getUri());
                $this->localeLinks[$subdomain] = 'https://' . preg_replace("/^{$this->localeInRequest}\./", $subdomain . '.', $host) .
                    $this->getRequest()->getPathInfo();

//                $this->localeLinks[$subdomain] = str_replace($search, $replace, $uri);
            }
            dd($this->localeLinks);
        }
    }

}
