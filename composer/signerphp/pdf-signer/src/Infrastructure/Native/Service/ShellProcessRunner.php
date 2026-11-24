<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\ProcessRunnerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject\ProcessResult;
/** @internal */
final class ShellProcessRunner implements ProcessRunnerInterface
{
    public function run(string $command) : ProcessResult
    {
        $output = [];
        $exitCode = 0;
        \exec($command . ' 2>&1', $output, $exitCode);
        return new ProcessResult($exitCode, $output);
    }
}
