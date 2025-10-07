<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result;

use OCA\Libresign\Vendor\Endroid\QrCode\Matrix\MatrixInterface;
/** @internal */
final class EpsResult extends AbstractResult
{
    public function __construct(
        MatrixInterface $matrix,
        /** @var array<string> $lines */
        private readonly array $lines
    )
    {
        parent::__construct($matrix);
    }
    public function getString() : string
    {
        return \implode("\n", $this->lines);
    }
    public function getMimeType() : string
    {
        return 'image/eps';
    }
}
