<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-09
 */

namespace Octopouce\AdminBundle\Form\Manipulator;

use Symfony\Component\Form\FormInterface;

interface FormManipulatorInterface
{
	public function getFieldsConfig(FormInterface $form): array;
}