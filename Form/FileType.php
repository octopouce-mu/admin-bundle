<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/03/2018
 */

namespace Octopouce\AdminBundle\Form;

use Octopouce\AdminBundle\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType as FileFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{


		$builder
			->add('title', TextType::class)
			->add('alt', TextType::class, [
				'required' => false
			])
			->add('path', FileFormType::class, [
				'required' => false,
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => File::class
		]);
	}
}