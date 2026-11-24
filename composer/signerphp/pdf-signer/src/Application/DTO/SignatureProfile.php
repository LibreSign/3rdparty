<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
enum SignatureProfile : string
{
    case PdfBasic = 'pdf-basic';
    case PadesBaselineB = 'pades-baseline-b';
    case PadesBaselineT = 'pades-baseline-t';
    case PadesBaselineLT = 'pades-baseline-lt';
    case PadesBaselineLTA = 'pades-baseline-lta';
}
