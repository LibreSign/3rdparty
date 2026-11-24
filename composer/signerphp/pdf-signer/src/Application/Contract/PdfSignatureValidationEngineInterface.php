<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValidationResultDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ValidatePdfRequestDto;
/** @internal */
interface PdfSignatureValidationEngineInterface
{
    public function validate(ValidatePdfRequestDto $request) : SignatureValidationResultDto;
}
