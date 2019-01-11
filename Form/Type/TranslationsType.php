<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-09
 */


namespace Octopouce\AdminBundle\Form\Type;

use Octopouce\AdminBundle\Form\EventListener\TranslationsListener;
use Octopouce\AdminBundle\Provider\Locale\LocaleProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TranslationsType extends AbstractType
{
	/** @var TranslationsListener */
	private $translationsListener;

	/** @var LocaleProviderInterface */
	private $localeProvider;

	public function __construct( TranslationsListener $translationsListener, LocaleProviderInterface $localeProvider ) {
		$this->translationsListener = $translationsListener;
		$this->localeProvider       = $localeProvider;
	}

	public function buildForm( FormBuilderInterface $builder, array $options ): void {
		$builder->addEventSubscriber( $this->translationsListener );
	}

	public function buildView( FormView $view, FormInterface $form, array $options ): void {
		$view->vars['default_locale']   = $options['default_locale'];
		$view->vars['required_locales'] = $options['required_locales'];
	}

	public function configureOptions( OptionsResolver $resolver ): void {

		$resolver->setDefaults( [
			'by_reference'        => false,
			'empty_data'          => function ( FormInterface $form ) {
				return new ArrayCollection();
			},
			'locales'             => $this->localeProvider->getLocales(),
			'default_locale'      => $this->localeProvider->getDefaultLocale(),
			'required_locales'    => [],
			'theming_granularity' => 'field',
			'fields'              => [],
			'excluded_fields'     => ['id', 'locale'],
			'label'               => false
		] );
		$resolver->setAllowedValues( 'theming_granularity', [ 'field', 'locale_field' ] );
	}
}