<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-09
 */

namespace Octopouce\AdminBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TranslationsFormsListener implements EventSubscriberInterface
{
	public static function getSubscribedEvents(): array {
		return [
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::SUBMIT       => 'submit',
		];
	}

	public function preSetData( FormEvent $event ): void {
		$form        = $event->getForm();
		$formOptions = $form->getConfig()->getOptions();
		foreach ( $formOptions['locales'] as $locale ) {
			$form->add( $locale, $formOptions['form_type'],
				$formOptions['form_options'] + [
					'required' => in_array( $locale, $formOptions['required_locales'], true ),
				]
			);
		}
	}

	public function submit( FormEvent $event ): void {
		$data = $event->getData();
		foreach ( $data as $locale => $translation ) {
			// Remove useless Translation object
			if ( empty( $translation ) ) {
				$data->removeElement( $translation );
				continue;
			}
			$translation->setLocale( $locale );
		}
	}
}