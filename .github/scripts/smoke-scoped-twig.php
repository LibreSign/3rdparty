<?php
/**
 * SPDX-FileCopyrightText: 2026 LibreCode coop and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/composer/composer/ClassLoader.php';
require dirname(__DIR__, 2) . '/composer/autoload.php';

$twig = new \OCA\Libresign\Vendor\Twig\Environment(
    new \OCA\Libresign\Vendor\Twig\Loader\ArrayLoader([
        'escaped' => 'Hello {{ name }}',
        'loop' => '{% for item in items %}{{ item }}{% endfor %}',
    ]),
    ['autoescape' => 'html'],
);

$escaped = $twig->render('escaped', ['name' => '<script>']);
if ($escaped !== 'Hello &lt;script&gt;') {
    throw new \RuntimeException('Scoped Twig autoescaping regression: ' . $escaped);
}
$loop = $twig->render('loop', ['items' => ['a', 'b', 'c']]);
if ($loop !== 'abc') {
    throw new \RuntimeException('Scoped Twig loop rendering regression: ' . $loop);
}
echo "Scoped Twig rendering smoke test passed.\n";
