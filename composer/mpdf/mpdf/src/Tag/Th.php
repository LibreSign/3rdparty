<?php

namespace OCA\Libresign\3rdparty\Mpdf\Tag;

/** @internal */
class Th extends Td
{
    public function close(&$ahtml, &$ihtml)
    {
        $this->mpdf->SetStyle('B', \false);
        parent::close($ahtml, $ihtml);
    }
}
