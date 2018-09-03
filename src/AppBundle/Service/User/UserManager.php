<?php

namespace AppBundle\Service\User;

use AppBundle\Entity\User;
use Requestum\ApiBundle\Event\FormActionEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** Class UserManager */
class UserManager
{

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * ResetPassword constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param User $user
     */
    public function updatePassword(User $user)
    {
        if ($user->getPlainPassword()) {
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user
                ->setPassword($encodedPassword)
                ->setConfirmationToken(null)
                ->eraseCredentials();
        }
    }
}
