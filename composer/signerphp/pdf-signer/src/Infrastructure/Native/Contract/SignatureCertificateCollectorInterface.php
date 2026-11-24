<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

/** @internal */
interface SignatureCertificateCollectorInterface
{
    /**
     * @return array<int, string> DER-encoded certificate binaries
     */
    public function collectDerCertificates(string $signatureHex) : array;
}
