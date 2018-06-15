<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 22/03/2018
 */

namespace Octopouce\AdminBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimePickerType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'date_widget' => 'single_text',
			'time_widget' => 'single_text',
			'date_format'      => 'YYYY/MM/dd',
			'time_format'      => 'H:i',
			'html5' => false
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return DateTimeType::class;
	}
}