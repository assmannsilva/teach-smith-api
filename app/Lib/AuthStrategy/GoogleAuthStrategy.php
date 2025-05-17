<?php

namespace App\Lib\AuthStrategy;

use App\Enums\ProvidersEnum;
use App\Lib\AuthStrategy\Traits\HasState;
use App\Lib\LoginStrategy\Interfaces\ExternalProviderInterface;
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

    protected function configureGoogleCodeVerifier(): void
    {
        $code = $this->client->getOAuth2Service()->generateCodeVerifier();
        session()->put("google_code_verifier", $code);
    }


    /**
     * Gera a URL de autenticação do Google
     * @return string
     */
    public function generateOAuthUrl() : string
    {
        $this->client->addScope(Oauth2::USERINFO_EMAIL);
        $this->client->setPrompt('consent');
        $this->client->setState($this->generateState());
        $this->configureGoogleCodeVerifier();

        return $this->client->createAuthUrl();
    }

    /**
     * Busca as informações do usuário no Google
     * @param string $token
     * @return Userinfo
     */
    protected function getUserInfo(string $token): Userinfo
    {
        $this->client->setAccessToken($token);
        $oauth2 = new Google\Service\Oauth2($this->client);
        return $oauth2->userinfo->get();
    }

    /**
     * Gera o token de acesso do Google
     * @param string $code
     * @return string
     * @throws 
     */
    public function generateToken(string $code) : string
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code,session()->get("google_code_verifier"));
        session()->forget("google_code_verifier");

        if (!isset($token['access_token'])) {
            throw new \Exception('Erro ao obter token: ' . $token['error_description'] ?? 'Erro desconhecido');
        }

        return $token["access_token"];
    }

    /**
     * Realiza a autenticação do usuário
     * @param array $credentiais
     * @return bool
     */
    public function authenticate(array $credentiais): bool
    {
        $access_token = $this->generateToken($credentiais['code']);
        $google_user_data = $this->getUserInfo($access_token);
        $user = $this->userRepository->findByProviderCredentials(ProvidersEnum::GOOGLE, $google_user_data->id);
        
        if(!$user) return false;
        
        Auth::login($user, $credentiais['remember'] ?? false);

        return true;
    }

    /**
     * Completa o registro do usuário com o método de autenticação do Google
     * @param User $user
     * @param string $auth_credential (code)
     * @return User
     * @throws 
     */
    public function makeRegistration(User $user, string $auth_credential): User
    {
        $access_token = $this->generateToken($auth_credential);
        $google_user_data = $this->getUserInfo($access_token);
        
        $user->email = $google_user_data->email;
        $user->provider = ProvidersEnum::GOOGLE;
        $user->provider_id = $google_user_data->id;
        $user->active = true;

        if($this->userRepository->findByEmail($google_user_data->email)) {
            throw new \Exception('Usuário já cadastrado');
        }

        $this->userRepository->save($user);
        
        Auth::login($user);

        return $user;
    }
    
}