<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject\ProcessResult;
/** @internal */
interface ProcessRunnerInterface
{
    public function run(string $command) : ProcessResult;
}
