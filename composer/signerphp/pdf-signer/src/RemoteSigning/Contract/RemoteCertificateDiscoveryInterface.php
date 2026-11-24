<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\DTO\RemoteCertificate;
/** @internal */
interface RemoteCertificateDiscoveryInterface
{
    /** @return list<RemoteCertificate> */
    public function certificates(?string $identifier = null) : array;
}
