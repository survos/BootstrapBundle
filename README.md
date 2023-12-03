# Bootstrap Bundle

A common bootstrap layout, including menus and several components

symfony new my-app --webapp && cd my-app
composer req symfony/asset-mapper
composer req survos/bootstrap-bundle
composer req survos/maker-bundle --dev

echo "import 'bootstrap/dist/css/bootstrap.min.css'" >> assets/app.js

bin/console importmap:require bootstrap-icons/font/bootstrap-icons.min.css
bin/console importmap:require bootswatch/cerulean/bootstrap.min.css

hello@themeselection.com

## @todo:

* make:create-user-command
* make:menu

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
yarn install 
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
