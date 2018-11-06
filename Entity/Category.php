<?php

namespace Octopouce\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass(repositoryClass="Octopouce\AdminBundle\Repository\CategoryRepository")
 */
abstract class Category
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
    private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $slug;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	private $type;

	/**
	 * @var Option[]|ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="Octopouce\AdminBundle\Entity\Option", mappedBy="category", orphanRemoval=true, cascade={"persist"})
	 */
	private $options;

	/**
	 * @var Category
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="childs")
	 */
	private $parent;

	/**
	 * @var Category[]|ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent", orphanRemoval=true, cascade={"persist"})
	 */
	private $childs;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->options = new ArrayCollection();
		$this->childs = new ArrayCollection();
	}

	public function __toString() {
		return $this->name;
	}

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

	/**
	 * Set slug.
	 *
	 * @param string $slug
	 *
	 * @return Category
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;

		return $this;
	}

	/**
	 * Get slug.
	 *
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

    /**
     * Add option.
     *
     * @param Option $option
     *
     * @return Category
     */
    public function addOption(Option $option)
    {
        $this->options[] = $option;

        return $this;
    }

    /**
     * Remove option.
     *
     * @param Option $option
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeOption(Option $option)
    {
        return $this->options->removeElement($option);
    }

    /**
     * Get options.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set parent.
     *
     * @param \App\Entity\Category|null $parent
     *
     * @return Category
     */
    public function setParent(\App\Entity\Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return \App\Entity\Category|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child.
     *
     * @param \App\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(\App\Entity\Category $child)
    {
        $this->childs[] = $child;

        return $this;
    }

    /**
     * Remove child.
     *
     * @param \App\Entity\Category $child
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeChild(\App\Entity\Category $child)
    {
        return $this->childs->removeElement($child);
    }

    /**
     * Get childs.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Category
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
