<?php

namespace OCA\Libresign\3rdparty\Mpdf\Tag;

/** @internal */
class TocPageBreak extends FormFeed
{
    public function open($attr, &$ahtml, &$ihtml)
    {
        list($isbreak, $toc_id) = $this->tableOfContents->openTagTOCPAGEBREAK($attr);
        $this->toc_id = $toc_id;
        if ($isbreak) {
            return;
        }
        parent::open($attr, $ahtml, $ihtml);
    }
}
