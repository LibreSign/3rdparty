<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\IntegraIcp;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
/** @internal */
final readonly class IntegraIcpAuthorization
{
    public function __construct(private IntegraIcpClient $client)
    {
    }
    public function authorizationUrl(string $codeChallenge, string $callbackUri, ?string $cpf = null, int $credentialLifetimeSeconds = 900, int $clearanceLifetimeSeconds = 900) : string
    {
        return $this->client->url('authentications', $this->parameters($codeChallenge, $callbackUri, $cpf, $credentialLifetimeSeconds, $clearanceLifetimeSeconds) + ['autostart' => 'true']);
    }
    /** @return list<array{clearanceId: string, productName: string, providerName: string, clearanceEndpoint: string, clearanceType: string}> */
    public function clearances(string $codeChallenge, string $callbackUri, ?string $cpf = null, int $credentialLifetimeSeconds = 900, int $clearanceLifetimeSeconds = 900) : array
    {
        $data = $this->client->json('GET', 'authentications', query: $this->parameters($codeChallenge, $callbackUri, $cpf, $credentialLifetimeSeconds, $clearanceLifetimeSeconds));
        $items = $data['clearances'] ?? null;
        if (!\is_array($items) || !\array_is_list($items)) {
            throw new SignerException('IntegraICP returned an invalid clearance list.');
        }
        $clearances = [];
        foreach ($items as $item) {
            if (!\is_array($item)) {
                throw new SignerException('IntegraICP returned an invalid clearance.');
            }
            $clearance = [];
            foreach (['clearanceId', 'productName', 'providerName', 'clearanceEndpoint', 'clearanceType'] as $key) {
                if (!isset($item[$key]) || !\is_string($item[$key]) || \trim($item[$key]) === '') {
                    throw new SignerException('IntegraICP returned an incomplete clearance.');
                }
                $clearance[$key] = $item[$key];
            }
            if (!\str_starts_with($clearance['clearanceEndpoint'], 'https://')) {
                throw new SignerException('IntegraICP returned an insecure clearance endpoint.');
            }
            $clearances[] = $clearance;
        }
        return $clearances;
    }
    /** @return array<string, scalar> */
    private function parameters(string $challenge, string $callbackUri, ?string $cpf, int $credentialLifetime, int $clearanceLifetime) : array
    {
        if (!\preg_match('/^[A-Za-z0-9_-]{43}$/D', $challenge)) {
            throw new \InvalidArgumentException('IntegraICP requires an S256 PKCE challenge.');
        }
        if (\filter_var($callbackUri, \FILTER_VALIDATE_URL) === \false || \parse_url($callbackUri, \PHP_URL_SCHEME) !== 'https') {
            throw new \InvalidArgumentException('IntegraICP callback URI must use HTTPS.');
        }
        if ($credentialLifetime < 1 || $credentialLifetime > 604800 || $clearanceLifetime < 1 || $clearanceLifetime > 86400) {
            throw new \InvalidArgumentException('IntegraICP credential or clearance lifetime is out of range.');
        }
        $query = ['secret_data' => $challenge, 'secret_type' => 'code_challenge', 'callback_uri' => $callbackUri, 'credential_lifetime' => $credentialLifetime, 'clearance_lifetime' => $clearanceLifetime];
        if ($cpf !== null) {
            if (!\preg_match('/^[0-9]{11}$/D', $cpf)) {
                throw new \InvalidArgumentException('IntegraICP CPF must contain 11 digits.');
            }
            $query['subject_key'] = $cpf;
            $query['subject_type'] = 'CPF';
        }
        return $query;
    }
}
