<?php

namespace AppBundle\DataFixtures\Demo;

use AppBundle\DataFixtures\ORM\LoadSiteData;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

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

        $user = new User();
        $user->setEmail('kirill@gmail.com');
        $user->setName('Kirill');
        $user->setEnabled(true);
        $user->setPlainPassword(123);
        $this->addReference('user-kirill', $user);

        $userManager->updatePassword($user);

        $manager->persist($user);

        $manager->flush();
    }
}
