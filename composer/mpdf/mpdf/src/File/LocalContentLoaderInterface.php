<?php

namespace OCA\Libresign\Vendor\Mpdf\File;

/** @internal */
interface LocalContentLoaderInterface
{
    /**
     * @return string|null
     */
    public function load($path);
}
