<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\User;
use AppBundle\Service\User\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Requestum\ApiBundle\Util\ErrorFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserSubscriber
 */
class UserSubscriber implements EventSubscriberInterface
{

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var ErrorFactory $errorFactory */
    private $errorFactory;

    /** @var ValidatorInterface  $validator*/
    private $validator;

    /**
     * UserEntityListener constructor.
     * @param EntityManagerInterface $entityManager
     * @param ErrorFactory $errorFactory
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $entityManager, ErrorFactory $errorFactory, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->errorFactory = $errorFactory;
        $this->validator = $validator;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return ['oauth2.social_login.complete' => 'socialLogin'];
    }

    /**
     * @param GenericEvent $event
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \OAuth2\OAuth2ServerException
     */
    public function socialLogin(GenericEvent $event)
    {
        $socialData = $event->getSubject();
        $repository = $this->entityManager->getRepository(User::class);
        if (!$user = $repository->findOneBy(['socialId' => $socialData->id, 'socialNetwork' => $socialData->socialNetwork])) {
            $user = new User();
            $user->setSocialId($socialData->id);
            $user->setSocialNetwork($socialData->socialNetwork);
            $user->setEmail($socialData->email);
            $user->setPlainPassword(uniqid());

            if ($socialData->firstName && $socialData->lastName) {
                $user->setFirstName($socialData->firstName);
                $user->setLastName($socialData->lastName);
            }

            /** @var ConstraintViolation[] $constraints */
            $constraints = $this->validator->validate($user, null, ['Social']);

            if (count($constraints)) {
                $errors = [];
                $accessor = PropertyAccess::createPropertyAccessor();
                foreach ($constraints as $constraint) {
                    $accessor->setValue(
                        $errors,
                        '['.str_replace('.', '][', $constraint->getPropertyPath()).']',
                        $this->errorFactory->createFromConstraintViolation($constraint)
                    );
                }

                throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, 'unprocessable_user');
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}
