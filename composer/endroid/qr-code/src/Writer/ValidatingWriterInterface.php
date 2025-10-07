<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer;

use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
interface ValidatingWriterInterface
{
    public function validateResult(ResultInterface $result, string $expectedData) : void;
}
