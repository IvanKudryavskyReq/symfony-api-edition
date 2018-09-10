<?php

namespace Tests\AuthBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AuthBundle\Security\UserProvider;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class UserProviderTest
 */
class UserProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test refresh user.
     */
    public function testRefreshUser()
    {
        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userRepository->expects(static::exactly(2))
            ->method('findOneBy')
            ->will($this->onConsecutiveCalls(new User(), null));

        $entityManager->expects(static::exactly(2))
            ->method('getRepository')
            ->willReturn($userRepository);

        $userProvider = new UserProvider($entityManager);
        $user = new User();

        static::assertInstanceOf(User::class, $userProvider->refreshUser($user->setEmail('email')));
        $this->expectException(UsernameNotFoundException::class);

        $userProvider->refreshUser($user->setEmail('notEmail'));
    }

    /**
     * Test class supporting.
     */
    public function testSupportsClass()
    {
        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userProvider = new UserProvider($entityManager);

        static::assertEquals(true, $userProvider->supportsClass(User::class));
        static::assertEquals(false, $userProvider->supportsClass(self::class));
    }
}
