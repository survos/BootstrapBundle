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
