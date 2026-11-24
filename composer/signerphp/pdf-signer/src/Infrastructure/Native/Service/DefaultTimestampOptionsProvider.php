<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\DefaultTimestampOptionsProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
/** @internal */
final class DefaultTimestampOptionsProvider implements DefaultTimestampOptionsProviderInterface
{
    public function makeDefault() : TimestampOptionsDto
    {
        return new TimestampOptionsDto(tsaUrl: 'https://freetsa.org/tsr', hashAlgorithm: 'sha256', certReq: \true, username: null, password: null, timeoutSeconds: 15);
    }
}
