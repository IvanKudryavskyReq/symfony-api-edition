<?php

namespace Tests\AuthBundle\OAuth;

use AppBundle\Repository\UserRepository;
use AppBundle\Subscriber\UserSubscriber;
use AuthBundle\OAuth\SocialGrantExtension;
use AuthBundle\OAuth\SocialProvider\Exception\InvalidAccessTokenException;
use AuthBundle\OAuth\SocialProvider\SocialData;
use AuthBundle\OAuth\SocialProvider\SocialProviderInterface;
use Doctrine\ORM\EntityManager;
use OAuth2\Model\IOAuth2Client;
use OAuth2\OAuth2ServerException;
use Requestum\ApiBundle\Util\ErrorFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SocialGrantExtensionTest
 */
class SocialGrantExtensionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test grant type extension.
     *
     * @throws OAuth2ServerException
     */
    public function testCheckGrantExtension()
    {
        $socialProvider = $this->getMockBuilder(SocialProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $socialProvider->method('getName')->willReturn('provider');

        $socialProvider->expects(static::exactly(2))
            ->method('getSocialData')
            ->will(static::onConsecutiveCalls(
                new SocialData('provider', 1, 'firstName', 'lastName'),
                $this->throwException(new InvalidAccessTokenException())
            ));

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventDispatcher->expects(static::exactly(1))->method('dispatch')->willReturn([]);

        $socialGrantExtension = new SocialGrantExtension($eventDispatcher);
        $socialGrantExtension->addProvider($socialProvider);

        $client = $this->getMockBuilder(IOAuth2Client::class)
            ->disableOriginalConstructor()
            ->getMock();


        $inputData = [
            'network' => 'provider',
            'token' => 'token',
        ];

        static::assertEquals(true, $socialGrantExtension->checkGrantExtension($client, $inputData, []));

        static::assertEquals(false, $socialGrantExtension->checkGrantExtension($client, $inputData, []));
    }

    /**
     * Test fail user provider.
     *
     * @throws OAuth2ServerException
     */
    public function testProviderFail()
    {
        $client = $this->getMockBuilder(IOAuth2Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventDispatcher->expects(static::exactly(0))->method('dispatch');

        $socialGrantExtension = new SocialGrantExtension($eventDispatcher);

        $inputData = [
            'network' => 'provider',
            'token' => 'token',
        ];

        $this->expectException(OAuth2ServerException::class);
        $socialGrantExtension->checkGrantExtension($client, $inputData, []);
    }

    /**
     * Test for validation.
     *
     * @throws OAuth2ServerException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testValidate()
    {
        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userRepository->method('findOneBy')
            ->willReturn(null);

        $entityManager->method('getRepository')
            ->willReturn($userRepository);

        $constraint = $this->getMockBuilder(ConstraintViolation::class)
            ->disableOriginalConstructor()
            ->getMock();

        $constraint->expects(static::once())
            ->method('getPropertyPath')
            ->willReturn('test');

        $validator = $this->getMockBuilder(ValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validator->expects(static::exactly(2))->method('validate')
            ->will(static::onConsecutiveCalls(
                [$constraint],
                []
            ));

        $errorFactory = $this->getMockBuilder(ErrorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userSubscriber = new UserSubscriber($entityManager, $errorFactory, $validator);

        $socialData = new SocialData('facebook', 1, 'firstName', 'lastName', 'SocialLoginTest@test.ua');

        // Test for fail validation
        try {
            $userSubscriber->socialLogin(new GenericEvent($socialData));
            self::fail('Data Validation Error');
        } catch (OAuth2ServerException $e) {
        }

        // Test for success validation
        try {
            $userSubscriber->socialLogin(new GenericEvent($socialData));
        } catch (\Exception $e) {
            self::fail('Error processing data');
        }
    }
}
