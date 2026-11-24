<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\Vidaas;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\HttpClientInterface;
/** @internal */
final readonly class VidaasClient
{
    public const PRODUCTION_URL = 'https://certificado.vidaas.com.br';
    public const SANDBOX_URL = 'https://hml-certificado.vidaas.com.br';
    public function __construct(#[\SensitiveParameter] private string $accessToken, private HttpClientInterface $httpClient, private string $baseUrl = self::PRODUCTION_URL, private int $timeoutSeconds = 20)
    {
        if (\trim($this->accessToken) === '') {
            throw new SignerException('VIDaaS access token cannot be empty.');
        }
    }
    /** @param array<string, mixed> $payload */
    public function authorizedJson(string $method, string $path, array $payload = []) : array
    {
        $body = $payload === [] ? '' : \json_encode($payload, \JSON_THROW_ON_ERROR);
        $headers = ['Authorization: Bearer ' . $this->accessToken, 'Accept: application/json'];
        if ($body !== '') {
            $headers[] = 'Content-Type: application/json';
        }
        $response = $this->httpClient->request($method, \rtrim($this->baseUrl, '/') . $path, $headers, $body, $this->timeoutSeconds);
        if (!$response->isSuccessful()) {
            throw new SignerException(\sprintf('VIDaaS %s failed with HTTP %d.', $path, $response->statusCode));
        }
        $json = \json_decode($response->body, \true);
        if (!\is_array($json)) {
            throw new SignerException(\sprintf('VIDaaS %s returned invalid JSON.', $path));
        }
        return $json;
    }
}
