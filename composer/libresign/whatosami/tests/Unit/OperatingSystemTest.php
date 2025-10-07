<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor;

use OCA\Libresign\Vendor\LibreSign\WhatOSAmI\OperatingSystem;
use OCA\Libresign\Vendor\PHPUnit\Framework\Attributes\DataProvider;
use OCA\Libresign\Vendor\PHPUnit\Framework\TestCase;
/**
 * SPDX-FileCopyrightText: 2024-2024 LibreCode coop and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 * @internal
 */
final class OperatingSystemTest extends TestCase
{
    #[DataProvider('dataGetLinuxDistribution')]
    public function testGetLinuxDistribution(string $expected) : void
    {
        $id = \strtolower($expected);
        $tempFolder = \sys_get_temp_dir();
        @\mkdir($tempFolder . '/etc', 0755, \true);
        \file_put_contents($tempFolder . '/etc/os-release', <<<OS_RELEASE
NAME="{$expected} Linux"
ID={$id}
VERSION_ID=3.20.1
PRETTY_NAME="{$expected} Linux v3.20"
HOME_URL="https://{$id}linux.org/"
OS_RELEASE
);
        $instance = new OperatingSystem($tempFolder);
        $actual = $instance->getLinuxDistribution();
        $this->assertEquals(\strtolower($expected), \strtolower($actual));
    }
    public static function dataGetLinuxDistribution() : array
    {
        return [['Alpine'], ['Debian']];
    }
}
/**
 * SPDX-FileCopyrightText: 2024-2024 LibreCode coop and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 * @internal
 */
\class_alias('OCA\\Libresign\\Vendor\\OperatingSystemTest', 'OperatingSystemTest', \false);
