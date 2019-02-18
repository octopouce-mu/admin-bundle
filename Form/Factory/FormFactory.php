<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-02-18
 */

namespace Octopouce\AdminBundle\Form\Factory;

use Symfony\Component\Form\FormFactoryInterface;

class FormFactory implements FactoryInterface
{
	/**
	 * @var FormFactoryInterface
	 */
	private $formFactory;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * FormFactory constructor.
	 *
	 * @param FormFactoryInterface $formFactory
	 * @param string               $name
	 * @param string               $type
	 */
	public function __construct(FormFactoryInterface $formFactory, $name, $type)
	{
		$this->formFactory = $formFactory;
		$this->name = $name;
		$this->type = $type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function createForm(array $options = array())
	{
		return $this->formFactory->createNamed($this->name, $this->type, null, $options);
	}
}
