<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore;

/** @internal */
readonly class AppearanceXObject
{
    /**
     * @param  string  $stream  PDF content stream operators for the n2 layer
     * @param  array<string, mixed>|null  $resources  Resource dictionary (fonts, XObjects, etc.)
     */
    public function __construct(public string $stream, public ?array $resources = null)
    {
    }
}
