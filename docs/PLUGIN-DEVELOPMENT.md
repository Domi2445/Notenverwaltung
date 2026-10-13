# Nexora Plugin Development

This document is the implementation contract for third-party Nexora module
developers. Nexora is available as Cloud hosting for 15 EUR per month or as a
self-hosted license for 50 EUR one time. From 100 customers, the self-hosted
license is reduced to 45 EUR one time. A module author may publish a module
with an additional one-time price or a recurring subscription.

## Contract

Implement `App\Plugins\Plugin` and register the class in
`config/plugins.php`. The manifest must be deterministic and must not perform
database writes in its constructor. Routes, migrations and other bootstrapping
belong in the plugin service provider.

Required methods:

| Method | Requirement |
| --- | --- |
| `id()` | globally unique, stable identifier such as `vendor.catalog` |
| `name()` | human-readable module name |
| `version()` | semantic version |
| `description()` | concise dashboard description |
| `icon()` | short text icon already supported by the UI |
| `accent()` | `primary` or `secondary` until theme tokens are expanded |
| `url()` | named route generated with `route()` |

## Package layout

Use a Composer package for distribution. The package should expose a service
provider through Composer package discovery and keep all implementation under
its own namespace. Do not modify Nexora core files from a module install.

```text
vendor/nexora/catalog/
  src/CatalogPlugin.php
  src/CatalogServiceProvider.php
  src/Models/
  src/Http/Controllers/
  database/migrations/
  resources/views/
  routes/web.php
  composer.json
```

## Tenant safety

Every business record must belong to an `Organization`. Always scope reads
and writes to the active organization. Do not trust an organization ID sent
by the browser; resolve it from the authenticated membership and authorize
the requested action. Add feature tests proving that a user from
organization A cannot read or modify organization B.

## Pricing metadata

Pricing belongs to the module author, not to the free Nexora core. The
platform subscription record supports:

- `plan_type=one_time`
- `plan_type=subscription`
- `price_cents`
- `currency`
- `status`

Nexora retains a 10% service fee on module revenue processed through the
platform to cover infrastructure, delivery and maintenance. Payment
processing is not yet connected. A production module must not claim
that payment succeeded until a verified provider webhook has been processed.

## Verification

Before publishing:

```powershell
composer validate
php artisan test
php artisan platform:plugins
npm run build
```

The module must document its permissions, data retention, required PHP
extensions, migrations, upgrade path and support contact.
