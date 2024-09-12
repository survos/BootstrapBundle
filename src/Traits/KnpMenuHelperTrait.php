<?php

// Simplies the construction of menu items,
namespace Survos\BootstrapBundle\Traits;

use Google\Auth\Cache\Item;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Survos\BootstrapBundle\Event\KnpMenuEvent;
use Survos\CoreBundle\Entity\RouteParametersInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function Symfony\Component\String\u;

trait KnpMenuHelperTrait
{
//    private ?AuthorizationCheckerInterface $authorizationChecker = null;
    //    private ?ParameterBagInterface $bag=null;

    //    private ?array $options;
    private array $childOptions=[];
    protected string $env;

    public function supports(KnpMenuEvent $event): bool
    {
        return true;
    }

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function addSubmenu(ItemInterface $menu, ?string $label = null,
                               ?string $icon = null,
                               ?string $id = null,
                               ?string $translationDomain=null,
    array $extras = []
    ): ItemInterface
    {
        $subMenu = $this->add($menu,
            label: $label,
            icon: $icon,
            id: $id,
            returnItem: true // get the child, not the menu
        );
        if ($translationDomain) {
            $subMenu->setExtra('translation_domain', $translationDomain);
        }
        $subMenu->setExtra('is_submenu', true);
        foreach ($extras as $extra=>$extraVal) {
            $subMenu->setExtra($extra, $extraVal);
        }
        return $subMenu;
    }

    public function addHeading(ItemInterface $menu, string $label, string $icon = null, ?string $translationDomain=null): void
    {
        $item = $this->addMenuItem($menu, [
            'label' => $label,
            'style' => 'header',
            'icon' => $icon,
            'translation_domain' => $translationDomain,
            'id' => (new AsciiSlugger())->slug($label)->toString()
        ]);

    }

    private function createId(ItemInterface $menu): string
    {
        $label = $menu->getLabel();
        return (new AsciiSlugger())->slug($label)->toString() . '_' . uniqid();
    }

    // add returns self, for chaining, by default.  Pass returnItem: true to get the item for adding options.
    public function add(
        ItemInterface $menu,
        ?string $route = null,
        array|RouteParametersInterface|null $rp = null,
        ?string $label = null,
        ?string $uri = null,
        ?string $id = null,
        ?string $icon = null,
        string|int|null $badge = null,
        ?bool $external = null,
        bool $returnItem = false,
        bool $if = true,
        bool $dividerPrepend = false,
        bool $dividerAppend = false,
        ?string $translationDomain = 'routing', // from the method names
        array $translationParams = [], // e.g. count

    ): self|ItemInterface { // for nesting.  Leaves only, requires route or uri.

        assert(! ($route && $uri));

        // if the condition is false, don't bother to addit
        $options = [];
        if ($route) {
            $options['route'] = $route;
        }

        if ($badge) {
            $options['badge'] = $badge;
        }

        if ($rp) {
            $options['routeParameters'] = is_array($rp) ? $rp : $rp->getrp();
        }
        if ($icon) {
            $options['icon'] = $icon;
        }

        if (! $label) {
            $label = $route  ?? $uri; // @todo, be smarter.
        }

        if ($route) {
            if (isset($this->menuService)) {
                $routeRequirements = $this->menuService->getRouteRequirements()[$route]??[];
                    foreach ($routeRequirements as $y) {
                        // in order to check security, $rp must be an entity
                        if (is_a($rp, RouteParametersInterface::class)) {
                            if (!$this->isGranted($routeRequirements[0], $rp)) {
                                return $this;
                            }
                        } else {
                            // probably a ROLE_ or an expression
                            if (!$this->isGranted($routeRequirements[0])) {
                                return $this;
                            }
                        }
//                        dump( $routeRequirements);
//                            if (!$this->isGranted(...array_values($routeRequirements))) {
//                                $label .= " NOT AUTHORIZED?";
//                            dd(sprintf('rejecting %s %s %s', $route, json_encode($routeRequirements), $this->security->getUser()));
//                                return $this;
//                            }
//                        try {
//                        } catch (\Exception $exception) {
//                            dd($y, $exception->getMessage());
//                        }
                    }
            }
        }

        // if not valid, return so other items can be chained.
        if (!$if) {
            return $this;
        }

        $options['label'] = $label;
        if (! $id) {
            $id = $this->createId($menu);
        }

        $child = $menu->addChild($id, $options);
        if (!$label) {
            $child->setLabel(' '); // ideally so id isn't used.
        }
//        if (!$label) dd($id, $options, $child->getName());
        if ($uri) {
            $child->setUri($uri);
            if ($external !== false) {
                $external = true;
            }
        }
        if ($external) {
            $child->setLinkAttribute('target', '_blank');
            if (!$icon) {
                $options['icon'] = 'tabler:external-link';
            }
        }




        // hack to align navigation if no link

        if (!$child->getExtra('translation_domain')) {
            if ($translationDomain) {
                $child->setExtra('translation_domain', $translationDomain);
                $child->setExtra('translation_params', $translationParams);
            }
        }
//        if ($child->getName() == 'login') dd($child->getExtras());


        // now add the various classes based on the style.  Unfortunately, this happens in the menu_get, not the render.
        $child->setLabel($label);

        if ($options['icon']??false) {
            $child->setAttribute('icon', $options['icon']);
        }

        if ($dividerPrepend) {
            $child->setAttribute('divider_prepend', true);
        }
        if ($dividerAppend) {
            $child->setAttribute('divider_append', true);
        }

        $options = $this->menuOptions($options);
        $this->setChildOptions($child, $options);
        $child->setExtra('safe_label', true);

            $child->setExtra('is_submenu', $options['is_submenu']);
            $child->setAttribute('is_submenu', $options['is_submenu']);
        if ($options['is_submenu']) {
        }


        if ($dividerAppend) {
//            dd($label, $child->getLabel());
        }


        return $returnItem ? $child : $this;
    }

