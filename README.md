# LibreSign 3rdparty

Vendored Composer dependencies for the LibreSign Nextcloud app. PHP-Scoper
isolates these libraries under `OCA\Libresign\Vendor\` to avoid namespace
conflicts with Nextcloud and other apps.

## Updating dependencies

For the reviewed, cross-repository procedure, see
[Updating vendored dependencies in the LibreSign Developer Manual](https://docs.libresign.coop/main/developer_manual/dependency-updates.html).
It covers `/composer-update`, `/libresign-pr`, `/update-3rdparty`, required
checks, merge order, signed commits, DCO and recovery. These bot commands
never merge pull requests automatically.

### Regenerating scoped libraries locally

Use a clean, disposable checkout for Composer/PHP-Scoper changes:

1. Review `composer.json` and the intended version; when needed, run
   `composer update vendor/package`.
2. Remove existing generated package directories under `composer/` and
   `vendor/`, then run `composer install --no-dev` to regenerate libraries.
3. Inspect ignored paths before running `git clean -X -d -f`; it deletes
   ignored files. Run `composer dump-autoload -o` afterwards.
4. Review the manifest, lockfile and generated changes in the source PR.
   Preserve cryptographically verified, DCO-signed commits.

The cross-repository gitlink update, CI verification and merge procedure are
documented in the Developer Manual rather than duplicated here.

## PHP-Scoper

- Dependency requirements are defined in `composer.json`; downloaded sources
  live in `vendor/`, and scoped output is generated in `composer/`.
- `scoper.inc.php` contains namespace rules and special handling for Twig,
  mPDF, phpseclib and PDFParser.
- Composer's `post-install-cmd` and `post-update-cmd` regenerate the scoped
  libraries. Do not hand-edit generated dependencies.

For troubleshooting, PHP-Scoper can also be invoked explicitly after
installing its tooling:

```sh
composer bin all install --ansi
php -d error_reporting=E_ALL\&~E_DEPRECATED\&~E_USER_DEPRECATED \
    vendor-bin/php-scoper/vendor/humbug/php-scoper/bin/php-scoper add-prefix --force
composer dump-autoload -o
```
