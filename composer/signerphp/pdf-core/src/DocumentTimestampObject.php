<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore;

/** @internal */
class DocumentTimestampObject extends SignatureObject
{
    public function __construct(int $oid)
    {
        parent::__construct($oid);
        // ISO 32000-2, 12.8.5: a document timestamp dictionary has /Type /DocTimeStamp.
        $this->value['Type'] = '/DocTimeStamp';
        $this->value['SubFilter'] = '/ETSI.RFC3161';
        // ETSI EN 319 142-1, 5.4.3: the authoritative time is in the TSA token.
        unset($this->value['M']);
    }
}
