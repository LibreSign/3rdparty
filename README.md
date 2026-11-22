# LibreSign 3rdparty

Scoped 3rd party libraries that are necessary to run the LibreSign Nextcloud app.

This repository contains vendor dependencies processed with PHP-Scoper to avoid conflicts with Nextcloud core and other apps. All classes are prefixed with `OCA\Libresign\Vendor\`.

## Structure

The LibreSign 3rdparty repository has a different structure compared to Nextcloud's 3rdparty:

- **Scoped dependencies**: Located in `composer/` directory (not root)
- **Source dependencies**: Located in `vendor/` directory  
- **Scoper configuration**: Uses `scoper.inc.php` to apply namespace transformations
- **Automatic processing**: PHP-Scoper runs automatically via composer scripts

## Maintainer dependency update workflow

Follow the [LibreSign Developer Manual: Updating vendored dependencies](https://docs.libresign.coop/main/developer_manual/dependency-updates.html)
for the supported three-command process, CI, the cross-repository PR
sequence, merge order, signed commits/DCO and recovery. The automation
targets the matching code branch and never merges PRs automatically.

This README retains only the 3rdparty-specific Composer/PHP-Scoper
instructions; the cross-repository procedure is maintained centrally.
## Updating libraries manually

1. Make sure to use the latest version of composer: `composer self-update`
2. Edit composer.json and adjust the version of the library to the one to update to. Pay attention to use the full version number (i.e. ^5.3.14).
3. Run `composer update thevendor/thelib` (replace accordingly)
4. Delete all installed dependencies with `rm -rf ./composer/*/ ./vendor/*/`
5. Run `composer install --no-dev`
   - This automatically runs PHP-Scoper via post-install-cmd script
   - Scoped dependencies are generated in `composer/` directory
6. Run `git clean -X -d -f`
7. Run `composer dump-autoload`
8. Commit all changes onto a new branch with verified signature and DCO sign-off
9. You might need the following command for pushing if used as submodule: `git push git@github.com:libresign/3rdparty.git branchname`

## PHP-Scoper Integration

This repository uses PHP-Scoper to avoid namespace conflicts with Nextcloud core and other apps:

- **Namespace prefix**: `OCA\Libresign\Vendor\`
- **Configuration**: See `scoper.inc.php` for scoping rules and patchers
- **Output directory**: `composer/` (configured in scoper.inc.php)
- **Automatic execution**: Runs via composer post-install-cmd and post-update-cmd scripts

### Scoper Patchers

The `scoper.inc.php` file includes specific patchers for:
- **Twig**: Handles template compilation and function calls
- **Mpdf**: Manages PDF generation library scoping
- **phpseclib**: Cryptographic library adjustments  
- **pdfparser**: PDF parsing functionality

### Manual Scoper Execution

If you need to run PHP-Scoper manually:

```bash
# Install scoper dependencies first
composer bin all install --ansi

# Run scoper manually
php -d error_reporting=E_ALL\&~E_DEPRECATED\&~E_USER_DEPRECATED \
    vendor-bin/php-scoper/vendor/humbug/php-scoper/bin/php-scoper add-prefix --force

# Regenerate autoload
composer dump-autoload -o
```
