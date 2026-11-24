<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\BirdId;

/** @internal */
final readonly class BirdIdSlot
{
    public function __construct(public string $alias, public string $label)
    {
    }
}
