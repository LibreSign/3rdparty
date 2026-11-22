<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore;

/** @internal */
class DocumentTimestampObject extends SignatureObject
{
    public function __construct(int $oid)
    {
        parent::__construct($oid);
        $this->value['SubFilter'] = '/ETSI.RFC3161';
    }
}
