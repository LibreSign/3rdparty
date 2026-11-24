<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\PdfSignatureValidationEngineInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValidationResultDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ValidatePdfRequestDto;
/** @internal */
final readonly class PdfSignatureValidationService
{
    public function __construct(private PdfSignatureValidationEngineInterface $validationEngine)
    {
    }
    public function validate(ValidatePdfRequestDto $request) : SignatureValidationResultDto
    {
        return $this->validationEngine->validate($request);
    }
}
