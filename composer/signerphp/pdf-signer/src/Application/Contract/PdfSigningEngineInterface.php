<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\LocalSigningContextDto;
/** @internal */
interface PdfSigningEngineInterface
{
    public function sign(LocalSigningContextDto $context) : string;
}
