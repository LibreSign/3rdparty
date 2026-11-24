<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\IntegraIcp;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\HttpClientInterface;
/** @internal */
final readonly class IntegraIcpClient
{
    public const PRODUCTION_URL = 'https://services.integraicp.com.br';
    public function __construct(#[\SensitiveParameter] private string $channelId, private HttpClientInterface $httpClient, private string $baseUrl = self::PRODUCTION_URL, private int $timeoutSeconds = 20)
    {
        if (\trim($channelId) === '' || $timeoutSeconds < 1) {
            throw new \InvalidArgumentException('IntegraICP requires a channel and a positive timeout.');
        }
        $parts = \parse_url($baseUrl);
        if (!\is_array($parts) || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) {
            throw new \InvalidArgumentException('IntegraICP base URL must be an HTTPS URL without credentials, query or fragment.');
        }
    }
    /** @param array<string, scalar> $query */
    public function url(string $path, array $query = []) : string
    {
        $url = \rtrim($this->baseUrl, '/') . '/c/' . \rawurlencode($this->channelId) . '/icp/v3/' . $path;
        return $query === [] ? $url : $url . '?' . \http_build_query($query, '', '&', \PHP_QUERY_RFC3986);
    }
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, scalar>  $query
     * @return array<string, mixed>
     */
    public function json(string $method, string $path, #[\SensitiveParameter] array $payload = [], #[\SensitiveParameter] array $query = []) : array
    {
        $body = $payload === [] ? '' : \json_encode($payload, \JSON_THROW_ON_ERROR);
        $headers = ['Accept: application/json'];
        if ($body !== '') {
            $headers[] = 'Content-Type: application/json';
        }
        $response = $this->httpClient->request($method, $this->url($path, $query), $headers, $body, $this->timeoutSeconds);
        if (!$response->isSuccessful()) {
            throw new SignerException(\sprintf('IntegraICP request failed with HTTP %d.', $response->statusCode));
        }
        $json = \json_decode($response->body, \true);
        $data = \is_array($json) ? $json['data'] ?? null : null;
        if (!\is_array($data) || isset($json['error'])) {
            throw new SignerException('IntegraICP returned an invalid response envelope.');
        }
        return $data;
    }
}