    public function addMenuItem(ItemInterface $menu, array $options, array $extra = []): ItemInterface
    {
        assert(count($extra) === 0, json_encode($extra));
        $options = $this->menuOptions($options);
        // must pass in either route, icon or menu_code

        // especially for collapsible menus.  Cannot start with a digit.
        if (!$options['id']) {
            $options['id'] = 'id_' . (new AsciiSlugger())->slug($options['label']??'')->toString() . '_' . md5(json_encode($options));
        }

        $child = $menu->addChild($options['id'], $options);
        $this->setChildOptions($child, $options);
        return $child;
        //        $child->setChildrenAttribute('class', 'branch');

    }

    private function setChildOptions(ItemInterface $child, array $options): ItemInterface
    {

        if ($options['external']) {
            $child->setLinkAttribute('target', '_blank');
            $options['icon'] = 'tabler:external-link';
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
        if (! empty($options['safe_label'])) {
            $child->setExtra('safe_label', true);
        }


        if ($routes = $options['routes']??false) {
            $child->setExtra('routes', $routes);
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
                'routeParameters' => [],
                'routes' => null,
                'external' => false,
                'is_submenu' => false,
                '_fragment' => null,
                'label' => '', // null will use id as label
                'icon' => null,
                'badge' => null,
                'feather' => null,
                'uri' => null,
                'classes' => [], // this doesn't feel quite right.  Maybe a "style: header"?
                'style' => null,
                'childOptions' => $this->childOptions,
                'description' => null,
                'attributes' => [],
                'translation_domain' => 'routing'
            ])->resolve($options);

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
            if ($options['label'] !== false) {
                $options['label'] = u($routeLabel)->replace('_', ' ')->title(true)->toString();
            }
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
        //        if ($options['label'] == 'Layouts') {
        //            dd($options);
        //        }

        if ($options['style'] === 'header') {
            // @warning: will probably break sneat!
//            $options['attributes']['class'] = 'menu-header menu-title';
            $options['attributes']['class'] = 'menu-title';
        }

        if (! $options['id']) {
            $options['id'] = $options['menu_code'];
        }
        return $options;
    }

    public function isGranted($attribute, $subject = null)
    {
        return $this->security->isGranted($attribute, $subject);

        if (! $this->authorizationChecker) {
            throw new \Exception("call setAuthorizationChecker() before making this call.");
        }
        return $this->authorizationChecker ? $this->authorizationChecker->isGranted($attribute, $subject) : false;
    }

    public function isEnv(string $envName): bool
    {
        return $this->env === $envName;
    }

    public function authMenu(AuthorizationCheckerInterface $authorizationChecker,
                             Security $security,
                             ItemInterface                 $menu,
                             $childOptions = []): ItemInterface
    {


        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $security->getUser();
            $subMenu = $this->addSubmenu($menu,
                $user->getUserIdentifier(),
                id: 'user_menu'
            );

            $subMenu->setExtra('btn', 'btn btn-info');

            if ($this->isGranted('IS_IMPERSONATOR')) {
                $this->add($subMenu, '');
            }
            $subMenu->addChild(
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

    public function addWorkflowMenu(ItemInterface $menu)
    {
        assert(isset($this->workflowHelperService), 'add protected WorkflowHelperService $workflowHelperService to the __constructor of ' . static::class);
        $workflowMenu = $this->addSubmenu($menu, 'Workflows', icon: 'fas fa-diagram-project');
        $this->add($workflowMenu, route: 'survos_workflows', label: "All");
        foreach ($this->workflowHelperService->getWorkflowsIndexedByName() as $workflowCode => $workflow) {
            $this->add($workflowMenu, 'survos_workflow', [
                'flowCode' => $workflowCode,
            ], $workflowCode);
        }


    }

}
