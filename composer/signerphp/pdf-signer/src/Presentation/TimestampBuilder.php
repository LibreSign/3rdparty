<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Presentation;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampConnectionResultDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service\TimestampService;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
/** @internal */
final class TimestampBuilder
{
    private ?TimestampOptionsDto $options = null;
    private ?string $content = null;
    public function __construct(private readonly TimestampService $service)
    {
    }
    public static function new(?TimestampService $service = null) : self
    {
        return new self($service ?? new TimestampService());
    }
    public function withOptions(TimestampOptionsDto $options) : self
    {
        $this->options = $options;
        return $this;
    }
    public function withContent(string $content) : self
    {
        $this->content = $content;
        return $this;
    }
    public function requestTokenHex() : string
    {
        return $this->service->requestTokenHex($this->requireContent(), $this->requireOptions());
    }
    public function requestTokenBase64() : string
    {
        return $this->service->requestTokenBase64($this->requireContent(), $this->requireOptions());
    }
    public function testConnection(?string $probeContent = null) : TimestampConnectionResultDto
    {
        return $this->service->testConnection($this->requireOptions(), $probeContent);
    }
    private function requireOptions() : TimestampOptionsDto
    {
        if ($this->options === null) {
            throw new SignerException('Timestamp options are required. Use withOptions().');
        }
        return $this->options;
    }
    private function requireContent() : string
    {
        if ($this->content === null) {
            throw new SignerException('Timestamp content is required. Use withContent().');
        }
        return $this->content;
    }
}
