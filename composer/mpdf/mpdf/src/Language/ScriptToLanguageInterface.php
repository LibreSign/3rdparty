<?php

namespace OCA\Libresign\Vendor\Mpdf\Language;

/** @internal */
interface ScriptToLanguageInterface
{
    public function getLanguageByScript($script);
    public function getLanguageDelimiters($language);
}
