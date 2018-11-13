<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/03/2018
 */

namespace Octopouce\AdminBundle\Form;


use App\Entity\Account\User;
use Octopouce\AdminBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$builder
			->add('username', TextType::class)
			->add('firstname', TextType::class, [
				'required' => false
			])
			->add('lastname', TextType::class, [
				'required' => false
			])
			->add('email', EmailType::class)
			->add('phone', TelType::class, [
				'required' => false
			])
			->add('address', TextType::class, [
				'required' => false
			])
			->add('complementAddress', TextType::class, [
				'required' => false
			])
			->add('postalCode', TextType::class, [
				'required' => false
			])
			->add('city', TextType::class, [
				'required' => false
			])

			->add('country', CountryType::class, [
				'placeholder' => 'Choose the country',
				'required' => false
			])
			->add('password', RepeatedType::class, [
				'type' => PasswordType::class,
				'required' => false,
				'mapped' => false,
				'invalid_message' => 'The password fields must match.',
				'options' => array('attr' => array('class' => 'password-field')),
				'first_options'  => array('label' => 'New Password'),
				'second_options' => array('label' => 'Repeat Password'),
			])


			->add('submit', SubmitType::class, [
				'label' => 'submit',
				'translation_domain' => 'button'
			])
		;

		if($options['super_admin']) {
			$roles = [
				'Admin' => 'ROLE_ADMIN'
			];

			if(class_exists('Octopouce\BlogBundle\OctopouceBlogBundle')){
				$roles['Blogger'] = 'ROLE_BLOG';
			}

			if(class_exists('Octopouce\CmsBundle\OctopouceCmsBundle')){
				$roles['CMS'] = 'ROLE_CMS';
			}

			if(class_exists('Octopouce\AdvertisingBundle\OctopouceAdvertisingBundle')){
				$roles['Advertiser'] = 'ROLE_ADVERT';
			}

			$roles['Super Admin'] = 'ROLE_SUPER_ADMIN';

			$builder->add('roles', ChoiceType::class, [
				'choices' => $roles,
				'multiple' => true,
				'placeholder' => 'Choose the role'
			]);
		}
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
			'edit' => false,
			'super_admin' => false
		]);
	}
}
