<?php

namespace OCA\Libresign\3rdparty\Mpdf\Barcode;

/** @internal */
interface BarcodeInterface
{
    /**
     * @return string
     */
    public function getType();
    /**
     * @return mixed[]
     */
    public function getData();
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getKey($key);
    /**
     * @return string
     */
    public function getChecksum();
}
