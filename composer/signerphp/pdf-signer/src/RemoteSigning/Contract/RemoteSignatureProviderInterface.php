<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ExternalSigningPayload;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValue;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\DTO\RemoteCertificate;
/** @internal */
interface RemoteSignatureProviderInterface
{
    public function sign(ExternalSigningPayload $payload, RemoteCertificate $certificate) : SignatureValue;
}
