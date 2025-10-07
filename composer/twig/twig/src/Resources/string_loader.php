<?php

namespace OCA\Libresign\3rdparty;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use OCA\Libresign\3rdparty\Twig\Environment;
use OCA\Libresign\3rdparty\Twig\Extension\StringLoaderExtension;
use OCA\Libresign\3rdparty\Twig\TemplateWrapper;
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function \OCA\Libresignrdparty\twig_template_from_string(Environment $env, $template, ?string $name = null) : TemplateWrapper
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return StringLoaderExtension::templateFromString($env, $template, $name);
}
