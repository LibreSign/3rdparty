<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */
namespace OCA\Libresign\Vendor\setasign\Fpdi\PdfParser\Filter;

use OCA\Libresign\Vendor\setasign\Fpdi\PdfParser\PdfParserException;
/**
 * Exception for filters
 * @internal
 */
class FilterException extends PdfParserException
{
    const UNSUPPORTED_FILTER = 0x201;
    const NOT_IMPLEMENTED = 0x202;
}
