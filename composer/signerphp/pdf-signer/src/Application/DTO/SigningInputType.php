<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
enum SigningInputType : string
{
    case Data = 'data';
    case Digest = 'digest';
}
