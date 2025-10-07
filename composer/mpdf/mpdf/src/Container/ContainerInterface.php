<?php

namespace OCA\Libresign\Vendor\Mpdf\Container;

/** @internal */
interface ContainerInterface
{
    public function get($id);
    public function has($id);
}
