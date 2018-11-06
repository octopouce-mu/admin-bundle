<?php

namespace Octopouce\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Octopouce\AdminBundle\Repository\OptionRepository")
 * @ORM\Table(name="`option`")
 */
class Option
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
	 * @ORM\Column(type="string", unique=true)
	 */
    private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
    private $value;

	/**
	 * @var Category
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="options")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $category;


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
     * @return Option
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
     * Set value.
     *
     * @param string|null $value
     *
     * @return Option
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set category.
     *
     * @param Category $category
     *
     * @return Option
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
