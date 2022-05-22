<?php

namespace Orlissenberg\SocialiteProviders\Canvas;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public static function additionalConfigKeys()
    {
        return ['install_url'];
    }

    protected function getAuthUrl($state)
    {
        $url = $this->getConfig('install_url') . '/login/oauth2/auth';

        return $this->buildAuthUrlFromBase($url, $state);
    }

    protected function getTokenUrl()
    {
        return $this->getConfig('install_url') . '/login/oauth2/token';
    }

    protected function getUserByToken($token)
    {
        $id = $this->credentialsResponseBody['user']['id'] ?? null;

        if (is_null($id)) {
            return new \InvalidArgumentException('Missing user id');
        }

        $response = $this->getHttpClient()->get(
            $this->getConfig('install_url') . '/api/v1/accounts/' . $id,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]
        );

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())
            ->setRaw($user)
            ->map([
                'id' => $user['id'] ?? null,
                'nickname' => null,
                'name' => $this->credentialsResponseBody['user']['name'] ?? null,
                'email' => null,
                'avatar' => null,
            ]);
    }
}
