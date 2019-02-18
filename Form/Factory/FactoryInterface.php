<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-02-18
 */

namespace Octopouce\AdminBundle\Form\Factory;


use Symfony\Component\Form\FormInterface;

interface FactoryInterface {
	/**
	 * @return FormInterface
	 */
	public function createForm();
}
