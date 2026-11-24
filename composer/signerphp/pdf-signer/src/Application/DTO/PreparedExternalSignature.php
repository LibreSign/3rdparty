<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class PreparedExternalSignature
{
    public function __construct(public ExternalSigningPayload $payload, public string $state)
    {
    }
}
