<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\Vidaas;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\HttpClientInterface;
/** @internal */
final readonly class VidaasAuthorization
{
    private const AUTHORIZE_PATH = '/v0/oauth/authorize';
    private const TOKEN_PATH = '/v0/oauth/token';
    private const PUSH_STATUS_PATH = '/valid/api/v1/trusted-services/authentications';
    private const PUSH_CODE_VERIFIER = 'challenge';
    private const PUSH_REDIRECT_URI = 'push://';
    public function __construct(private HttpClientInterface $httpClient, private string $clientId, #[\SensitiveParameter] private string $clientSecret, private string $baseUrl = VidaasClient::PRODUCTION_URL, private int $timeoutSeconds = 20)
    {
        if (\trim($this->clientId) === '' || \trim($this->clientSecret) === '') {
            throw new SignerException('VIDaaS authorization requires clientId and clientSecret.');
        }
    }
    public function authorizationUrl(string $codeChallenge, string $scope = 'signature_session', ?string $redirectUri = null, ?string $state = null, ?string $loginHint = null, int $lifetimeSeconds = 43200) : string
    {
        if (\trim($codeChallenge) === '') {
            throw new SignerException('VIDaaS authorization requires a codeChallenge.');
        }
        $query = ['response_type' => 'code', 'client_id' => $this->clientId, 'code_challenge' => $codeChallenge, 'code_challenge_method' => 'S256', 'scope' => $scope, 'lifetime' => $lifetimeSeconds];
        $this->addOptional($query, 'redirect_uri', $redirectUri);
        $this->addOptional($query, 'state', $state);
        $this->addOptional($query, 'login_hint', $loginHint);
        return $this->url(self::AUTHORIZE_PATH) . '?' . \http_build_query($query);
    }
    public function exchangeAuthorizationCode(string $code, string $codeVerifier, ?string $redirectUri = null) : VidaasAccessToken
    {
        if (\trim($code) === '' || \trim($codeVerifier) === '') {
            throw new SignerException('VIDaaS token exchange requires code and codeVerifier.');
        }
        $form = ['grant_type' => 'authorization_code', 'client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'code' => $code, 'code_verifier' => $codeVerifier];
        $this->addOptional($form, 'redirect_uri', $redirectUri);
        $response = $this->httpClient->request('POST', $this->url(self::TOKEN_PATH), ['Content-Type: application/x-www-form-urlencoded', 'Accept: application/json'], \http_build_query($form), $this->timeoutSeconds);
        $json = $this->successfulJson($response->statusCode, $response->body, 'token exchange');
        $token = $json['access_token'] ?? null;
        if (!\is_string($token) || \trim($token) === '') {
            throw new SignerException('VIDaaS token response does not contain access_token.');
        }
        return new VidaasAccessToken(\trim($token), \is_string($json['token_type'] ?? null) ? $json['token_type'] : 'Bearer', \is_numeric($json['expires_in'] ?? null) ? (int) $json['expires_in'] : 0, \is_string($json['scope'] ?? null) ? $json['scope'] : null);
    }
    public function startPush(string $loginHint, string $scope = 'signature_session', int $lifetimeSeconds = 120, ?string $state = null) : VidaasPushAuthorization
    {
        if (\trim($loginHint) === '') {
            throw new SignerException('VIDaaS push authorization requires a loginHint.');
        }
        if ($lifetimeSeconds < 60 || $lifetimeSeconds > 3600) {
            throw new SignerException('VIDaaS push lifetimeSeconds must be between 60 and 3600.');
        }
        $query = ['response_type' => 'code', 'client_id' => $this->clientId, 'code_challenge' => self::PUSH_CODE_VERIFIER, 'code_challenge_method' => 'plain', 'scope' => $scope, 'login_hint' => \trim($loginHint), 'redirect_uri' => self::PUSH_REDIRECT_URI, 'lifetime' => $lifetimeSeconds];
        $this->addOptional($query, 'state', $state);
        $url = $this->url(self::AUTHORIZE_PATH) . '?' . \http_build_query($query);
        $response = $this->httpClient->request('GET', $url, ['Accept: text/plain'], timeoutSeconds: $this->timeoutSeconds);
        if (!$response->isSuccessful()) {
            throw new SignerException(\sprintf('VIDaaS push authorization failed with HTTP %d.', $response->statusCode));
        }
        \parse_str(\trim($response->body), $values);
        $code = $values['code'] ?? null;
        if (!\is_string($code) || \trim($code) === '') {
            throw new SignerException('VIDaaS push authorization response does not contain code.');
        }
        return new VidaasPushAuthorization(\trim($code));
    }
    public function pollPush(string $code) : VidaasPushAuthentication
    {
        if (\trim($code) === '') {
            throw new SignerException('VIDaaS push polling requires a code.');
        }
        $response = $this->httpClient->request('POST', $this->url(self::PUSH_STATUS_PATH), ['Authorization: Bearer ' . \trim($code), 'Accept: application/json'], timeoutSeconds: $this->timeoutSeconds);
        $json = $this->successfulJson($response->statusCode, $response->body, 'push polling');
        $token = $json['authorizationToken'] ?? null;
        if (!\is_string($token) || \trim($token) === '') {
            return VidaasPushAuthentication::pending();
        }
        $redirectUrl = \is_string($json['redirectUrl'] ?? null) ? \trim($json['redirectUrl']) : null;
        return VidaasPushAuthentication::approved(\trim($token), $redirectUrl ?: null);
    }
    public function exchangePushAuthorizationToken(string $authorizationToken) : VidaasAccessToken
    {
        return $this->exchangeAuthorizationCode($authorizationToken, self::PUSH_CODE_VERIFIER, self::PUSH_REDIRECT_URI);
    }
    /** @param array<string, mixed> $values */
    private function addOptional(array &$values, string $key, ?string $value) : void
    {
        if ($value !== null && \trim($value) !== '') {
            $values[$key] = \trim($value);
        }
    }
    /** @return array<string, mixed> */
    private function successfulJson(int $statusCode, string $body, string $operation) : array
    {
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new SignerException(\sprintf('VIDaaS %s failed with HTTP %d.', $operation, $statusCode));
        }
        $json = \json_decode($body, \true);
        if (!\is_array($json)) {
            throw new SignerException(\sprintf('VIDaaS %s returned invalid JSON.', $operation));
        }
        return $json;
    }
    private function url(string $path) : string
    {
        return \rtrim($this->baseUrl, '/') . $path;
    }
}
