<?php

namespace Octopouce\AdminBundle\Entity\Account;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass(repositoryClass="Octopouce\AdminBundle\Repository\Account\UserRepository")
 */
abstract class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstname;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $lastname;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $address;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $complementAddress;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $postalCode;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $city;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $country;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    protected $username;


    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Image(
     *     minWidth = 100,
     *     maxWidth = 250,
     *     minHeight = 100,
     *     maxHeight = 250
     * )
     */
    protected $image;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $password;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $resetToken;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $passwordRequestedAt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="date", nullable=true)
	 */
	protected $birthday;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $roles;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	protected $enabled;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime")
	 */
	protected $createdAt;


	public function __construct()
	{
		$this->invitations = new ArrayCollection();
		$this->roles = array('ROLE_USER');
		$this->enabled = false;
		$this->createdAt = new \DateTime();
	}

	public function __toString() {
		return $this->getUsername();
	}


	public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

	public function hasRole($role): bool
	{
		return in_array(strtoupper($role), $this->getRoles(), true);
	}

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

	public function addRole($role)
	{
		$role = strtoupper($role);
		if ($role === 'ROLE_USER') {
			return $this;
		}
		if (!in_array($role, $this->roles, true)) {
			$this->roles[] = $role;
		}
		return $this;
	}

	public function removeRole($role)
	{
		if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
			unset($this->roles[$key]);
			$this->roles = array_values($this->roles);
		}
		return $this;
	}


    /**
     * Returns the salt that was originally used to encode the password.
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
        // we're using bcrypt in security.yml to encode the password, so
        // the salt value is built-in and you don't have to generate one

        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    /**
     * Set firstname.
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname.
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname.
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname.
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return User
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set complementAddress.
     *
     * @param string|null $complementAddress
     *
     * @return User
     */
    public function setComplementAddress($complementAddress = null)
    {
        $this->complementAddress = $complementAddress;

        return $this;
    }

    /**
     * Get complementAddress.
     *
     * @return string|null
     */
    public function getComplementAddress()
    {
        return $this->complementAddress;
    }

    /**
     * Set postalCode.
     *
     * @param string|null $postalCode
     *
     * @return User
     */
    public function setPostalCode($postalCode = null)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode.
     *
     * @return string|null
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set city.
     *
     * @param string|null $city
     *
     * @return User
     */
    public function setCity($city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country.
     *
     * @param string|null $country
     *
     * @return User
     */
    public function setCountry($country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return User
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set resetToken.
     *
     * @param string|null $resetToken
     *
     * @return User
     */
    public function setResetToken($resetToken = null)
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * Get resetToken.
     *
     * @return string
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }

    /**
     * Set passwordRequestedAt.
     *
     * @param \DateTime|null $passwordRequestedAt
     *
     * @return User
     */
    public function setPasswordRequestedAt($passwordRequestedAt = null)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    /**
     * Get passwordRequestedAt.
     *
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

	public function isPasswordRequestNonExpired($ttl)
	{
		return $this->getPasswordRequestedAt() instanceof \DateTime &&
		       $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
	}

	/**
	 * @return \DateTime
	 */
	public function getBirthday() {
		return $this->birthday;
	}

	/**
	 * @param \DateTime $birthday
	 *
	 * @return User
	 */
	public function setBirthday( \DateTime $birthday ){
		$this->birthday = $birthday;

		return $this;
	}

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $createdAt
	 *
	 * @return User
	 */
	public function setCreatedAt( \DateTime $createdAt ){
		$this->createdAt = $createdAt;

		return $this;
	}

}
