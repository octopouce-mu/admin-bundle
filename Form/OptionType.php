<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/03/2018
 */

namespace Octopouce\AdminBundle\Form;


use Octopouce\AdminBundle\Form\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('options', CollectionType::class)
			->add('submit', SubmitType::class, [
				'label' => 'submit',
				'translation_domain' => 'button'
			])
		;

		if($options['category']->getName() == 'mail'){
			$builder->add('submit_send', SubmitType::class, [
				'label' => 'submit_send',
				'translation_domain' => 'button',
				'attr' => [
					'class' => 'blue-grey'
				]
			]);
		}

		$builder->addEventListener(
			FormEvents::POST_SET_DATA,
			function (FormEvent $event) use ($options) {
				$form = $event->getForm();
				foreach ($options['options'] as $option) {
					if($option->getName() == 'PROJECT_LOGO' || $option->getName() == 'GOOGLE_GA_JSON'){
						$value = $option->getValue() ? ' ('.$option->getValue().')' : '';
						$form
							->get('options')
							->add($option->getId(), FileType::class, [
								'label' => $option->getName().$value,
								'required' =>false
							])
						;
					}elseif(strpos($option->getName(), '_ENABLE')){
						$form
							->get('options')
							->add($option->getId(), SwitchType::class, [
								'label' => $option->getName(),
								'required' =>false,
								'data' => boolval($option->getValue())
							])
						;
					}else{
						$form
							->get('options')
							->add($option->getId(), TextType::class, [
								'label' => $option->getName(),
								'data' => $option->getValue(),
								'required' =>false
							])
						;
					}
				}
			}
		);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setRequired([
			'options',
			'category'
		]);
	}
}