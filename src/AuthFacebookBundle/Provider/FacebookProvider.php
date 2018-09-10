<?php

namespace AuthFacebookBundle\Provider;

use AuthBundle\OAuth\SocialProvider\AbstractGuzzleSocialProvider;
use AuthBundle\OAuth\SocialProvider\SocialData;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FacebookProvider.
 */
class FacebookProvider extends AbstractGuzzleSocialProvider
{
    /**
     * {@inheritdoc}
     */
    public function getSocialData($token)
    {
        $data = $this->request(Request::METHOD_GET, '/me', [
            'query' => [
                'fields' => 'email,first_name,last_name,id',
                'access_token' => $token,
            ],
        ]);

        return new SocialData(
            $this->getName(),
            $data['id'],
            $data['first_name'],
            $data['last_name'],
            isset($data['email']) ? $data['email'] : null
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseUrl()
    {
        return 'https://graph.facebook.com';
    }

    /**
     * {@inheritdoc}
     */
    protected function isInvalidTokenResponse($body)
    {
        return isset($body['error']['code']) && 190 == $body['error']['code'];
    }

    public function getName()
    {
        return 'facebook';
    }
}
