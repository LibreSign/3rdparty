<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\BirdId;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\HttpClientInterface;
/** @internal */
final readonly class BirdIdClient
{
    public const PRODUCTION_URL = 'https://api.birdid.com.br';
    public const VAULT_ID_URL = 'https://apicloudid.vaultid.com.br';
    public function __construct(#[\SensitiveParameter] private string $accessToken, private HttpClientInterface $httpClient, private string $baseUrl = self::PRODUCTION_URL, private int $timeoutSeconds = 20)
    {
        if (\trim($this->accessToken) === '') {
            throw new SignerException('BirdID access token cannot be empty.');
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
            throw new SignerException(\sprintf('BirdID %s failed with HTTP %d.', $path, $response->statusCode));
        }
        $json = \json_decode($response->body, \true);
        if (!\is_array($json)) {
            throw new SignerException(\sprintf('BirdID %s returned invalid JSON.', $path));
        }
        return $json;
    }
}
