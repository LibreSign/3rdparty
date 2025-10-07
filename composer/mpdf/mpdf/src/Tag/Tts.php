<?php

namespace OCA\Libresign\3rdparty\Mpdf\Tag;

/** @internal */
class Tts extends SubstituteTag
{
    public function open($attr, &$ahtml, &$ihtml)
    {
        $this->mpdf->tts = \true;
        $this->mpdf->InlineProperties['TTS'] = $this->mpdf->saveInlineProperties();
        $this->mpdf->setCSS(['FONT-FAMILY' => 'csymbol', 'FONT-WEIGHT' => 'normal', 'FONT-STYLE' => 'normal'], 'INLINE');
    }
}
