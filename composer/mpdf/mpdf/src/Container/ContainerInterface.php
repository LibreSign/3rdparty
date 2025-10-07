<?php

namespace OCA\Libresign\3rdparty\Mpdf\Container;

/** @internal */
interface ContainerInterface
{
    public function get($id);
    public function has($id);
}
