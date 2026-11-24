<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

/** @internal */
interface LongTermValidationApplierInterface
{
    public function apply(string $signedPdfContent) : string;
}
