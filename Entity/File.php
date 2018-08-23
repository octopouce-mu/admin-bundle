<?php

namespace Octopouce\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity()
 */
class File
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="title", type="string", length=255)
	 */
	protected $title;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="path", type="string", length=255)
	 */
	protected $path;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="alt", type="string", length=255, nullable=true)
	 */
	protected $alt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created_at", type="datetime")
	 */
	protected $createdAt;

	/**
	 * File constructor.
	 */
	public function __construct() {
		$this->createdAt   = new \DateTime('now');
	}


	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set title
	 *
	 * @param string $title
	 *
	 * @return File
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set alt
	 *
	 * @param string $alt
	 *
	 * @return File
	 */
	public function setAlt($alt)
	{
		$this->alt = $alt;

		return $this;
	}

	/**
	 * Get alt
	 *
	 * @return string
	 */
	public function getAlt()
	{
		return $this->alt;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return File
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Get path
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt(): \DateTime {
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $createdAt
	 * @return File
	 */
	public function setCreatedAt( \DateTime $createdAt ) {
		$this->createdAt = $createdAt;

		return $this;
	}

}
