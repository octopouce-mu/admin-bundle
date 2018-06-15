<?php

namespace Octopouce\AdminBundle\Entity\Account;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass(repositoryClass="Octopouce\AdminBundle\Repository\Account\InvitationRepository")
 */
abstract class Invitation
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=18)
	 */
	protected $code;

	/**
	 * @ORM\Column(type="string", length=256)
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string", length=256)
	 */
	protected $type;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $sent;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $confirm;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $sender;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime")
	 */
	protected $createdAt;

	public function __construct()
	{
		// generate identifier only once, here a 18 characters length code
		$this->code = substr(md5(uniqid(rand(), true)), 0, 18);
		$this->sent = false;
		$this->confirm = false;
		$this->createdAt = new \DateTime();

	}

	public function getCode()
	{
		return $this->code;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function isSent()
	{
		return $this->sent;
	}

	public function send()
	{
		$this->sent = true;
	}

	public function isConfirm()
	{
		return $this->confirm;
	}

	public function confirm()
	{
		$this->confirm = true;
	}

	public function isActive(){
		$now = new \DateTime();
		$expiredAt = $this->getCreatedAt()->modify('+1 month');
		if(($now > $expiredAt) || !$this->isSent() || $this->isConfirm())
			return false;
		else
			return true;
	}

	/**
	 * Set sender.
	 *
	 * @param integer $sender
	 *
	 * @return Invitation
	 */
	public function setSender(int $sender)
	{
		$this->sender = $sender;

		return $this;
	}

	/**
	 * Get sender.
	 *
	 * @return integer
	 */
	public function getSender()
	{
		return $this->sender;
	}
}
