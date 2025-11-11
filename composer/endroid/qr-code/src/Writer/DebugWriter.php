<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer;

use OCA\Libresign\Vendor\Endroid\QrCode\Bacon\MatrixFactory;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\LabelInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Logo\LogoInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\QrCodeInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\DebugResult;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
final readonly class DebugWriter implements WriterInterface, ValidatingWriterInterface
{
    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []) : ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);
        return new DebugResult($matrix, $qrCode, $logo, $label, $options);
    }
    public function validateResult(ResultInterface $result, string $expectedData) : void
    {
        if (!$result instanceof DebugResult) {
            throw new \Exception('Unable to write logo: instance of DebugResult expected');
        }
        $result->setValidateResult(\true);
    }
}
