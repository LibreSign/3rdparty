<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class TimestampConnectionResultDto
{
    public function __construct(public bool $success, public ?string $message = null, public ?int $statusCode = null)
    {
    }
}
