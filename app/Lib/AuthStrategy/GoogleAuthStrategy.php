<?php

namespace App\Lib\AuthStrategy;

use App\Enums\ProvidersActionsEnum;
use App\Enums\ProvidersEnum;
use App\Exceptions\InvalidStateRequestException;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\UserAlreadyRegisteredException;
use App\Lib\AuthStrategy\Traits\HasState;
use App\Lib\AuthStrategy\Interfaces\ExternalProviderInterface;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Google\Client;
use Google\Service\Oauth2;
use Google\Service\Oauth2\Userinfo;
use Illuminate\Support\Facades\Auth;

class GoogleAuthStrategy implements ExternalProviderInterface {

    use HasState;
    
    public function __construct(
        protected Client $client,
        protected UserRepositoryInterface $userRepository
    ) { }

    /**
     * Set the google code verifier in the session.
     * @return void
     */
    protected function configureGoogleCodeVerifier(): void
    {
        $code = $this->client->getOAuth2Service()->generateCodeVerifier();
        session()->put("google_code_verifier", $code);
    }

    /**
     * Set the Google redirect URI.
     * @param ProvidersActionsEnum $action
     * @return void
     */
    protected function setGoogleRedirectUri(ProvidersActionsEnum $action): void
    {
        $this->client->setRedirectUri(config('services.google.redirect_uri').$action->value);
    }

    /**
     * Generates an OAuth URL for the Google authentication.
     * @param ProvidersActionsEnum $action
     * @param array $state_extra_data (Use this for a data that must be used on the callback)
     * @return string
     */
    public function generateOAuthUrl(ProvidersActionsEnum $action, array $state_extra_data = []) : string
    {
        $this->setGoogleRedirectUri($action);
        $this->client->addScope(Oauth2::USERINFO_EMAIL);
        $this->client->addScope(Oauth2::USERINFO_PROFILE);
        $this->client->setPrompt('consent');
        $this->client->setState($this->generateState($state_extra_data));
        $this->configureGoogleCodeVerifier();

        return $this->client->createAuthUrl();
    }

    /**
     * Searches for user information using the access token.
     * @param string $token
     * @return Userinfo
     */
    protected function getUserInfo(string $token): Userinfo
    {
        $this->client->setAccessToken($token);
        $oauth2 = new Oauth2($this->client);
        return $oauth2->userinfo->get();
    }

    /**
     * Generates the Google access token.
     * @param string $code
     * @return string
     * @throws InvalidTokenException
     */
    public function generateToken(string $code) : string
    {   
        $token = $this->client->fetchAccessTokenWithAuthCode($code,session()->get("google_code_verifier"));
        session()->forget("google_code_verifier");

        if (!isset($token['access_token'])) {
            throw new InvalidTokenException($token['error_description'] ?? 'Erro desconhecido');
        }

        return $token["access_token"];
    }

    /**
     * Authenticates the user.
     * @param array $credentials (code, state, remember)
     * @return bool
     * @throws InvalidStateRequestException
     */
    public function authenticate(array $credentials): bool
    {
        if(!$this->checkState($credentials["state"])) throw new InvalidStateRequestException;
        
        $this->setGoogleRedirectUri(ProvidersActionsEnum::LOGIN);
        $access_token = $this->generateToken($credentials['code']);
        $google_user_data = $this->getUserInfo($access_token);
        $user = $this->userRepository->findByProviderCredentials(ProvidersEnum::GOOGLE, $google_user_data->id);
        
        Auth::login($user, $credentials['remember'] ?? false);

        return true;
    }

    /**
     * Completes the user data with information from Google.
     * @param User $user
     * @param Userinfo $user_info_from_google
     * @return User
     */
    private function completeUserData(User $user, Userinfo $user_info_from_google)
    {
        $user->email = $user_info_from_google->email;
        $user->provider = ProvidersEnum::GOOGLE;
        $user->provider_id = $user_info_from_google->id;
        $user->active = true;

        if($user->exists) return $user;

        $user->first_name = $user_info_from_google->givenName;
        $user->surname = $user_info_from_google->familyName;

        return $user;
    }

    /**
     * Registers the user with the Google authentication method.
     * @param User $user
     * @param string $auth_credential (code)
     * @param string $state (extra data and CSRF protection)
     * @return User
     * @throws UserAlreadyRegisteredException
     * @throws InvalidStateRequestException
     */
    public function makeRegistration(User $user, string $auth_credential, ?string $state = \null): User
    {
        if(!$this->checkState($state)) throw new InvalidStateRequestException;

        $this->setGoogleRedirectUri(ProvidersActionsEnum::REGISTER);
        $access_token = $this->generateToken($auth_credential);
        $google_user_data = $this->getUserInfo($access_token);
        
        $user = $this->completeUserData($user,$google_user_data);

        if($user_finded = $this->userRepository->findByEmail($google_user_data->email)) {
            throw new UserAlreadyRegisteredException($user_finded);
        }

        $this->userRepository->save($user);
        
        Auth::login($user);

        return $user;
    }
    
}