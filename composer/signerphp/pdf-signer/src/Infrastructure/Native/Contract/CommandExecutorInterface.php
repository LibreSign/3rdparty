<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

/** @internal */
interface CommandExecutorInterface
{
    public function run(string $command, string $errorMessage) : void;
}
