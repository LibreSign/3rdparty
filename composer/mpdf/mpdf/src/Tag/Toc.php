<?php

namespace OCA\Libresign\3rdparty\Mpdf\Tag;

/** @internal */
class Toc extends Tag
{
    public function open($attr, &$ahtml, &$ihtml)
    {
        //added custom-tag - set Marker for insertion later of ToC
        $this->tableOfContents->openTagTOC($attr);
    }
    public function close(&$ahtml, &$ihtml)
    {
    }
}
