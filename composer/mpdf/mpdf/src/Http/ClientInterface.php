<?php

namespace OCA\Libresign\Vendor\Mpdf\Http;

use OCA\Libresign\Vendor\Psr\Http\Message\RequestInterface;
/** @internal */
interface ClientInterface
{
    public function sendRequest(RequestInterface $request);
}
