<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ProtectPdfRequestDto;
/** @internal */
interface PdfProtectionEngineInterface
{
    public function protect(ProtectPdfRequestDto $request) : string;
}
