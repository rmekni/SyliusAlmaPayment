# Alma for Sylius 2 (fork)

Fork of the official [alma/sylius-payment-plugin](https://github.com/alma/AlmaSyliusPaymentPlugin)
**2.1.0** (MIT), made compatible with **Sylius 2**. It is not an official Alma package.
Same PHP namespace (`Alma\SyliusPaymentPlugin`) and same gateway (`alma_payments`), so it replaces
the official plugin without any change to existing payment methods; `composer.json` declares
`"replace": {"alma/sylius-payment-plugin": "*"}`.

## Changes from Alma 2.1.0

- `Bridge/AlmaBridge.php`: the Sylius version is read from `SyliusCoreBundle::VERSION`
  (`Sylius\Bundle\CoreBundle\Application\Kernel` no longer exists in Sylius 2).
- `services/data_builders.yml`: Sylius 2 service ids `sylius.resolver.shipping_methods` and
  `sylius.resolver.tax_rate` (were `sylius.shipping_methods_resolver`, `sylius.tax_rate_resolver`).
- Removed the Sylius 1 "sonata block" JavaScript listener (`services/assets.yml`, class removed
  from Sylius) and the Sylius 1 checkout template overrides (`views/bundles/SyliusShopBundle`).
- Admin: the gateway settings are shown on the Sylius 2 payment method form through Twig hooks
  (`AlmaSyliusPaymentExtension::prepend()`, `views/admin/payment_method/gateway_configuration.html.twig`).
- `alma/alma-php-client` pinned to `2.6.1`; requires PHP 8.2+ and Sylius 2.
- Removed Alma's CI configuration (`.github`, `.travis.yml`). The Sylius 1 test application in
  `tests/` is kept for reference but is not runnable as is.

## Installation (Sylius 2)

```json
"repositories": [{"type": "vcs", "url": "https://github.com/rmekni/SyliusAlmaPayment.git"}],
"require": {"agenceweb/alma-sylius-payment-plugin": "@dev"}
```

Register `Alma\SyliusPaymentPlugin\AlmaSyliusPaymentPlugin::class => ['all' => true]` in
`config/bundles.php`.

---

Original README:

<p align="center">
    <img src="src/Resources/public/img/alma-logo.svg" alt="logo alma" height="75" style="margin-right:30px" />
    <img src="https://demo.sylius.com/assets/shop/img/logo.png" height="75" />
</p>

<h1 align="center">Sylius Alma Payment Plugin</h1>

<p align="center">Integrate Alma installments and pay later payments with your Sylius shop</p>

## Documentation

### Installation
1. Use Composer to install the plugin:

```
$ composer require alma/sylius-payment-plugin
```

2. Import routes:

```
# config/routes/sylius_shop.yaml

sylius_alma:
    resource: "@AlmaSyliusPaymentPlugin/Resources/config/shop_routing.yaml"
    prefix: /{_locale}
    requirements:
        _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$
```

3. Override Sylius' templates:

```
cp -R vendor/alma/sylius-payment-plugin/src/Resources/views/bundles/* templates/bundles/
```

4. Export assets:

```
bin/console sylius:install:asset
```

5. Update your shop's translation catalogs:

```
$ php bin/console translation:update --dump-messages fr AlmaSyliusPaymentPlugin 
$ php bin/console translation:update --dump-messages en AlmaSyliusPaymentPlugin 
```

6. Finally, clear your cache:

```
$ php bin/console cache:clear
```

### Requirements

- PHP version >= 7.3
- Sylius version >= 1.9.0

Alma currently accepts Euros only; make sure you activate your payment method on channels that use that currency, else 
you won't see it at checkout.

Your Alma payment methods will only show for eligible carts. Eligibility is mainly based on the purchased amount, which
by default should be between 100€ and 2000€; if you want those limits changed, you can talk to your sales representative
at Alma, or contact [support@getalma.eu](mailto:support@getalma.eu).

### Usage
1. Go to the Payment Methods admin page and choose to create a new "Alma Payments" method

2. Grab your API keys [from your dashboard](https://dashboard.getalma.eu/api) and paste them into the appropriate fields

3. Choose the installments count to apply for this payment method. If you want to offer multiple installments counts to 
   your customers, you can create one Alma payment method per installments count.

4. Set the API mode to Test if you want to first test the integration with a fake credit card, on your preproduction 
   servers for instance.  
   When you're ready for production, set the API mode to Live.

5. Choose a name for your method in the languages relevant to your shop.

6. You're done! Save the payment method to start accepting instalments payments on your shop!
