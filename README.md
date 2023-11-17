# Bootstrap Bundle, based on Sneat Admin theme.

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


# bootstrap-icons bug
```bash
symfony new bootstrap-icons-bug --webapp --version=next --php=8.2 && cd bootstrap-icons-bug
composer config minimum-stability beta
composer config extra.symfony.allow-contrib true
composer req symfony/asset-mapper:^6.4 
bin/console importmap:require bootstrap-icons/font/bootstrap-icons.min.css
echo "import 'bootstrap/dist/css/bootstrap.min.css'" >> assets/app.js
bin/console make:controller App -i
sed -i "s|/app|/|" src/Controller/AppController.php 

cat > templates/app.html.twig <<'END'
{% extends 'base.html.twig' %}

{% block body %}
  icon: 
  <span class="bi bi-github"></span>
{% endblock %}
END

composer req survos/deployment-bundle
bin/console dokku:config bootstrap-icons-bug


```
