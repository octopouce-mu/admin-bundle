<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-09
 */

namespace Octopouce\AdminBundle\Provider\Locale;

class LocaleProvider implements LocaleProviderInterface
{
	/** @var string|array */
	protected $locales;

	/** @var string */
	protected $defaultLocale;

	public function __construct(string $defaultLocale) {
		$this->defaultLocale   = $defaultLocale;
	}


	public function setLocales( $locales ): void {
		if(!is_array($locales)) {
			$locales = explode('|', $locales);
		}
		$this->locales = $locales;
	}

	public function getLocales(): array {
		return $this->locales;
	}

	public function getDefaultLocale(): string {
		return $this->defaultLocale;
	}
}