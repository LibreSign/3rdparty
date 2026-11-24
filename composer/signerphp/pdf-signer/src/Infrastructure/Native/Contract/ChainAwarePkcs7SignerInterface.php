<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignaturePolicyIdentifierDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureProfile;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
/**
 * CMS signer able to embed the signer's certificate chain in the SignedData certificates set,
 * so validators can build a path to an independently trusted anchor. The chain can be
 * combined with an explicit signature policy, as ICP-Brasil signatures need both.
 * @internal
 */
interface ChainAwarePkcs7SignerInterface extends PolicyAwarePkcs7SignerInterface
{
    /**
     * @param  list<string>  $certificateChainPem  intermediate (and optionally root) certificates
     */
    public function signWithChain(Buffer $signableDocument, SignatureProviderInterface $signatureProvider, string $certificatePem, SignatureProfile $profile, array $certificateChainPem, ?SignaturePolicyIdentifierDto $signaturePolicy = null, ?TimestampOptionsDto $signatureTimestamp = null) : string;
}
