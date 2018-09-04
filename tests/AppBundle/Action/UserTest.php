<?php

namespace Tests\AppBundle\Action\User;

use AppBundle\DataFixtures\Demo\LoadAccessTokenData;
use AppBundle\DataFixtures\Demo\LoadUserData;
use AppBundle\DataFixtures\Test\LoadAnonymousTokenData;
use AppBundle\Entity\User;
use Requestum\ApiBundle\Tests\RestCrudTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\HttpFoundation\Request;

class UserTest extends RestCrudTestCase
{
    protected $url = '/api/users';
    protected $headers = [
        'Accept' => 'application/json',
        'HTTP_Authorization' => 'Bearer AccessToken_For_Client',
    ];

    protected function getEntityName()
    {
        return User::class;
    }

    public function testRegistration()
    {
        $this->loadFixtures([
            LoadAnonymousTokenData::class
        ]);

        $this->createItem([
            'name' => 'test',
            'email' => 'test@toto.com',
            'plainPassword' => '123',
        ]);

        $createdUser = $this->getObjectOf(User::class, ['email' => 'test@toto.com']);

        static::assertNotNull($createdUser);
        static::assertPassword($createdUser, '123');
    }

    public function testForgotPassword()
    {
        $this->loadFixtures([
            LoadAnonymousTokenData::class,
            LoadUserData::class
        ]);

        /** @var User $user */
        $user = $this->getObjectOf(User::class, ['email' => 'artur@gmail.com']);

        $client = $this->getClient();
        $client->enableProfiler();

        $client->request(Request::METHOD_PATCH, $this->url.'/'.$user->getEmail().'/forgot-password', [], [], $this->headers);
        static::assertEquals(204, $this->getClient()->getResponse()->getStatusCode());

        static::assertNotNull($user->getConfirmationToken());

        /** @var MessageDataCollector $emailCollector */
        $emailCollector = $client->getProfile()->getCollector('swiftmailer');
        static::assertSame(1, $emailCollector->getMessageCount());
        /** @var \Swift_Message $message */
        $message = $emailCollector->getMessages()[0];
        static::assertArrayHasKey($user->getEmail(), $message->getTo());
        static::assertContains($user->getConfirmationToken(), $message->getBody());
    }

    public function testResetPassword()
    {
        $this->loadFixtures([
            LoadAnonymousTokenData::class,
            LoadUserData::class
        ]);

        /** @var User $user */
        $user = $this->getObjectOf(User::class, ['email' => 'kirill@gmail.com']);
        $user->setConfirmationToken($confirmationToken = '1234567890');

        $this->getClient()->getContainer()->get('doctrine.orm.default_entity_manager')->flush();

        $data = [
            'plainPassword' => '321'
        ];

        $this->getClient()->request(Request::METHOD_PATCH, $this->url.'/'.$confirmationToken.'/reset-password', $data, [], $this->headers);
        static::assertEquals(200, $this->getClient()->getResponse()->getStatusCode());

        static::assertPassword($user, '321');
    }


    public function testChangePassword()
    {
        $this->loadFixtures([
            LoadAccessTokenData::class,
            LoadUserData::class
        ]);

        /** @var User $user */
        $user = $this->getObjectOf(User::class, ['email' => 'kirill@gmail.com']);

        $this->getClient()->getContainer()->get('doctrine.orm.default_entity_manager')->flush();

        $headers = $this->headers;
        $headers['HTTP_Authorization'] = 'Bearer AccessToken_For_Kirill';

        $data = [
            'currentPassword' => '123',
            'plainPassword' => [
                'first' => '321',
                'second' => '321',
            ],
        ];

        $this->getClient()->request(Request::METHOD_PATCH, $this->url.'/'.$user->getId().'/change-password', $data, [], $headers);

        static::assertEquals(200, $this->getClient()->getResponse()->getStatusCode());
        static::assertPassword($user, '321');


        // 422 - Incorrect current password
        $data = [
            'currentPassword' => '111',
            'plainPassword' => [
                'first' => '456',
                'second' => '456',
            ],
        ];

        $this->getClient()->request(Request::METHOD_PATCH, $this->url.'/'.$user->getId().'/change-password', $data, [], $headers);

        static::assertEquals(422, $this->getClient()->getResponse()->getStatusCode());


        // 422 - The first and second plain password are different
        $data = [
            'currentPassword' => '321',
            'plainPassword' => [
                'first' => '321',
                'second' => '223',
            ],
        ];
        $this->getClient()->request(Request::METHOD_PATCH, $this->url.'/'.$user->getId().'/change-password', $data, [], $headers);

        static::assertEquals(422, $this->getClient()->getResponse()->getStatusCode());


        // 404
        $this->getClient()->request(Request::METHOD_PATCH, $this->url.'/99999999/change-password', $data, [], $headers);

        static::assertEquals(404, $this->getClient()->getResponse()->getStatusCode());
    }

    private function assertPassword(User $user, $password)
    {
        static::assertTrue($this->getContainer()->get('security.password_encoder')->isPasswordValid($user, $password), 'password valid');
    }
}
