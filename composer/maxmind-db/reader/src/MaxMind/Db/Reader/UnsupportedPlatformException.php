<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\MaxMind\Db\Reader;

/**
 * The platform cannot decode a database value or offset.
 *
 * @final
 * @internal
 */
// phpcs:ignore PSR2.Classes.ClassDeclaration, Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
class UnsupportedPlatformException extends \RuntimeException
{
}
