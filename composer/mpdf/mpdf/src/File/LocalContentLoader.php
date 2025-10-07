<?php

namespace OCA\Libresign\Vendor\Mpdf\File;

/** @internal */
class LocalContentLoader implements \OCA\Libresign\Vendor\Mpdf\File\LocalContentLoaderInterface
{
    public function load($path)
    {
        return \file_get_contents($path);
    }
}
