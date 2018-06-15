<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 22/03/2018
 */

namespace Octopouce\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatePickerType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'widget' => 'single_text',
			'format' => 'yyyy/MM/dd',
			'html5'  => false
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return DateType::class;
	}
}