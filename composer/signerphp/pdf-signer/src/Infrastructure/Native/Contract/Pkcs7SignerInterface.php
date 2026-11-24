<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderInterface;
/** @internal */
interface Pkcs7SignerInterface
{
    public function sign(Buffer $signableDocument, SignatureProviderInterface $signatureProvider, string $certificatePem) : string;
}
