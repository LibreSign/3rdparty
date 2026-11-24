<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Presentation;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\PdfContentDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ProtectionOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ProtectPdfRequestDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service\PdfProtectionService;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
/** @internal */
final class PdfProtectionBuilder
{
    private ?PdfContentDto $content = null;
    private ?ProtectionOptionsDto $options = null;
    public function __construct(private readonly PdfProtectionService $service)
    {
    }
    public static function new(PdfProtectionService $service) : self
    {
        return new self($service);
    }
    public function withPdfContent(string $content) : self
    {
        $this->content = new PdfContentDto($content);
        return $this;
    }
    public function withProtection(ProtectionOptionsDto $options) : self
    {
        $this->options = $options;
        return $this;
    }
    public function protect() : string
    {
        if ($this->content === null) {
            throw new SignerException('PDF content is required. Use withPdfContent().');
        }
        if ($this->options === null) {
            throw new SignerException('Protection options are required. Use withProtection().');
        }
        return $this->service->protect(new ProtectPdfRequestDto($this->content, $this->options));
    }
}
