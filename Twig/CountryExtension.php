<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 2019-03-08
 */

namespace Octopouce\AdminBundle\Twig;

use Symfony\Component\Intl\Intl;

class CountryExtension extends \Twig_Extension
{
	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('country', array($this, 'country')),
		);
	}

	public function country($countryCode){
		return Intl::getRegionBundle()->getCountryName($countryCode);
	}

	public function getName()
	{
		return 'country_extension';
	}
}
