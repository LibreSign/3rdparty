<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\Sandbox;

use OCA\Libresign\Vendor\Twig\Source;
/**
 * Interface for a class that can optionally enable the sandbox mode based on a template's Twig\Source.
 *
 * @author Yaakov Saxon
 * @internal
 */
interface SourcePolicyInterface
{
    public function enableSandbox(Source $source) : bool;
}
