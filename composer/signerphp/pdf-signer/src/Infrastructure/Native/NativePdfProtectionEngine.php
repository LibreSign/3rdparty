<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\PdfProtectionEngineInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ProtectPdfRequestDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\ProtectionProcessException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\PdfProtectionApplierInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\QpdfPdfProtectionApplier;
/** @internal */
final readonly class NativePdfProtectionEngine implements PdfProtectionEngineInterface
{
    public function __construct(private PdfProtectionApplierInterface $protectionApplier = new QpdfPdfProtectionApplier())
    {
    }
    public function protect(ProtectPdfRequestDto $request) : string
    {
        try {
            return $this->protectionApplier->apply($request->pdf->content, $request->options);
        } catch (\Throwable $throwable) {
            throw new ProtectionProcessException(\sprintf('Could not apply PDF protection using native v1 engine. Root cause: %s', $throwable->getMessage()), previous: $throwable);
        }
    }
}
