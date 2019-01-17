<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-09
 */

namespace Octopouce\AdminBundle\Form\EventListener;

use Octopouce\AdminBundle\Form\Manipulator\FormManipulatorInterface;
use Octopouce\AdminBundle\Form\Type\AutoType;
use Octopouce\AdminBundle\Provider\Locale\LocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class TranslationsListener implements EventSubscriberInterface
{
	/** @var FormManipulatorInterface */
	private $formManipulator;

	/** @var LocaleProviderInterface */
	private $localeProvider;

	public function __construct( FormManipulatorInterface $formManipulator, LocaleProviderInterface $localeProvider ) {
		$this->formManipulator = $formManipulator;
		$this->localeProvider = $localeProvider;
	}

	public static function getSubscribedEvents(): array {
		return [
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::SUBMIT       => 'submit',
		];
	}

	public function preSetData( FormEvent $event ): void {
		$form = $event->getForm();
		if ( null === $formParent = $form->getParent() ) {
			throw new \RuntimeException( 'Parent form missing' );
		}
		$formOptions      = $form->getConfig()->getOptions();
		$fieldsOptions    = $this->getFieldsOptions( $form, $formOptions );
		$translationClass = $this->getTranslationClass( $formParent );
		foreach ( $formOptions['locales'] as $locale ) {
			if ( ! isset( $fieldsOptions[ $locale ] ) || $locale == $this->localeProvider->getDefaultLocale() ) {
				continue;
			}
			$form->add( $locale, AutoType::class, [
				'data_class'      => $translationClass,
				'block_name'      => ( 'field' === $formOptions['theming_granularity'] ) ? 'locale' : null,
				'fields'          => $fieldsOptions[ $locale ],
				'excluded_fields' => $formOptions['excluded_fields'],
				'label' => false
			] );
		}
	}

	public function submit( FormEvent $event ): void {
		$data = $event->getData();
		foreach ( $data as $locale => $translation ) {
			// Remove useless Translation object
			if ( ( method_exists( $translation, 'isEmpty' ) && $translation->isEmpty() ) // Knp
			     || empty( $translation ) // Default
			) {
				$data->removeElement( $translation );
				continue;
			}
			$translation->setLocale( $locale );
		}
	}

	public function getFieldsOptions( FormInterface $form, array $formOptions ): array {
		$fieldsOptions = [];
		$fieldsConfig  = $this->formManipulator->getFieldsConfig( $form );
		foreach ( $fieldsConfig as $fieldName => $fieldConfig ) {
			// Simplest case: General options for all locales
			if ( ! isset( $fieldConfig['locale_options'] ) ) {
				foreach ( $formOptions['locales'] as $locale ) {
					$fieldsOptions[ $locale ][ $fieldName ] = $fieldConfig;
				}
				continue;
			}
			// Custom options by locale
			$localesFieldOptions = $fieldConfig['locale_options'];
			unset( $fieldConfig['locale_options'] );
			foreach ( $formOptions['locales'] as $locale ) {
				$localeFieldOptions = isset( $localesFieldOptions[ $locale ] ) ? $localesFieldOptions[ $locale ] : [];
				if ( ! isset( $localeFieldOptions['display'] ) || ( true === $localeFieldOptions['display'] ) ) {
					$fieldsOptions[ $locale ][ $fieldName ] = $localeFieldOptions + $fieldConfig;
				}
			}
		}

		return $fieldsOptions;
	}

	private function getTranslationClass( FormInterface $form ): string {
		do {
			$translatableClass = $form->getConfig()->getDataClass();
		} while ( ( null === $translatableClass ) && $form->getConfig()->getInheritData() && ( null !== $form = $form->getParent() ) );

		// Knp
		if ( method_exists( $translatableClass, 'getTranslationEntityClass' ) ) {
			return $translatableClass::getTranslationEntityClass();
		}


		return $translatableClass . 'Translation';
	}
}