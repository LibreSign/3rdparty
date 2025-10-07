<?php

namespace OCA\Libresign\3rdparty;

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use OCA\Libresign\3rdparty\Twig\Environment;
use OCA\Libresign\3rdparty\Twig\Extension\EscaperExtension;
use OCA\Libresign\3rdparty\Twig\Node\Node;
use OCA\Libresign\3rdparty\Twig\Runtime\EscaperRuntime;
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function \OCA\Libresignrdparty\twig_raw_filter($string)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return $string;
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function \OCA\Libresignrdparty\twig_escape_filter(Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = \false)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return $env->getRuntime(EscaperRuntime::class)->escape($string, $strategy, $charset, $autoescape);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function \OCA\Libresignrdparty\twig_escape_filter_is_safe(Node $filterArgs)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return EscaperExtension::escapeFilterIsSafe($filterArgs);
}
