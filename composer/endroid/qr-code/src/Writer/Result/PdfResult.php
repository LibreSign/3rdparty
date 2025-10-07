<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result;

use OCA\Libresign\3rdparty\Endroid\QrCode\Matrix\MatrixInterface;
/** @internal */
final class PdfResult extends AbstractResult
{
    public function __construct(MatrixInterface $matrix, private readonly \OCA\Libresign\3rdparty\FPDF $fpdf)
    {
        parent::__construct($matrix);
    }
    public function getPdf() : \OCA\Libresign\3rdparty\FPDF
    {
        return $this->fpdf;
    }
    public function getString() : string
    {
        return $this->fpdf->Output('S');
    }
    public function getMimeType() : string
    {
        return 'application/pdf';
    }
}
