<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result;

use OCA\Libresign\Vendor\Endroid\QrCode\Matrix\MatrixInterface;
/** @internal */
final class PdfResult extends AbstractResult
{
    public function __construct(MatrixInterface $matrix, private readonly \OCA\Libresign\Vendor\FPDF $fpdf)
    {
        parent::__construct($matrix);
    }
    public function getPdf() : \OCA\Libresign\Vendor\FPDF
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
