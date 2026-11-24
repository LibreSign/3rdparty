<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\PdfProtectionEngineInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ProtectPdfRequestDto;
/** @internal */
final readonly class PdfProtectionService
{
    public function __construct(private PdfProtectionEngineInterface $protectionEngine)
    {
    }
    public function protect(ProtectPdfRequestDto $request) : string
    {
        return $this->protectionEngine->protect($request);
    }
}
