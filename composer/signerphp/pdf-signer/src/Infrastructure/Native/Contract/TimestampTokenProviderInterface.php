<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
/** @internal */
interface TimestampTokenProviderInterface
{
    /**
     * @param  array{0:int,1:int,2:int,3:int}  $byteRange
     */
    public function requestTokenHex(string $signableDocument, array $byteRange, TimestampOptionsDto $options) : string;
}
