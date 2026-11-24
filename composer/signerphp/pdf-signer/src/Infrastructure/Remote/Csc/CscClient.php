<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\Csc;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\HttpClientInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject\HttpResponse;
/** @internal */
final readonly class CscClient
{
    private const SIGN_HASH_OPERATION = 'signatures/signHash';
    public function __construct(private HttpClientInterface $httpClient, private string $baseUrl, #[\SensitiveParameter] private ?string $accessToken = null, private int $timeoutSeconds = 20)
    {
        $parts = \parse_url($baseUrl);
        if (!\is_array($parts) || !\in_array($parts['scheme'] ?? '', ['http', 'https'], \true) || !isset($parts['host']) || isset($parts['user']) || isset($parts['pass']) || $parts['scheme'] === 'http' && !\in_array($parts['host'], ['localhost', '127.0.0.1', '::1'], \true)) {
            throw new \InvalidArgumentException('CSC endpoint must use HTTPS or loopback HTTP.');
        }
        if ($timeoutSeconds < 1) {
            throw new \InvalidArgumentException('CSC timeout must be positive.');
        }
    }
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function post(string $operation, #[\SensitiveParameter] array $payload = []) : array
    {
        if (!\in_array($operation, ['info', 'credentials/list', 'credentials/info', 'credentials/authorize', self::SIGN_HASH_OPERATION], \true)) {
            throw new \InvalidArgumentException('Unsupported CSC operation.');
        }
        $response = $this->sendRequest($operation, \json_encode($payload, \JSON_THROW_ON_ERROR));
        try {
            return $this->decodeResponse($response, $operation);
        } catch (SignerException $exception) {
            if ($operation !== self::SIGN_HASH_OPERATION) {
                throw $exception;
            }
            throw new CscSignHashOutcomeUnknownException('CSC signHash outcome is unknown; reconcile before resubmission.');
        }
    }
    private function sendRequest(string $operation, #[\SensitiveParameter] string $body) : HttpResponse
    {
        $url = \rtrim($this->baseUrl, '/') . '/csc/v1/' . $operation;
        $headers = $this->headers();
        try {
            return $this->httpClient->request('POST', $url, $headers, $body, $this->timeoutSeconds);
        } catch (\LogicException $exception) {
            throw $exception;
        } catch (\Exception) {
            if ($operation === self::SIGN_HASH_OPERATION) {
                throw new CscSignHashOutcomeUnknownException('CSC signHash outcome is unknown; reconcile before resubmission.');
            }
            throw new SignerException(\sprintf('CSC %s transport failed.', $operation));
        }
    }
    /** @return list<string> */
    private function headers() : array
    {
        $headers = ['Accept: application/json', 'Content-Type: application/json'];
        if ($this->accessToken !== null && $this->accessToken !== '') {
            $headers[] = 'Authorization: Bearer ' . $this->accessToken;
        }
        return $headers;
    }
    /** @return array<string, mixed> */
    private function decodeResponse(HttpResponse $response, string $operation) : array
    {
        if ($response->transportError !== null) {
            throw new SignerException(\sprintf('CSC %s transport failed.', $operation));
        }
        if (!$response->isSuccessful()) {
            throw new SignerException(\sprintf('CSC %s failed with HTTP %d.', $operation, $response->statusCode));
        }
        try {
            $data = \json_decode($response->body, \true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new SignerException(\sprintf('CSC %s returned invalid JSON.', $operation));
        }
        if (!\is_array($data) || \array_is_list($data)) {
            throw new SignerException(\sprintf('CSC %s returned an invalid response.', $operation));
        }
        return $data;
    }
    public function assertVersion() : void
    {
        if (($this->post('info')['specs'] ?? null) !== '1.0.4.0') {
            throw new SignerException('CSC service does not advertise supported version 1.0.4.0.');
        }
    }
}
