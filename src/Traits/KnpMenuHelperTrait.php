<?php // Simplies the construction of menu items,
namespace Survos\BootstrapBundle\Traits;

use Knp\Menu\ItemInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use function Symfony\Component\String\u;

trait KnpMenuHelperTrait
{

    public function authMenu(AuthorizationCheckerInterface $security, ItemInterface $menu, $childOptions=[])
    {
        if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $menu->addChild(
                'logout',
                ['route' => 'app_logout', 'label' => 'menu.logout', 'childOptions' => $childOptions]
            )->setLabelAttribute('icon', 'fas fa-sign-out-alt');
        } else {
            $menu->addChild(
                'login',
                ['route' => 'app_login', 'label' => 'menu.login', 'childOptions' => $childOptions]
            )->setLabelAttribute('icon', 'fas fa-sign-in-alt');

            try {
                $menu->addChild(
                    'register',
                    ['route' => 'app_register', 'label' => 'menu.register', 'childOptions' => $childOptions]
                )->setLabelAttribute('icon', 'fas fa-sign-in-alt');
            } catch (\Exception $exception) {
                // route is likely missing
            }
        }

    }


}
