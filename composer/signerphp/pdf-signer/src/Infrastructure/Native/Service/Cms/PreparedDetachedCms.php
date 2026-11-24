<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\Cms;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\HashAlgorithm;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureAlgorithm;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignaturePolicyIdentifierDto;
/** @internal */
final readonly class PreparedDetachedCms
{
    /** @var list<string> */
    public array $certificateChainPem;
    /** @param list<string> $certificateChainPem */
    public function __construct(public string $certificatePem, public string $signedAttributes, public HashAlgorithm $digestAlgorithm, public SignatureAlgorithm $signatureAlgorithm, array $certificateChainPem = [], public ?SignaturePolicyIdentifierDto $signaturePolicy = null)
    {
        $this->certificateChainPem = X509Certificate::normalizeChain($certificateChainPem);
    }
}
