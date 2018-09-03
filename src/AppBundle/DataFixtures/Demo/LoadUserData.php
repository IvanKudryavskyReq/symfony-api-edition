<?php

namespace AppBundle\DataFixtures\Demo;

use AppBundle\DataFixtures\ORM\LoadSiteData;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Workflow\Event\Event;

/**
 * Class LoadUserData
 */
class LoadUserData extends Fixture
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('app.service.user_manager');

        $user = new User();
        $user->setEmail('artur@gmail.com');
        $user->setName('Artur');
        $user->setEnabled(true);
        $user->setPlainPassword(123);
        $this->addReference('user-artur', $user);

        $userManager->updatePassword($user);

        $manager->persist($user);

        $user1 = new User();
        $user1->setEmail('kirill@gmail.com');
        $user1->setName('Kirill');
        $user1->setEnabled(true);
        $user1->setPlainPassword(123);
        $this->addReference('user-kirill', $user1);

        $userManager->updatePassword($user1);

        $manager->persist($user1);

        $manager->flush();
    }
}
