<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject\HttpResponse;
/** @internal */
interface HttpClientInterface
{
    /**
     * @param  array<int, string>  $headers
     */
    public function request(string $method, string $url, array $headers = [], string $body = '', int $timeoutSeconds = 10, bool $followRedirects = \false) : HttpResponse;
}
