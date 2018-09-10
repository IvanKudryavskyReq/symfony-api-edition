<?php

namespace Tests\AuthBundle\OAuth;

use AppBundle\DataFixtures\ORM\LoadClientData;
use AppBundle\DataFixtures\Test\LoadAnonymousTokenData;
use AuthBundle\OAuth\SocialProvider\AbstractGuzzleSocialProvider;
use AuthBundle\OAuth\SocialProvider\Exception\InvalidAccessTokenException;
use AuthBundle\OAuth\SocialProvider\SocialData;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SocialGrantExtensionActionTest
 */
class SocialGrantExtensionActionTest extends WebTestCase
{
    protected $url = '/oauth/token';

    protected $headers = [
        'Accept' => 'application/json',
        'HTTP_Authorization' => 'Bearer AccessToken_For_Client',
    ];

    /**
     * Test grant type extension.
     *
     * @throws OAuth2ServerException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \TypeError
     */
    public function testSocialLoginAction()
    {
        $this->loadFixtures(
            [
                LoadClientData::class,
                LoadAnonymousTokenData::class,
            ]
        );

        $provider = $this->createMock(AbstractGuzzleSocialProvider::class);

        $provider->method("getName")->willReturn('testprovider');
        $provider->expects(static::exactly(2))->method("getSocialData")
            ->will(static::onConsecutiveCalls(
                new SocialData(
                    $this->getName(),
                    '5448755',
                    'FirstNameUser',
                    'LastNameUser',
                    'test@test.ua',
                    null
                ),
                $this->throwException(new InvalidAccessTokenException())
            ));

        $data = [
            'grant_type' => 'urn:requestum:social_grant_type',
            'client_id' => '20aa5tpwg04ks4w84o8cookswwccgkwko40gwcs0ws840wkssk',
            'client_secret' => '79fqd7qzbp8g8o8oggss48w4kwck4s4kccwwk8804ksowg8o',
            'network' => $provider->getName(),
            'token' => 'EAAeu87YxZCCEBACJaNn4PPmf6'
        ];


        $client = $this->getClient($provider);
        $client->request(Request::METHOD_POST, $this->url, $data, [], $this->headers);

        static::assertEquals(200, $client->getResponse()->getStatusCode());


        // Invalid token
        $data['token'] = 'EAAeu87YxZCCEBACJaNn';
        $client = $this->getClient($provider);
        $client->request(Request::METHOD_POST, $this->url, $data, [], $this->headers);
        $response = $client->getResponse();

        static::assertEquals(400, $response->getStatusCode());
        static::assertContains('invalid_grant', $response->getContent());


        // Unsupported_network
        $data['network'] = 'undefined_social_network';
        $client = $this->getClient($provider);
        $client->request(Request::METHOD_POST, $this->url, $data, [], $this->headers);

        $response = $client->getResponse();

        static::assertEquals(400, $response->getStatusCode());
        static::assertContains('unsupported_network', $response->getContent());
    }

    protected function getClient($provider)
    {
        $client = $this->makeClient();
        $client->getContainer()->get('auth.grant_extension.social')->addProvider($provider);
        return $client;
    }
}
