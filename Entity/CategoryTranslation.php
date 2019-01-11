<?php

namespace Octopouce\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * CategoryTranslation
 *
 * @ORM\Table(name="category_translation")
 * @ORM\Entity()
 */
class CategoryTranslation
{
	use ORMBehaviors\Translatable\Translation;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	protected $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="slug", type="string", length=255)
	 */
	protected $slug;


	public function __toString() {
		return $this->getName();
	}

	public function getName() {
		return $this->name;
	}

	public function setName( $name ) {
		$this->name = $name;
	}

	public function getSlug() {
		return $this->slug;
	}

	public function setSlug( $slug ) {
		$this->slug = $slug;
	}
}
