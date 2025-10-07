<?php

namespace OCA\Libresign\3rdparty\Mpdf\Http;

use OCA\Libresign\3rdparty\Psr\Http\Message\RequestInterface;
/** @internal */
interface ClientInterface
{
    public function sendRequest(RequestInterface $request);
}
