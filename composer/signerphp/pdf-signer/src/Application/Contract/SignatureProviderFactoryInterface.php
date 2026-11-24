<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\ValueObject\VerifiedCertificate;
/** @internal */
interface SignatureProviderFactoryInterface
{
    public function create(VerifiedCertificate $certificate) : SignatureProviderInterface;
}
