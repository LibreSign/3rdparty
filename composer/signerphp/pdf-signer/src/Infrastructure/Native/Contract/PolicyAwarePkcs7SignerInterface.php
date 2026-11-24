<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignaturePolicyIdentifierDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureProfile;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
/**
 * CMS signer able to declare an explicit signature policy (id-aa-ets-sigPolicyId) and, when the
 * policy requires it, to add a signature timestamp (id-aa-signatureTimeStampToken).
 * @internal
 */
interface PolicyAwarePkcs7SignerInterface extends ProfileAwarePkcs7SignerInterface
{
    public function signWithPolicy(Buffer $signableDocument, SignatureProviderInterface $signatureProvider, string $certificatePem, SignatureProfile $profile, SignaturePolicyIdentifierDto $signaturePolicy, ?TimestampOptionsDto $signatureTimestamp = null) : string;
}
