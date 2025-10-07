<?php

namespace OCA\Libresign\3rdparty\Mpdf\File;

/** @internal */
class LocalContentLoader implements \OCA\Libresign\3rdparty\Mpdf\File\LocalContentLoaderInterface
{
    public function load($path)
    {
        return \file_get_contents($path);
    }
}
