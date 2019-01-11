<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-09
 */

namespace Octopouce\AdminBundle\Form\Type;

use Octopouce\AdminBundle\Form\EventListener\AutoFormListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoType extends AbstractType
{
	/** @var AutoFormListener */
	private $autoFormListener;

	public function __construct(AutoFormListener $autoFormListener)
	{
		$this->autoFormListener = $autoFormListener;
	}
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder->addEventSubscriber($this->autoFormListener);
	}
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'fields' => [],
			'excluded_fields' => [],
		]);
		$resolver->setNormalizer('data_class', function (Options $options, $value): string {
			if (empty($value)) {
				throw new \RuntimeException(sprintf('Missing "data_class" option of "AutoFormType".'));
			}
			return $value;
		});
	}
}