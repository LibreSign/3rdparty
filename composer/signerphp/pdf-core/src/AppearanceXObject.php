<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore;

use Closure;
/** @internal */
readonly class AppearanceXObject
{
    /**
     * @param  string  $stream  PDF content stream operators for the n2 layer
     * @param  array<string, mixed>|null  $resources  Resource dictionary (fonts, XObjects, etc.)
     * @param  (Closure(PdfDocument, array<string, mixed>): array<string, mixed>)|null  $resourcesBuilder
     *                                                                                                     Materialize resource objects in the destination document before appearance serialization.
     */
    public function __construct(public string $stream, public ?array $resources = null, public ?Closure $resourcesBuilder = null)
    {
    }
}
