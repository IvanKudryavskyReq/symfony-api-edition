<?php

namespace AuthBundle\OAuth\SocialProvider;

/**
 * Class SocialData.
 */
class SocialData
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $avatar;

    /**
     * @var string
     */
    public $socialNetwork;

    /**
     * SocialData constructor.
     *
     * @param $socialNetwork
     * @param string $id
     * @param string $firstName
     * @param string $lastName
     * @param string|null $email
     * @param string|null $avatar
     */
    public function __construct($socialNetwork, $id, $firstName, $lastName, $email = null, $avatar = null)
    {
        $this->socialNetwork = $socialNetwork;
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->avatar = $avatar;
    }
}
