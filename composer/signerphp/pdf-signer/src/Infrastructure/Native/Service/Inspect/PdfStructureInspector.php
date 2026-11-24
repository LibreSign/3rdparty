<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\Inspect;

/** @internal */
final class PdfStructureInspector extends \OCA\Libresign\Vendor\SignerPHP\PdfCore\Service\Inspect\PdfStructureInspector
{
    /**
     * @return array{has_dss:bool,has_vri:bool,has_ocsps:bool,has_crls:bool,has_certs:bool,vri_entry_hint_count:int,ocsp_stream_hint_count:int,crl_stream_hint_count:int,cert_stream_hint_count:int}
     */
    public function analyzeLtvAssembly(string $pdfContent) : array
    {
        return ['has_dss' => \preg_match('/\\/DSS\\b/', $pdfContent) === 1, 'has_vri' => \preg_match('/\\/VRI\\b/', $pdfContent) === 1, 'has_ocsps' => \preg_match('/\\/OCSPs\\b/', $pdfContent) === 1, 'has_crls' => \preg_match('/\\/CRLs\\b/', $pdfContent) === 1, 'has_certs' => \preg_match('/\\/Certs\\b/', $pdfContent) === 1, 'vri_entry_hint_count' => (int) \preg_match_all('/\\/VRI\\b/', $pdfContent, $unused), 'ocsp_stream_hint_count' => (int) \preg_match_all('/\\/OCSPs\\b/', $pdfContent, $unused2), 'crl_stream_hint_count' => (int) \preg_match_all('/\\/CRLs\\b/', $pdfContent, $unused3), 'cert_stream_hint_count' => (int) \preg_match_all('/\\/Certs\\b/', $pdfContent, $unused4)];
    }
}
