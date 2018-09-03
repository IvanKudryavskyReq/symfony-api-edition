<?php

namespace AppBundle\Form\User;

use Requestum\ApiBundle\Form\Type\AbstractApiType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * Class ChangePasswordType
 */
class ChangePasswordType extends AbstractApiType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [new UserPassword()],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
            ])
        ;
    }
}
