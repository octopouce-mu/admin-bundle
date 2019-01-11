<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-01-09
 */

namespace Octopouce\AdminBundle\Provider\Locale;

interface LocaleProviderInterface
{
	public function getLocales(): array;

	public function setLocales($locales): void;

	public function getDefaultLocale(): string;
}
