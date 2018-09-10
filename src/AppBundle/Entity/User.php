<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Requestum\ApiBundle\Rest\Metadata\Reference;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\EntityListeners({"AppBundle\EventListener\UserListener"})
 * @UniqueEntity("email", message="User with email {{ value }} already exist in the system.")
 */
class User implements UserInterface
{

    use TimestampableEntity;

    /**
     * @var int The entity Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups("default")
     */
    private $id;

    /**
     * @var string First name
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank
     *
     * @Groups("default")
     */
    private $firstName = '';

    /**
     * @var string Last name
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank
     *
     * @Groups("default")
     */
    private $lastName = '';

    /**
     * @var string User email
     *
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\NotBlank
     * @Assert\Email()
     *
     * @Groups("default")
     */
    private $email;

    /**
     * @var string Encrypted password.
     * @ORM\Column(type="string")
     *
     */
    protected $password;

    /**
     * @var string Encrypted password.
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank
     *
     */
    protected $plainPassword;

    /**
     * @var string User level
     *
     * @ORM\Column(type="boolean")
     *
     * @Groups("default")
     */
    private $enabled;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $confirmationToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $socialId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $socialNetwork;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->enabled = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        if (!$this->email) {
            return ['ROLE_UNCOMPLETED_PROFILE'];
        }
        return ['ROLE_USER'];
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
        $this->password = null;

        return $this;
    }

    /**
     *
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return bool|string
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $boolean
     * @return $this
     */
    public function setEnabled($boolean)
    {
        $this->enabled = $boolean;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     * @return $this
     */
    public function setConfirmationToken($confirmationToken = null)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }


    /**
     * @return string
     */
    public function getSocialNetwork()
    {
        return $this->socialNetwork;
    }

    /**
     * @param string $socialNetwork
     *
     * @return $this
     */
    public function setSocialNetwork($socialNetwork)
    {
        $this->socialNetwork = $socialNetwork;

        return $this;
    }

    /**
     * @return string
     */
    public function getSocialId()
    {
        return $this->socialId;
    }

    /**
     * @param string $socialId
     *
     * @return $this
     */
    public function setSocialId($socialId)
    {
        $this->socialId = $socialId;

        return $this;
    }
}
