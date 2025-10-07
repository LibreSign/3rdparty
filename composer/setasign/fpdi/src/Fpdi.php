<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */
namespace OCA\Libresign\3rdparty\setasign\Fpdi;

use OCA\Libresign\3rdparty\setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use OCA\Libresign\3rdparty\setasign\Fpdi\PdfParser\PdfParserException;
use OCA\Libresign\3rdparty\setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use OCA\Libresign\3rdparty\setasign\Fpdi\PdfParser\Type\PdfNull;
/**
 * Class Fpdi
 *
 * This class let you import pages of existing PDF documents into a reusable structure for FPDF.
 * @internal
 */
class Fpdi extends FpdfTpl
{
    use FpdiTrait;
    use FpdfTrait;
    /**
     * FPDI version
     *
     * @string
     */
    const VERSION = '2.6.4';
}
