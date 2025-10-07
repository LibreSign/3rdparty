<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Writer;

use OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
interface ValidatingWriterInterface
{
    public function validateResult(ResultInterface $result, string $expectedData) : void;
}
