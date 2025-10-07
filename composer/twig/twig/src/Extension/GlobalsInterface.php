<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\3rdparty\Twig\Extension;

/**
 * Allows Twig extensions to add globals to the context.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @internal
 */
interface GlobalsInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getGlobals() : array;
}
