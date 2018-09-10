<?php

namespace AuthBundle\OAuth;

use AuthBundle\OAuth\SocialProvider\Exception\InvalidAccessTokenException;
use AuthBundle\OAuth\SocialProvider\SocialProviderInterface;
use FOS\OAuthServerBundle\Storage\GrantExtensionInterface;
use OAuth2\Model\IOAuth2Client;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class SocialGrantExtension.
 */
class SocialGrantExtension implements GrantExtensionInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var SocialProviderInterface[]
     */
    protected $providers;

    /**
     * SocialGrantExtension constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param SocialProviderInterface $provider
     */
    public function addProvider(SocialProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
    }

    /**
     * @param IOAuth2Client $client
     * @param array         $inputData
     * @param array         $authHeaders
     *
     * @return bool
     *
     * @throws OAuth2ServerException
     * @throws \TypeError
     */
    public function checkGrantExtension(IOAuth2Client $client, array $inputData, array $authHeaders)
    {
        if (!isset($this->providers[$inputData['network']])) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, 'unsupported_network');
        }

        try {
            $socialData = $this->providers[$inputData['network']]->getSocialData($inputData['token']);
        } catch (InvalidAccessTokenException $exception) {
            return false;
        }

        $this->eventDispatcher->dispatch('oauth2.social_login.complete', new GenericEvent($socialData));

        return true;
    }
}
