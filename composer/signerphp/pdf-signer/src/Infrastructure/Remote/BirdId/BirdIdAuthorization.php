<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\BirdId;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\HttpClientInterface;
/** @internal */
final readonly class BirdIdAuthorization
{
    private const AUTHORIZE_PATH = '/v0/oauth/authorize';
    private const TOKEN_PATH = '/v0/oauth/token';
    private const PASSWORD_PATH = '/v0/oauth/pwd_authorize';
    private const REVOKE_PATH = '/v0/oauth/revoke';
    private const USER_DISCOVERY_PATH = '/v0/oauth/user-discovery';
    /** Health integrations commonly keep a 13-hour signature session. */
    public const DEFAULT_SIGNATURE_LIFETIME_SECONDS = 46800;
    public function __construct(private HttpClientInterface $httpClient, private string $clientId, #[\SensitiveParameter] private string $clientSecret, private string $baseUrl = BirdIdClient::PRODUCTION_URL, private int $timeoutSeconds = 20)
    {
        if (\trim($this->clientId) === '' || \trim($this->clientSecret) === '') {
            throw new SignerException('BirdID authorization requires clientId and clientSecret.');
        }
    }
    public function authorizationUrl(string $codeChallenge, string $scope = 'signature_session', ?string $redirectUri = null, ?string $state = null, ?string $loginHint = null, int $lifetimeSeconds = self::DEFAULT_SIGNATURE_LIFETIME_SECONDS) : string
    {
        if (\trim($codeChallenge) === '') {
            throw new SignerException('BirdID authorization requires a codeChallenge.');
        }
        $query = ['response_type' => 'code', 'client_id' => $this->clientId, 'code_challenge' => $codeChallenge, 'code_challenge_method' => 'S256', 'scope' => $scope, 'lifetime' => $lifetimeSeconds];
        $this->addOptional($query, 'redirect_uri', $redirectUri);
        $this->addOptional($query, 'state', $state);
        $this->addOptional($query, 'login_hint', $this->normalizeIdentification($loginHint));
        return $this->url(self::AUTHORIZE_PATH) . '?' . \http_build_query($query);
    }
    public function exchangeAuthorizationCode(string $code, string $codeVerifier, ?string $redirectUri = null, int $lifetimeSeconds = self::DEFAULT_SIGNATURE_LIFETIME_SECONDS) : BirdIdAccessToken
    {
        if (\trim($code) === '' || \trim($codeVerifier) === '') {
            throw new SignerException('BirdID token exchange requires code and codeVerifier.');
        }
        $payload = ['grant_type' => 'authorization_code', 'client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'code' => \trim($code), 'code_verifier' => $codeVerifier, 'lifetime' => $lifetimeSeconds];
        $this->addOptional($payload, 'redirect_uri', $redirectUri);
        return $this->tokenFromJson($this->postJson(self::TOKEN_PATH, $payload), 'token exchange');
    }
    public function authorizeWithPassword(string $username, #[\SensitiveParameter] string $otp, string $scope = 'signature_session', int $lifetimeSeconds = self::DEFAULT_SIGNATURE_LIFETIME_SECONDS) : BirdIdAccessToken
    {
        $identification = $this->normalizeIdentification($username);
        if ($identification === null || \trim($otp) === '') {
            throw new SignerException('BirdID password authorization requires username and OTP.');
        }
        return $this->tokenFromJson($this->postJson(self::PASSWORD_PATH, ['grant_type' => 'password', 'client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'username' => $identification, 'password' => \trim($otp), 'scope' => $scope, 'lifetime' => $lifetimeSeconds]), 'password authorization');
    }
    public function revoke(#[\SensitiveParameter] string $accessToken) : bool
    {
        if (\trim($accessToken) === '') {
            throw new SignerException('BirdID revoke requires an access token.');
        }
        $json = $this->postJson(self::REVOKE_PATH, ['token' => \trim($accessToken)]);
        return ($json['revoked'] ?? \false) === \true;
    }
    public function discoverUser(string $cpfOrCnpj) : BirdIdUserDiscoveryResult
    {
        $identification = $this->normalizeIdentification($cpfOrCnpj);
        if ($identification === null) {
            throw new SignerException('BirdID user discovery requires a CPF or CNPJ.');
        }
        $type = \strlen($identification) === 14 ? 'CNPJ' : 'CPF';
        $json = $this->postJson(self::USER_DISCOVERY_PATH, ['client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'user_cpf_cnpj' => $type, 'val_cpf_cnpj' => $identification]);
        $slots = [];
        $rawSlots = $json['slots'] ?? [];
        if (\is_array($rawSlots)) {
            foreach ($rawSlots as $slot) {
                if (!\is_array($slot)) {
                    continue;
                }
                $alias = $slot['slot_alias'] ?? null;
                $label = $slot['label'] ?? '';
                if (!\is_string($alias) || \trim($alias) === '') {
                    continue;
                }
                $slots[] = new BirdIdSlot(\trim($alias), \is_string($label) ? \trim($label) : '');
            }
        }
        $status = $json['status'] ?? null;
        $hasCertificate = \is_string($status) && \strtoupper($status) === 'S' || $slots !== [];
        return new BirdIdUserDiscoveryResult($hasCertificate, $slots);
    }
    /** @param array<string, mixed> $payload */
    private function postJson(string $path, array $payload) : array
    {
        $response = $this->httpClient->request('POST', $this->url($path), ['Content-Type: application/json', 'Accept: application/json'], \json_encode($payload, \JSON_THROW_ON_ERROR), $this->timeoutSeconds);
        return $this->successfulJson($response->statusCode, $response->body, $path);
    }
    /** @param array<string, mixed> $json */
    private function tokenFromJson(array $json, string $operation) : BirdIdAccessToken
    {
        $token = $json['access_token'] ?? null;
        if (!\is_string($token) || \trim($token) === '') {
            throw new SignerException(\sprintf('BirdID %s response does not contain access_token.', $operation));
        }
        return new BirdIdAccessToken(\trim($token), \is_string($json['token_type'] ?? null) ? $json['token_type'] : 'Bearer', \is_numeric($json['expires_in'] ?? null) ? (int) $json['expires_in'] : 0, \is_string($json['scope'] ?? null) ? $json['scope'] : null, \is_string($json['authorized_identification_type'] ?? null) ? $json['authorized_identification_type'] : null, \is_string($json['authorized_identification'] ?? null) ? $json['authorized_identification'] : null);
    }
    /** @param array<string, mixed> $values */
    private function addOptional(array &$values, string $key, ?string $value) : void
    {
        if ($value !== null && \trim($value) !== '') {
            $values[$key] = \trim($value);
        }
    }
    private function normalizeIdentification(?string $value) : ?string
    {
        if ($value === null) {
            return null;
        }
        $digits = \preg_replace('/\\D+/', '', $value) ?? '';
        if ($digits === '') {
            return null;
        }
        if (\strlen($digits) <= 11) {
            return \str_pad($digits, 11, '0', \STR_PAD_LEFT);
        }
        return \str_pad($digits, 14, '0', \STR_PAD_LEFT);
    }
    /** @return array<string, mixed> */
    private function successfulJson(int $statusCode, string $body, string $operation) : array
    {
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new SignerException(\sprintf('BirdID %s failed with HTTP %d.', $operation, $statusCode));
        }
        $json = \json_decode($body, \true);
        if (!\is_array($json)) {
            throw new SignerException(\sprintf('BirdID %s returned invalid JSON.', $operation));
        }
        return $json;
    }
    private function url(string $path) : string
    {
        return \rtrim($this->baseUrl, '/') . $path;
    }
}
