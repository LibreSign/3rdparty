<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject;

/** @internal */
final readonly class ProcessResult
{
    /**
     * @param  array<int, string>  $output
     */
    public function __construct(public int $exitCode, public array $output = [])
    {
    }
    public function succeeded() : bool
    {
        return $this->exitCode === 0;
    }
    public function outputAsString() : string
    {
        return \implode("\n", $this->output);
    }
}
