<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
enum SignatureAlgorithm : string
{
    case RsaPkcs1V15 = 'rsa-pkcs1-v1_5';
    case Ecdsa = 'ecdsa';
}
