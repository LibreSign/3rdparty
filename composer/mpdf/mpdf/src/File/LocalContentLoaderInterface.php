<?php

namespace OCA\Libresign\3rdparty\Mpdf\File;

/** @internal */
interface LocalContentLoaderInterface
{
    /**
     * @return string|null
     */
    public function load($path);
}
