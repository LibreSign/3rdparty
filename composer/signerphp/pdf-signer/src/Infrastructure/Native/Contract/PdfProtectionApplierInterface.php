<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ProtectionOptionsDto;
/** @internal */
interface PdfProtectionApplierInterface
{
    public function apply(string $pdfContent, ProtectionOptionsDto $options) : string;
}
