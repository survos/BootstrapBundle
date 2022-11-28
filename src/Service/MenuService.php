<?php

// base class for menu subscriber, makes it easier to handle options

namespace Survos\BootstrapBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use function Symfony\Component\String\u;

class MenuService
{
    private ?array $options;

    private $childOptions;

    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getAuthorizationChecker()
    {
        return $this->authorizationChecker;
    }

    public function setOptions(array $options = [], array $childOptions = [])
    {
        $this->options = $options;
        $this->childOptions = $childOptions;
    }

    public function addAuthMenu(ItemInterface $menu, $childOptions = []): ItemInterface
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $menu->addChild(
                'logout',
                [
                    'route' => 'app_logout',
                    'label' => 'menu.logout',
                    'childOptions' => $childOptions,
                ]
            )->setLabelAttribute('icon', 'fas fa-sign-out-alt');
        } else {
            $menu->addChild(
                'login',
                [
                    'route' => 'app_login',
                    'label' => 'menu.login',
                    'childOptions' => $childOptions,
                ]
            )->setLabelAttribute('icon', 'fas fa-sign-in-alt');

            try {
                $menu->addChild(
                    'register',
                    [
                        'route' => 'app_register',
                        'label' => 'menu.register',
                        'childOptions' => $childOptions,
                    ]
                )->setLabelAttribute('icon', 'fas fa-sign-in-alt');
            } catch (\Exception $exception) {
                // route is likely missing
            }
        }
        return $menu;
    }

    public function addMenuItem(ItemInterface $menu, array $options, array $extra = []): ItemInterface
    {
        $options = $this->menuOptions($options);
        // must pass in either route, icon or menu_code

        // especially for collapsible menus.  Cannot start with a digit.
        if (! $options['id']) {
            $options['id'] = 'id_' . md5(json_encode($options));
        }

        $child = $menu->addChild($options['id'], $options);
        //        $child->setChildrenAttribute('class', 'branch');

        if ($options['external']) {
            $child->setLinkAttribute('target', '_blank');
            $options['icon'] = 'fas fa-external-alt';
        }

        //        if ($icon = $options['icon']) {
        //            $child->setLinkAttribute('icon', $icon);
        //            $child->setLabelAttribute('icon', $icon);
        //            $child->setAttribute('icon', $icon);
        //        }

        if ($icon = $options['feather']) {
            $child->setLinkAttribute('feather', $icon);
            $child->setLabelAttribute('feather', $icon);
            $child->setAttribute('feather', $icon);
        }

        if (! empty($extra['safe_label'])) {
            $child->setExtra('safe_label', true);
        }

        // if this is a collapsible menu item, we need to set the data target to next element.  OR we can let knp_menu renderer handle it.
        if (! $options['route'] && ! $options['uri']) {
            // only if there are children, but otherwise this is just a label
            //            $child->setAttribute('collapse_type', 'collapse');
            //            $child->setAttribute('class', 'collapse collapsed');
            //            $child->setAttribute('data-bs-target', 'hmm');
        }

        if ($classes = $options['classes']) {
            $child->setAttribute('class', $classes);
        }

        if ($badge = $options['badge']) {
            $child->setExtra('badge', is_array($badge) ? $badge : [
                'value' => $badge,
            ]);
        }

        if ($style = $options['style']) {
            $child->setAttribute('style', $style);
        }

        return $child;
    }

    private function menuOptions(array $options, array $extra = []): array
    {
        // idea: make the label a . version of the route, e.g. project_show could be project.show
        // we could also set a default icon for things like edit, show
        try {
            $options = (new OptionsResolver())
                ->setDefaults([
                    // deprecated, use 'id' instead
                    'menu_code' => null,
                    'extras' => [],
                    'id' => null,
                    'route' => null,
                    'rp' => null,
                    'external' => false,
                    '_fragment' => null,
                    'label' => null,
                    'icon' => null,
                    'badge' => null,
                    'feather' => null,
                    'uri' => null,
                    'classes' => [], // this doesn't feel quite right.  Maybe a "style: header"?
                    'style' => null,
                    'childOptions' => $this->childOptions,
                    'description' => null,
                    'attributes' => [],
                ])->resolve($options);
        } catch (\Exception $exception) {
            assert(false, $exception->getMessage());
            //            dd($options, $exception);
        }

        // rename rp
        if (is_object($options['rp'])) {
            $options['routeParameters'] = $options['rp']->getRp();
            if (empty($options['icon'])) {
                $iconConstant = get_class($options['rp']) . '::ICON';
                $options['icon'] = defined($iconConstant) ? constant($iconConstant) : 'fas fa-database'; // generic database entity
            }
        } elseif (is_array($options['rp'])) {
            $options['routeParameters'] = $options['rp'];
        }
        // if (isset($options['rp'])) { throw new \Exception($options);}
        unset($options['rp']);
        if (empty($options['label']) && ($routeLabel = $options['route'])) {
            // _index is commonly used to list entities
            $routeLabel = preg_replace('/_index$/', '', $routeLabel);
            $routeLabel = preg_replace('/^app_/', '', $routeLabel);
            $options['label'] = u($routeLabel)->replace('_', ' ')->title(true)->toString();
        }

        if (empty($options['label']) && $options['menu_code']) {
            $options['label'] = u($options['menu_code'])->replace('.', ' ')->title(true)->replace('_header', '')->toString();
        }

        // if label is exactly true then automate the label from the route
        if ($options['label'] === true) {
            $options['label'] = str_replace('_', '.', $options['route']);
        }

        // we could pass in a hash route and hash params instead.
        if ($fragment = $options['_fragment']) {
            $options['uri'] = '#' . $fragment;
            unset($options['route']);
            //
        }

        // default icons, should be configurable in survos_base.yaml
        if ($options['icon'] === null) {
            foreach ([
                'show' => 'fas fa-eye',
                'edit' => 'fas fa-wrench',
            ] as $regex => $icon) {
                if ($route = $options['route']) {
                    if (preg_match("|$regex|", $route)) {
                        $options['data-icon'] = $icon;
                    }
                }
            }
        }

        // move the icon to attributes, where it belongs
        if ($options['icon']) {
            $options['attributes']['data-icon'] = $options['icon'];
            //            $options['attributes']['class'] = 'text-danger';
            $options['label_attributes']['data-icon'] = $options['icon'];
            unset($options['icon']);
        }

        // @todo: move to knp_menu or menu_component
        if ($options['style'] === 'header') {
            $options['attributes']['class'] = 'menu-header menu-title';
        }

        if (! $options['id']) {
            $options['id'] = $options['menu_code'];
        }
        return $options;
    }

    public function isGranted($attribute, $subject = null)
    {
        if (! $this->authorizationChecker) {
            throw new \Exception("call setAuthorizationChecker() before making this call.");
        }
        return $this->authorizationChecker ? $this->authorizationChecker->isGranted($attribute, $subject) : false;
    }
}
