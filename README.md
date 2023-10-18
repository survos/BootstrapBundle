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

## Bootstrap CSS

```bash
symfony new bootstrap-64 --webapp --version=next && cd bootstrap-64 
composer config minimum-stability dev
composer config extra.symfony.allow-contrib true
bin/console --version
composer req symfony/asset-mapper
bin/console importmap:require bootstrap
bin/console make:controller -i ButtonController

cat > config/packages/twig.yaml << END
twig:
    default_path: '%kernel.project_dir%/templates'
    form_themes:
        - 'bootstrap_5_layout.html.twig'
        - 'bootstrap_5_horizontal_layout.html.twig'

when@test:
    twig:
        strict_variables: true
END

cat > assets/app.js <<END
import './styles/app.css'
import 'bootstrap/dist/css/bootstrap.min.css' // from importmap.php
console.log('app.js is loading app.css and bootstrap')
END

cat > templates/button.html.twig <<END
{% extends 'base.html.twig' %}
{% block body %}
    <button class="btn btn-primary">Primary</button>
{% endblock %}  
END

symfony server:start -d
symfony open:local --path=/button

```


```
