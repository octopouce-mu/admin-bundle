<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-16
 */

namespace Octopouce\AdminBundle\Translatable;

use Doctrine\Common\Collections\ArrayCollection;

trait TranslatableMethods
{
	public function getTranslations()
	{
		return $this->translations = $this->translations ?: new ArrayCollection();
	}

	public function getNewTranslations()
	{
		return $this->newTranslations = $this->newTranslations ?: new ArrayCollection();
	}

	public function addTranslation($translation)
	{
		$this->getTranslations()->set((string)$translation->getLocale(), $translation);
		$translation->setTranslatable($this);

		return $this;
	}

	public function removeTranslation($translation)
	{
		$this->getTranslations()->removeElement($translation);
	}

	public function translate($locale = null, $fallbackToDefault = true)
	{
		return $this->doTranslate($locale, $fallbackToDefault);
	}

	protected function doTranslate($locale = null, $fallbackToDefault = true)
	{
		if (null === $locale) {
			$locale = $this->getCurrentLocale();
		}

		if($locale == $this->getDefaultLocale()) {
			return $this;
		}

		$translation = $this->findTranslationByLocale($locale);
		if ($translation and !$translation->isEmpty()) {
			return $translation;
		}

		if ($fallbackToDefault) {
			if (($fallbackLocale = $this->computeFallbackLocale($locale))
			    && ($translation = $this->findTranslationByLocale($fallbackLocale))) {
				return $translation;
			}

			if ($defaultTranslation = $this->findTranslationByLocale($this->getDefaultLocale(), false)) {
				return $defaultTranslation;
			}
		}

		$class       = static::getTranslationEntityClass();
		$translation = new $class();
		$translation->setLocale($locale);

		$this->getNewTranslations()->set((string)$translation->getLocale(), $translation);
		$translation->setTranslatable($this);

		return $translation;
	}

	public function mergeNewTranslations()
	{
		foreach ($this->getNewTranslations() as $newTranslation) {
			if (!$this->getTranslations()->contains($newTranslation) && !$newTranslation->isEmpty()) {
				$this->addTranslation($newTranslation);
				$this->getNewTranslations()->removeElement($newTranslation);
			}
		}
	}

	public function setCurrentLocale($locale)
	{
		$this->currentLocale = $locale;
	}

	public function getCurrentLocale()
	{
		return $this->currentLocale ?: $this->getDefaultLocale();
	}

	public function setDefaultLocale($locale)
	{
		$this->defaultLocale = $locale;
	}

	public function getDefaultLocale()
	{
		return $this->defaultLocale;
	}

	protected function proxyCurrentLocaleTranslation($method, array $arguments = [])
	{
		return call_user_func_array(
			[$this->translate($this->getCurrentLocale()), $method],
			$arguments
		);
	}

	public static function getTranslationEntityClass()
	{
		return __CLASS__.'Translation';
	}

	protected function findTranslationByLocale($locale, $withNewTranslations = true)
	{
		$translation = $this->getTranslations()->get($locale);

		if ($translation) {
			return $translation;
		}

		if ($withNewTranslations) {
			return $this->getNewTranslations()->get($locale);
		}
	}

	protected function computeFallbackLocale($locale)
	{
		if (strrchr($locale, '_') !== false) {
			return substr($locale, 0, -strlen(strrchr($locale, '_')));
		}

		return false;
	}
}