<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureAppearanceDto;
/** @internal */
interface DefaultSignatureAppearanceProviderInterface
{
    public function makeDefault() : SignatureAppearanceDto;
}
