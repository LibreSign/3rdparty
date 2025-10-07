<?php

namespace OCA\Libresign\3rdparty\Mpdf\Language;

/** @internal */
interface ScriptToLanguageInterface
{
    public function getLanguageByScript($script);
    public function getLanguageDelimiters($language);
}
