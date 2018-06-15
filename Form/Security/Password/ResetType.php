<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 08/05/2018
 */

namespace Octopouce\AdminBundle\Form\Security\Password;

use App\Entity\Account\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('password', RepeatedType::class, [
				'type' => PasswordType::class,
				'required' => true,
				'mapped' => false,
				'invalid_message' => 'The password fields must match.',
				'options' => array('attr' => array('class' => 'password-field')),
				'first_options'  => array(
					'label' => 'modify_password.new_password',
					'label_attr' => array('class' => 'sr-only'),
					'attr' => array(
						'placeholder' => 'modify_password.new_password'
					)
				),
				'second_options' => array(
					'label' => 'modify_password.confirm_password',
					'label_attr' => array('class' => 'sr-only'),
					'attr' => array(
						'placeholder' => 'modify_password.confirm_password'
					)
				)
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class
		]);
	}
}