<?php // Simplies the construction of menu items,
namespace Survos\BootstrapBundle\Traits;

use Knp\Menu\ItemInterface;
use Survos\BootstrapBundle\Event\KnpMenuEvent;
use Survos\CoreBundle\Entity\RouteParametersInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use function Symfony\Component\String\u;


trait KnpMenuHelperTrait
{
    private ?AuthorizationCheckerInterface $authorizationChecker=null;
//    private ?ParameterBagInterface $bag=null;

//    private ?array $options;
    private $childOptions;

    public function supports(KnpMenuEvent $event): bool
    {
        return true;
    }

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }



    public function addSubmenu(ItemInterface $menu, ?string $label=null, ?string $icon=null): ItemInterface
    {
        // how internal,
        $subMenu = $this->addMenuItem($menu, [
            'label' => $label,
            'icon' => $icon
        ]);
        return $subMenu;
    }
    public function addHeading(ItemInterface $menu, string $label, string $icon=null): void
    {
        $this->addMenuItem($menu, [
            'label' => $label,
            'style' => 'header',
            'icon' => $icon
        ]);

    }

    // add returns self, for chaining, by default.  Pass returnItem: true to get the item for adding options.
    public function add(
        ItemInterface $menu,
        ?string $route=null,
        array|RouteParametersInterface|null $rp=null,
        ?string $label=null,
        ?string $uri=null,
        ?string $id=null,
        ?string $icon=null,
        bool $external = false,
        bool $returnItem = false,
    ): self|ItemInterface // for nesting.  Leaves only, requires route or uri.
    {
        if (!$id) {
            $id = uniqid();
        }
        assert(!($route && $uri));
        $options = [];
        if ($route) {
            $options['route'] = $route;
        }
        if ($rp) {
            $options['routeParameters'] = is_array($rp) ? $rp : $rp->getrp();
        }
        if ($icon) {
            $options['icon'] = $icon;
        }

        if (!$label) {
            $label = $route; // @todo, be smarter.
        }
        $options['label'] = $label;
        $child = $menu->addChild($id, $options);
        if ($external) {
            $child->setLinkAttribute('target',  '_blank');
            $options['icon'] = 'fas fa-external-alt';
        }

        // now add the various classes based on the style.  Unfortunately, this happens in the menu_get, not the render.
        $child->setLabel($label);

        return $returnItem ? $child : $this;

    }
    public function addMenuItem(ItemInterface $menu, array $options, array $extra=[]): ItemInterface
    {
        assert(count($extra) === 0, json_encode($extra));
        $options = $this->menuOptions($options);
        // must pass in either route, icon or menu_code

        // especially for collapsible menus.  Cannot start with a digit.
        if (!$options['id']) {
            $options['id'] = 'id_' . md5(json_encode($options));
        }

        $child = $menu->addChild($options['id'], $options);
//        $child->setChildrenAttribute('class', 'branch');

        if ($options['external']) {
            $child->setLinkAttribute('target',  '_blank');
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

        if (!empty($extra['safe_label'])) {
            $child->setExtra('safe_label', true);
        }

        // if this is a collapsible menu item, we need to set the data target to next element.  OR we can let knp_menu renderer handle it.
        if (!$options['route'] && !$options['uri']) {

            // only if there are children, but otherwise this is just a label
//            $child->setAttribute('collapse_type', 'collapse');
//            $child->setAttribute('class', 'collapse collapsed');
//            $child->setAttribute('data-bs-target', 'hmm');
        }

        if ($classes = $options['classes']) {
            $child->setAttribute('class', $classes);
        }

        if ($badge = $options['badge']) {
            $child->setExtra('badge', is_array($badge) ? $badge: ['value' => $badge]);
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
                'attributes' => []
            ])->resolve($options);

        // rename rp
        if (is_object($options['rp'])) {
            $options['routeParameters'] = $options['rp']->getRp();
            if (empty($options['icon'])) {
                $iconConstant = get_class($options['rp']) . '::ICON';
                $options['icon'] =  defined($iconConstant) ? constant($iconConstant) : 'fas fa-database'; // generic database entity
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
                         'edit' => 'fas fa-wrench'
                     ] as $regex=>$icon) {

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
//        if ($options['label'] == 'Layouts') {
//            dd($options);
//        }

        if ($options['style'] === 'header') {
            $options['attributes']['class'] = 'menu-header';
        }

        if (!$options['id']) {
            $options['id'] = $options['menu_code'];
        }
        return $options;
    }

    public function isGranted($attribute, $subject=null) {
        if (!$this->authorizationChecker) {
            throw new \Exception("call setAuthorizationChecker() before making this call.");
        }
        return $this->authorizationChecker ? $this->authorizationChecker->isGranted($attribute, $subject): false;
    }

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
