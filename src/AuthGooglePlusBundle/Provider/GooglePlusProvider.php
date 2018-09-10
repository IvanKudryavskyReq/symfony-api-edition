<?php

namespace AuthGooglePlusBundle\Provider;

use AuthBundle\OAuth\SocialProvider\AbstractGuzzleSocialProvider;
use AuthBundle\OAuth\SocialProvider\SocialData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GooglePlusProvider.
 */
class GooglePlusProvider extends AbstractGuzzleSocialProvider
{
    /**
     * {@inheritdoc}
     */
    public function getSocialData($token)
    {
        $data = $this->request(Request::METHOD_GET, '/plus/v1/people/me', [
            'query' => [
                'access_token' => $token,
            ],
        ]);

        return new SocialData(
            $this->getName(),
            $data['id'],
            $data['name']['givenName'],
            $data['name']['familyName'],
            isset($data['emails']) ? $data['emails'][0]['value'] : null,
            isset($data['image']) ? $data['image']['url'] : null
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseUrl()
    {
        return 'https://www.googleapis.com';
    }

    /**
     * {@inheritdoc}
     */
    protected function isInvalidTokenResponse($body)
    {
        return Response::HTTP_UNAUTHORIZED == $body['error']['code'] || Response::HTTP_FORBIDDEN == $body['error']['code'];
    }

    public function getName()
    {
        return 'googleplus';
    }
}
