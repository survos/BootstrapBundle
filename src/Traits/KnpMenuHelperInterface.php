<?php // Simplies the construction of menu items,
namespace Survos\BootstrapBundle\Traits;

use Knp\Menu\ItemInterface;
use Survos\BootstrapBundle\Event\KnpMenuEvent;
use Survos\CoreBundle\Entity\RouteParametersInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use function Symfony\Component\String\u;


interface KnpMenuHelperInterface
{
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker);
    public function addSubmenu(ItemInterface $menu, ?string $label=null, ?string $icon=null): ItemInterface;
    public function addHeading(ItemInterface $menu, string $label, string $icon=null): void;
    public function add(
        ItemInterface $menu,
        ?string $route=null,
        array|RouteParametersInterface|null $rp=null,
        ?string $label=null,
        ?string $uri=null,
        ?string $id=null,
        ?string $icon=null,
        bool $returnItem = false,
    ): self|ItemInterface;

    public function addMenuItem(ItemInterface $menu, array $options, array $extra=[]): ItemInterface;

    public function isGranted($attribute, $subject=null);
    public function authMenu(AuthorizationCheckerInterface $security, ItemInterface $menu, $childOptions=[]);
    public function supports(KnpMenuEvent $event): bool;


}
