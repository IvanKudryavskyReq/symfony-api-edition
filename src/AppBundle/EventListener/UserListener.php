<?php

namespace AppBundle\EventListener;

use AppBundle\Service\User\UserManager;
use Requestum\ApiBundle\Event\FormActionEvent;

/**
 * Class UserListener
 */
class UserListener
{

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * UserListener constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param FormActionEvent $event
     */
    public function updatePassword(FormActionEvent $event)
    {
        $this->userManager->updatePassword($event->getSubject());
    }
}
