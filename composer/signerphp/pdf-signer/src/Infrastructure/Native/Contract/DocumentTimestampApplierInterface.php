<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
/** @internal */
interface DocumentTimestampApplierInterface
{
    public function apply(string $signedPdfContent, TimestampOptionsDto $options) : string;
}
