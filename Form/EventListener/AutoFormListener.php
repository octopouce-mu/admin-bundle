<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-09
 */


namespace Octopouce\AdminBundle\Form\EventListener;

use Octopouce\AdminBundle\Form\Manipulator\FormManipulatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AutoFormListener implements EventSubscriberInterface
{
	/** @var FormManipulatorInterface */
	private $formManipulator;

	public function __construct(FormManipulatorInterface $formManipulator)
	{
		$this->formManipulator = $formManipulator;
	}
	public static function getSubscribedEvents(): array
	{
		return [
			FormEvents::PRE_SET_DATA => 'preSetData',
		];
	}
	public function preSetData(FormEvent $event): void
	{
		$form = $event->getForm();
		$fieldsOptions = $this->formManipulator->getFieldsConfig($form);
		foreach ($fieldsOptions as $fieldName => $fieldConfig) {
			$fieldType = $fieldConfig['field_type'] ?? null;
			unset($fieldConfig['field_type']);
			$form->add($fieldName, $fieldType, $fieldConfig);
		}
	}
}