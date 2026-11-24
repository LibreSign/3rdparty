<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
/** @internal */
interface DefaultTimestampOptionsProviderInterface
{
    public function makeDefault() : TimestampOptionsDto;
}
