# LibreSign 3rdparty

Scoped 3rd party libraries that are necessary to run the LibreSign Nextcloud app.

This repository contains vendor dependencies processed with PHP-Scoper to avoid conflicts with Nextcloud core and other apps. All classes are prefixed with `OCA\Libresign\Vendor\`.

## Structure

The LibreSign 3rdparty repository has a different structure compared to Nextcloud's 3rdparty:

- **Scoped dependencies**: Located in `composer/` directory (not root)
- **Source dependencies**: Located in `vendor/` directory  
- **Scoper configuration**: Uses `scoper.inc.php` to apply namespace transformations
- **Automatic processing**: PHP-Scoper runs automatically via composer scripts

## Automated dependency updates

For a Dependabot pull request targeting this branch, use the workflow
documented on [the 3rdparty main branch](https://github.com/LibreSign/3rdparty/blob/main/README.md#updating-dependencies).

1. Review the dependency change in the **3rdparty** PR. Comment
   `/composer-update` there. The bot acknowledges the request (👀), publishes
   a status comment and, when needed, commits regenerated scoped Composer
   files with a verified signature and DCO.
2. Wait until **all 3rdparty PR checks pass**, including
   `Check vendor changes`, PHP lint and DCO. A bot 👍 means that regeneration
   completed, **not** that CI or review has passed.
3. On that same 3rdparty PR, comment `/libresign-pr`. Verify that the bot
   creates a corresponding PR targeting **the same stable branch** in
   [LibreSign/libresign](https://github.com/LibreSign/libresign/pulls).
   This initially uses the source PR's proposed commit.
4. Review the LibreSign integration and its CI. After the required reviews,
   **merge the 3rdparty PR first**. In the LibreSign PR, comment
   `/update-3rdparty`. This adds a new signed, DCO-compliant commit pointing
   to the *merged* 3rdparty SHA; it must not amend or force-push history.
5. Wait for the new LibreSign CI and approvals, then merge that PR. Verify
   the LibreSign submodule gitlink matches the merged 3rdparty SHA.

**If the 3rdparty PR has already been merged** before `/libresign-pr`
created a destination PR, create a separate PR in LibreSign targeting the same
stable branch. Change only the `3rdparty` gitlink to the *merged SHA*, with
a new verified, DCO-signed commit. Do not attempt to recreate the original
source PR branch or rewrite its history.

**Merge DCO is a separate check.** The green DCO check on PR commits does
not prove that GitHub's subsequent merge commit has a `Signed-off-by`
trailer. Before confirming a merge, use a DCO-compliant merge method and
include a trailer matching the **effective GitHub merge author and email**.
After merging, verify the actual merge SHA, cryptographic signature and DCO;
never rewrite protected history to repair a missing trailer.

These commands run from the default-branch GitHub Actions workflows, only
for authorized maintainers and same-repository PR branches. They never merge
PRs automatically. For a manual alternative, see below.

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

## Testing a manual 3rdparty PR in LibreSign

If automation is unavailable, create a separate signed, DCO-compliant PR
in `LibreSign/libresign` pointing to the *current* 3rdparty PR head SHA;
test and review that integration without merging the LibreSign PR yet.
After merging the 3rdparty PR, **add a new signed, DCO-compliant commit**
updating the same gitlink to the 3rdparty **merge SHA**. Rerun LibreSign CI
and obtain approval before merging the LibreSign PR.

Do not use `git commit --amend` or `git push --force-with-lease` to
update a reviewed submodule PR: preserve incremental commits and review
history. Verify the final merge commits' signatures and DCO trailers.
