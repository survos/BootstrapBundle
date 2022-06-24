# Bootstrap Bundle

A simple template with a navbar and login for Symfony, using Bootstrap 5

```bash
composer req survos/bootstrap-bundle
composer req survos/maker-bundle --dev

```

```twig


```

To set default values (@todo: install recipe)
```yaml
# config/packages/barcode.yaml
barcode:
  widthFactor: 3
  height: 120
  foregroundColor: 'purple'
```

```bash
symfony new BarcodeDemo --webapp
yarn install 
bin/console make:controller AppController
composer req survos/barcode-bundle
echo "{{ 'test'|barcode }} or {{ barcode('test', 2, 80, 'red') }} " >> templates/app/index.html.twig
symfony server:start -d

```
