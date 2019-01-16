<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-16
 */

namespace Octopouce\AdminBundle\Translatable;


trait TranslatableProperties
{
	protected $translations;

	protected $newTranslations;

	protected $currentLocale;

	protected $defaultLocale = 'en';
}
