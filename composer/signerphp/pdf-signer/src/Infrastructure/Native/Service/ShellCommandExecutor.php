<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\ProtectionProcessException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\CommandExecutorInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\ProcessRunnerInterface;
/** @internal */
final class ShellCommandExecutor implements CommandExecutorInterface
{
    public function __construct(private readonly ProcessRunnerInterface $processRunner = new ShellProcessRunner())
    {
    }
    public function run(string $command, string $errorMessage) : void
    {
        $result = $this->processRunner->run($command);
        if (!$result->succeeded()) {
            throw new ProtectionProcessException($errorMessage . ' ' . $result->outputAsString());
        }
    }
}
