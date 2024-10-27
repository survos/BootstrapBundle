# Bootstrap Bundle

A common bootstrap layout, including menus and several components.

This bundle leverages several Symfony components, and as such loads several dependencies, and is a fairly opinionated bundles.  Adhering to the coding suggestions offers several benefits.

## Layout

All of the themes are based on Bootstrap 5.3, and tabler is the recommended one.
The base layout has various menus: navbar, footer, page_header

## Menus

The menu system is based on event listeners.  While these are a bit complicated at first, benefits include:

* Context-specific menus (e.g. a separate menu for each entity, e.g. a Project menu, with its own navbar and footer.
* Automatic security -- does not render a menu item if IsGranted attribute blocks the user
* Automatic translation: uses the controller name (rather than route name) as the default label.

```bash
bin/console survos:make:menu App
```

```php
    #[AsEventListener(event: KnpMenuEvent::NAVBAR_MENU2, priority: 50)]
    public function navarButtonsMenu(KnpMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $this->add($menu, 'app_homepage');
        $this->add($menu, 'event_index');
    }

    #[AsEventListener(event: KnpMenuEvent::NAVBAR_MENU)]
    public function navbarMenu(KnpMenuEvent $event): void
    {
        if (!$this->supports($event)) {
            return;
        }
    }

```

Pass context-specific menu data by first configuring the allowable fields in menu_options of survos_bootstrap.yaml

```yaml
survos_bootstrap:
  routes:
      login: app_login
      logout: app_logout
      register: app_register
      homepage: app_homepage
  menu_options:
      entityClass: null
      project: null
      frontPage: false
      showAppMenu: false
```

```twig
{% block footer %}
<twig:menu :type="FOOTER_MENU" :caller="_self"
           :options="{owner: owner,
                entityClass: ownerClass,
               project:project|default(null)}"
               >
</twig:menu>
```

## Entity Routes

Entity routes can be generated in the menu component without having to remember the parameters.

```php
            if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                $subMenu = $this->addSubmenu($menu, 'super_admin_index');
                $this->add($subMenu, 'owner_references', $owner);
                $this->add($subMenu, 'owner_dump', $owner, external: true);
```

the $owner class must implement getUniqueParameters(), which is used by the getRp() method.
At the moment, this requires implementing the listener which is clumbsy :-(

)

```bash
symfony new my-app --webapp && cd my-app
composer req survos/bootstrap-bundle
composer req survos/maker-bundle --dev

bin/console importmap:require @tabler/core
echo "import '@tabler/core'; 
import '@tabler/core/dist/css/tabler.min.css';
" >> assets/app.js

@todo: 
{% extends "@SurvosBootstrap/%s/base.html.twig"|format(theme_option('theme')) %}


bin/console make:controller App
sed  -i "s|'/app'|'/'|" src/Controller/AppController.php # the landing page controller
cat > templates/app/index.html.twig <<'END'
{% extends 'base.html.twig' %}
{% block body %}
<twig:alert message="hello" dismissible="true">
        <twig:block name="alert_message">
            I can override the alert_message block and access the {{ message }} too!
        </twig:block>
    </twig:alert>
{% endblock %}
END

```


## @todo:

* make:create-user-command

A simple template with a navbar and login for Symfony, using Bootstrap 5

```bash
composer req survos/bootstrap-bundle
composer req survos/maker-bundle --dev

```

```twig


```

To set default values (@todo: install recipe)
```yaml
# config/packages/bootstrap.yaml
bootstrap:
  widthFactor: 3
  height: 120
  foregroundColor: 'purple'
```

```bash
symfony new BootstrapDemo --webapp
bin/console make:controller AppController
composer req survos/bootstrap-bundle
echo "{{ 'test'|bootstrap }} or {{ bootstrap('test', 2, 80, 'red') }} " >> templates/app/index.html.twig
symfony server:start -d

```

## Knp Menu Bundle

```bash

symfony new menu7 --webapp --version=next && cd menu7
composer config minimum-stability dev
composer config prefer-stable false
composer config extra.symfony.allow-contrib true
#sed -i 's/"6.4.*"/"^7.0"/' composer.json
composer update
#composer config repositories.knp_menu_bundle '{"type": "vcs", "url": "git@github.com:tacman/KnpMenuBundle.git"}'
composer require knplabs/knp-menu-bundle
composer req symfony/asset-mapper
bin/console make:controller -i Menu

cat > templates/menu.html.twig <<END
{% extends 'base.html.twig' %}
{% block body %}
    {{ knp_menu_render('App:Builder:mainMenu') }}
{% endblock %}  
END

cat > src/Menu/Builder.php << 'END'
namespace App\Menu;

use App\Entity\Blog;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Builder 
{

    public function mainMenu(FactoryInterface $factory, array $options): ItemInterface
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', ['route' => 'app_app']);

        return $menu;
    }
}
END

symfony server:start -d
symfony open:local --path=/menu


```


# twig bug
```bash
symfony new test --webapp --version=7.0 && cd test
composer require symfony/ux-twig-component symfony/asset-mapper 
composer req survos/bootstrap-bundle
composer req survos/api-grid-bundle
composer req survos/crawler-bundle
composer req survos/command-bundle
composer req survos/barcode-bundle
echo "import 'bootstrap/dist/css/bootstrap.min.css'" >> assets/app.js

bin/console make:controller App
sed  -i "s|'/app'|'/'|" src/Controller/AppController.php # the landing page controller
cat > templates/app/index.html.twig <<'END'
{% extends 'base.html.twig' %}
{% block body %}
<twig:alert message="hello" dismissible="true">
        <twig:block name="alert_message">
            I can override the alert_message block and access the {{ message }} too!
        </twig:block>
    </twig:alert>
{% endblock %}
END

symfony server:start -d
symfony open:local --path=/app

cd ../ux
./link ../bug
cd ../bug
symfony open:local --path=/app


```
