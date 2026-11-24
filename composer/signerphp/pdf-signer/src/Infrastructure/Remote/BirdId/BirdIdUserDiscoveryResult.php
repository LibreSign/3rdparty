<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\BirdId;

/** @internal */
final readonly class BirdIdUserDiscoveryResult
{
    /** @param list<BirdIdSlot> $slots */
    public function __construct(public bool $hasCertificate, public array $slots = [])
    {
    }
}
