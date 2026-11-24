<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

/** @internal */
interface SignatureRevocationEvidenceCollectorInterface
{
    /**
     * @param  array<int, string>  $certificateChainDer
     * @return array<int, array{ocsp:array<int,string>,crl:array<int,string>}>
     */
    public function collect(array $certificateChainDer) : array;
}
