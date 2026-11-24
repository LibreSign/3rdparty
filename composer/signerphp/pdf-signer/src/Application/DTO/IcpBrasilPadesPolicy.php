<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/**
 * ICP-Brasil PAdES signature policies in force (DOC-ICP-15.03), as published in the
 * official policy list https://politicas.icpbrasil.gov.br/LPA_PAdES.der.
 *
 * Digests are the SHA-256 of each policy document, as listed in the LPA. Only the
 * policies whose mandatory attributes this library produces are offered:
 *  - AD-RB v1.3: contentType, messageDigest, signingCertificateV2, sigPolicyId;
 *  - AD-RT v1.3: AD-RB plus the unsigned id-aa-signatureTimeStampToken.
 * AD-RC/AD-RA (v1.4) add further reference/archival rules and are not offered yet.
 * The June 2025 versions (AD-RB/AD-RT v1.2) were revoked on 2025-07-23.
 * @internal
 */
enum IcpBrasilPadesPolicy : string
{
    case AdRbV1_3 = '2.16.76.1.7.1.11.1.3';
    case AdRtV1_3 = '2.16.76.1.7.1.12.1.3';
    public function uri() : string
    {
        return match ($this) {
            self::AdRbV1_3 => 'http://politicas.icpbrasil.gov.br/PA_PAdES_AD_RB_v1_3.der',
            self::AdRtV1_3 => 'http://politicas.icpbrasil.gov.br/PA_PAdES_AD_RT_v1_3.der',
        };
    }
    public function sha256DigestHex() : string
    {
        return match ($this) {
            self::AdRbV1_3 => '23da544aef71f7a75dc85fa6e17a83875741e4baef41ec178258a5c86ace54dd',
            self::AdRtV1_3 => '92a972e7c292bb884e98e650773d9e9876994effb43eb36199b06bf2864a677c',
        };
    }
    public function requiresSignatureTimestamp() : bool
    {
        return $this === self::AdRtV1_3;
    }
    public function toIdentifier() : SignaturePolicyIdentifierDto
    {
        return SignaturePolicyIdentifierDto::fromHex($this->value, $this->sha256DigestHex(), HashAlgorithm::Sha256, $this->uri(), $this->requiresSignatureTimestamp());
    }
}
