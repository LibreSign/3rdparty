<?php

// SPDX-FileCopyrightText: 2026 LibreCode coop and contributors
// SPDX-License-Identifier: AGPL-3.0-or-later
declare (strict_types=1);
namespace OCA\Libresign\Vendor;

use OCA\Libresign\Vendor\Rector\Config\RectorConfig;
return static function (RectorConfig $rectorConfig) : void {
    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);
    $rectorConfig->phpVersion(\OCA\Libresign\Vendor\Rector\ValueObject\PhpVersion::PHP_82);
    $rectorConfig->sets([\OCA\Libresign\Vendor\Rector\Set\ValueObject\SetList::CODE_QUALITY, \OCA\Libresign\Vendor\Rector\Set\ValueObject\SetList::TYPE_DECLARATION]);
};
