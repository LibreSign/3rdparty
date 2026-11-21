# LibreSign 3rdparty

Vendored Composer dependencies for the LibreSign Nextcloud app. PHP-Scoper
isolates these libraries under `OCA\Libresign\Vendor\` to avoid namespace
conflicts with Nextcloud and other apps.

## Updating dependencies

Dependabot proposes updates for `main` and maintained stable branches. The
usual workflow is:

1. Review the dependency version and lockfile changes in the
   [3rdparty pull request](https://github.com/LibreSign/3rdparty/pulls).
2. A maintainer comments `/composer-update` on that PR to regenerate the
   vendored, scoped libraries and autoload files. Wait for the 3rdparty CI.
3. Only after the 3rdparty checks pass, comment `/libresign-pr` on the same PR to create a matching pull request
   in [LibreSign](https://github.com/LibreSign/libresign/pulls), targeting
   the corresponding branch and pointing to the proposed 3rdparty commit.
4. Review and test the integration in LibreSign. After approval, merge the
   3rdparty PR first.
5. Comment `/update-3rdparty` on the linked LibreSign PR to update its
   submodule pointer to the exact commit integrated into 3rdparty.
   Wait for the new LibreSign CI checks before merging that PR.

These commands are restricted to maintainers and pull-request branches hosted
in the respective repositories. They create or update PRs; **they do not merge
anything automatically**. For forks or unavailable automation, use the manual
procedure below.

### Manual fallback

In a clean, disposable 3rdparty working tree:

1. Update the version constraint in `composer.json`, if needed, and run
   `composer update vendor/package`.
2. Remove the generated `composer/*/` and `vendor/*/` directories, then run
   `composer install --no-dev`. The Composer scripts execute PHP-Scoper.
3. Run `git clean -X -d -f` and `composer dump-autoload -o`. **Note:**
   `git clean -X` permanently removes ignored files; inspect the working
   tree first.
4. Commit the changed manifest, lockfile and generated dependencies with DCO
   sign-off, then open a 3rdparty PR.
5. In `LibreSign/libresign`, create a separate branch and PR updating only
   the `3rdparty` submodule to the candidate commit. After the 3rdparty PR
   is merged, add another signed-off commit pointing to the integrated SHA.
   Rerun LibreSign CI before merging.

Do not amend or force-push merely to update the submodule pointer.

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
