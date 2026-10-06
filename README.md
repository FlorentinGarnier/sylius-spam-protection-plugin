# Sylius Spam Protection Plugin

[![CI](https://github.com/FlorentinGarnier/sylius-spam-protection-plugin/actions/workflows/ci.yml/badge.svg)](https://github.com/FlorentinGarnier/sylius-spam-protection-plugin/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/florentingarnier/sylius-spam-protection-plugin.svg)](https://packagist.org/packages/florentingarnier/sylius-spam-protection-plugin)
[![Total Downloads](https://img.shields.io/packagist/dt/florentingarnier/sylius-spam-protection-plugin.svg)](https://packagist.org/packages/florentingarnier/sylius-spam-protection-plugin)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
![Sylius](https://img.shields.io/badge/sylius-1.14-1abb9c.svg)

Protects the public forms of your Sylius shop against spam bots, without reCAPTCHA and without any puzzle for
your customers. It relies on
[florentingarnier/spam-protection-bundle](https://github.com/FlorentinGarnier/spam-protection-bundle): a
honeypot, single-use timed tokens, a proof of work solved by the browser, per-form rate limiting, IP reputation
and gibberish detection.

| Sylius form | Scope | Free text checked |
|-------------|-------|-------------------|
| Contact (`ContactType`) | `contact` | `message` |
| Registration (`CustomerRegistrationType`) | `registration` | — |
| Password reset request (`UserRequestPasswordResetType`) | `password_reset` | — |

## Requirements

- PHP 8.2 or later
- Sylius 1.14. Sylius 1.12 and 1.13 are no longer maintained: their dependencies have security advisories that
  will never be fixed. Use version 0.1 of the plugin with them.
- JavaScript in the customer's browser

Sylius 1.14 requires `api-platform/core` 2.7, whose releases are all affected by security advisories. Composer 2.9 and later refuses to install them unless your project ignores these advisories, as
this plugin does for its own CI in [composer.json](composer.json) (`config.policy.advisories.ignore-id`). This
concerns Sylius itself, not the plugin, which does not use API Platform.

### Sylius 2

Sylius 2 is not supported yet. Two changes are needed:

- its shop templates use Twig Hooks instead of template events, so the fields must be rendered through hooks;
- its registration form is a Live Component, which submits the form on every re-render: the protection must
  skip these validation requests, or it would count them as rejected attempts.

Contributions are welcome.

## Installation

1. Require the plugin:

   ```bash
   composer require florentingarnier/sylius-spam-protection-plugin
   ```

2. If you do not use Symfony Flex, enable the bundle and the plugin:

   ```php
   // config/bundles.php
   return [
       // ...
       FlorentinGarnier\SpamProtectionBundle\FlorentinGarnierSpamProtectionBundle::class => ['all' => true],
       FlorentinGarnier\SyliusSpamProtectionPlugin\FlorentinGarnierSyliusSpamProtectionPlugin::class => ['all' => true],
   ];
   ```

3. Load the JavaScript solver in your shop theme, as explained in the
   [bundle documentation](https://github.com/FlorentinGarnier/spam-protection-bundle#3-load-the-javascript-solver).
   Without it, the protected forms cannot be submitted.

4. Download the IP reputation lists, then schedule the command daily:

   ```bash
   bin/console spam-protection:refresh-ip-lists
   ```

Configuration is optional: see the [bundle documentation](https://github.com/FlorentinGarnier/spam-protection-bundle#configuration).

## How the fields are rendered

The Sylius shop templates end their forms with `render_rest: false`, so a field added to a form is not rendered
automatically. The plugin renders the protection in the forms' template events, with a priority of -100:

- `sylius.shop.contact.request.form`
- `sylius.shop.register.form`
- `sylius.shop.request_password_reset_token.form`

If your theme overrides these templates:

- **It still calls the template events:** nothing to do.
- **It renders `form.spam_protection` itself:** nothing to do either. The plugin skips a field that is already
  rendered.
- **It calls neither:** render the field in the form with `{{ form_row(form.spam_protection) }}`.

To remove the block from an event, for example when your theme renders the field elsewhere:

```yaml
# config/packages/sylius_ui.yaml
sylius_ui:
    events:
        sylius.shop.contact.request.form:
            blocks:
                florentin_garnier_spam_protection: false
```

## Protecting other forms

Any Symfony form can be protected with the bundle's form type, including your own shop forms:

```php
use FlorentinGarnier\SpamProtectionBundle\Form\SpamProtectionType;

$builder->add('spam_protection', SpamProtectionType::class, [
    'protection_scope' => 'quotation',
    'content_fields' => ['message'],
]);
```

## Testing

```bash
composer install
vendor/bin/phpunit
```

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) and the
[Code of Conduct](CODE_OF_CONDUCT.md). Report security issues privately, as
described in [SECURITY.md](SECURITY.md).

## License

Released under the [MIT License](LICENSE).
