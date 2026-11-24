<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
enum SignatureEncoding : string
{
    case RsaPkcs1 = 'rsa-pkcs1';
    case EcdsaDer = 'ecdsa-der';
}
