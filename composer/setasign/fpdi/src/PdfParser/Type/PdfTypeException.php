<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */
namespace OCA\Libresign\Vendor\setasign\Fpdi\PdfParser\Type;

use OCA\Libresign\Vendor\setasign\Fpdi\PdfParser\PdfParserException;
/**
 * Exception class for pdf type classes
 * @internal
 */
class PdfTypeException extends PdfParserException
{
    /**
     * @var int
     */
    const NO_NEWLINE_AFTER_STREAM_KEYWORD = 0x601;
}
