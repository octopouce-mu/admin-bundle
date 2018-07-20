<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 03/04/2018
 */

namespace Octopouce\AdminBundle\Form\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class TranslationFieldSubscriber implements EventSubscriberInterface
{
	private $locales;

	/**
	 * TranslationFieldListener constructor.
	 *
	 * @param $locales
	 */
	public function __construct( $locales ) {
		$this->locales = explode('|', $locales);
	}


	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::PRE_SET_DATA => 'onPreSetData',
		);
	}

	public function onPreSetData(FormEvent $event)
	{
		$entity = $event->getData();
		$form = $event->getForm();

		if(method_exists($entity, 'getTranslations')){
			$translations = $entity->getTranslations();
			if($translations->count() != count($this->locales)){
				$locales = $this->locales;
				foreach ($translations as $translation){
					$key = array_search($translation->getLocale(), $this->locales);
					if($key){
						unset($this->locales[$key]);
					}
				}

				foreach ($this->locales as $locale){
					$class = '\\'.get_class($entity).'Translation';
					$entityTranslation = new $class();
					$entityTranslation->setLocale($locale);
					$entity->addTranslation($entityTranslation);
				}
			}

		}

	}
}