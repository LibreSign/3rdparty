<?php

/**
 * SPDX-FileCopyrightText: 2025 LibreCode coop and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

// based on Arthur Schiwon blogpost:
// https://arthur-schiwon.de/isolating-nextcloud-app-dependencies-php-scoper

return [
	'prefix' => 'OCA\\Libresign\\Vendor',
	'output-dir' => 'composer',
	'finders' => [
		Finder::create()->files()
			->exclude([
				'bamarni',
				'bin',
				'roave',
			])
			->in('vendor'),
	],
	'patchers' => [
		// patchers for twig
		static function (string $filePath, string $prefix, string $content): string {
			if (!str_contains($filePath, 'twig/twig') || !str_ends_with($filePath, '.php')) {
				return $content;
			}
			// correct use statements in generated templates
			if (str_ends_with($filePath, 'twig/src/Node/ModuleNode.php')) {
				return str_replace('"use Twig\\', '"use ' . str_replace('\\', '\\\\', $prefix) . '\\\\Twig\\', $content);
			}

			// ignore files where functions are declared
			if (str_contains($filePath, 'twig/src/Resources/')) {
				return $content;
			}

			// Fix \Twig\ FQCN references inside PHP string literals that are emitted as raw code
			// into compiled templates (e.g. ->raw('...\Twig\Extension\CoreExtension::...')).
			// php-scoper scopes actual class references but cannot scope names inside strings.
			$content = str_replace(
				'\\\\Twig\\\\',
				'\\\\' . str_replace('\\', '\\\\', $prefix) . '\\\\Twig\\\\',
				$content
			);

			return $content;
		},
		// patchers for Mpdf
		static function (string $filePath, string $prefix, string $content): string {
			if (!str_contains($filePath, 'mpdf/mpdf') || !str_ends_with($filePath, '.php')) {
				return $content;
			}
			$searchReplacePairs = [
				'\\\\Mpdf\\\\' => '\\\\' . $prefix . '\\\\Mpdf\\\\',
				"'Mpdf\\\\" => "'" . $prefix . '\\\\Mpdf\\\\',
				"'\\\\Mpdf\\\\" => "'\\\\" . $prefix . '\\\\Mpdf\\\\',
				'@var \\\\Mpdf\\\\' => '@var \\\\' . $prefix . '\\\\Mpdf\\\\',
				'use Mpdf\\\\' => 'use ' . $prefix . '\\\\Mpdf\\\\',
				'namespace Mpdf\\\\' => 'namespace ' . $prefix . '\\\\Mpdf\\\\',
			];
			foreach ($searchReplacePairs as $search => $replace) {
				$content = str_replace($search, $replace, $content);
			}

			$file = basename($filePath);

			return match ($file) {
				'FpdiTrait.php' => str_replace('use \\setasign\\', "use \\$prefix\\setasign\\", $content),
				'Mpdf.php' => str_replace(["$prefix\\\\r\\\\n", "$prefix\\\\</t"], ['\\r\\n', '</t'], $content),
				'functions.php' => str_replace("namespace $prefix;", '', $content),
				'LoggerAwareInterface.php',
				'LoggerAwareTrait.php',
				'MpdfPsrLogAwareTrait.php',
				'PsrLogAwareTrait.php' => str_replace("\\$prefix\\Psr\\Log\\LoggerInterface", '\\Psr\\Log\\LoggerInterface', $content),
				default => $content,
			};
		},
		// patchers for pdfparser
		static function (string $filePath, string $prefix, string $content): string {
			if (!str_contains($filePath, 'smalot/pdfparser') || !str_ends_with($filePath, '.php')) {
				return $content;
			}
			$s_prefix = str_replace('\\', '\\\\', $prefix);
			$content = str_replace("'\\\\Smalot\\\\PdfParser", "'\\\\" . $s_prefix . '\\\\Smalot\\\\PdfParser', $content);
			return $content;
		},
		// patchers for phpseclib
		// phpseclib uses string-based class references for dynamic class loading
		// (factory patterns, plugin systems) that php-scoper cannot rewrite automatically.
		// php-scoper doubles backslashes in single-quoted strings during processing, so
		// patchers must match the POST-scoper form (double-backslash) not the original source.
		// Pattern 1: '\\phpseclib3\\Class\\...' post-scoper form of dynamic class name strings
		//   Appears in: EC curve lookup, BigInteger engine lookup, X509 callable arrays, EC format keys
		// Pattern 2: \\phpseclib3\\Common\\Functions\\Strings:: in eval code strings (SymmetricKey.php)
		static function (string $filePath, string $prefix, string $content): string {
			if (!str_contains($filePath, 'phpseclib/phpseclib') || !str_ends_with($filePath, '.php')) {
				return $content;
			}
			$s_prefix = str_replace('\\', '\\\\', $prefix);
			// Post-scoper pattern A: '\\phpseclib3\\ (apostrophe + double-backslash BEFORE phpseclib3)
			// Original source had '\phpseclib3\...; scoper doubled the single backslashes.
			// Covers EC.php, Engine.php, X509.php, EC/Formats/Keys/JWK|OpenSSH|XML.php
			$content = str_replace("'\\\\phpseclib3\\\\", "'\\\\" . $s_prefix . "\\\\phpseclib3\\\\", $content);
			// Post-scoper pattern B: 'phpseclib3\\ (apostrophe + phpseclib3 + double-backslash, no leading backslash)
			// Original source had 'phpseclib3\...; scoper doubled the backslashes.
			// Covers AsymmetricKey.php, Math/BigInteger.php, EC/Formats/Keys/Common.php
			$content = str_replace("'phpseclib3\\\\", "'" . $s_prefix . "\\\\phpseclib3\\\\", $content);
			// Post-scoper pattern C: \\phpseclib3\\Common\\Functions\\Strings:: in eval code strings
			// Covers SymmetricKey.php inline eval blocks
			$content = str_replace("\\\\phpseclib3\\\\Common\\\\Functions\\\\Strings::", "\\\\" . $s_prefix . "\\\\phpseclib3\\\\Common\\\\Functions\\\\Strings::", $content);
			return $content;
		},
	],
];
