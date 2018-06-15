<?php

namespace Octopouce\AdminBundle\DataFixtures;

use Octopouce\AdminBundle\Entity\Option;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class OptionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
	    foreach ($this->getOptionData() as [$nameOption, $value, $categoryName]) {

		    $option = new Option();
		    $option->setName($nameOption);
		    $option->setValue($value);
		    $option->setCategory($this->getReference('cat-'.$categoryName));

		    $manager->persist($option);
	    }

	    $manager->flush();
    }


	private function getOptionData(): array
	{
		return [
			// $optionData = [$name, $value, $categoryName];
			['GOOGLE_ENABLE', '0', 'google'],
			['GOOGLE_PAGE_URL', null, 'google'],
			['GOOGLE_API_KEY', null, 'google'],
			['GOOGLE_CLIENT_ID', null, 'google'],
			['GOOGLE_CLIENT_SECRET', null, 'google'],
			['GOOGLE_GA_JSON', null, 'google'],
			['GOOGLE_GA_VIEW', null, 'google'],


			['FACEBOOK_ENABLE', '0', 'facebook'],
			['FACEBOOK_PAGE_ID', null, 'facebook'],
			['FACEBOOK_PAGE_URL', null, 'facebook'],
			['FACEBOOK_APP_ID', null, 'facebook'],
			['FACEBOOK_APP_SECRET', null, 'facebook'],


			['TWITTER_ENABLE', '0', 'twitter'],
			['TWITTER_SCREEN_NAME', null, 'twitter'],
			['TWITTER_PAGE_URL', null, 'twitter'],
			['TWITTER_OAUTH_TOKEN', null, 'twitter'],
			['TWITTER_OAUTH_TOKEN_SECRET', null, 'twitter'],
			['TWITTER_CONSUMER_KEY', null, 'twitter'],
			['TWITTER_CONSUMER_SECRET', null, 'twitter'],

			['YOUTUBE_ENABLE', '0', 'youtube'],
			['YOUTUBE_CHANNEL_ID', null, 'youtube'],

			['INSTAGRAM_PAGE_URL', null, 'instagram'],

			['PROJECT_NAME', 'Thor', 'general'],
			['PROJECT_LOGO', null, 'general'],

			['COMPANY_NAME', null, 'company'],
			['COMPANY_ADDRESS', null, 'company'],
			['COMPANY_ADDRESS2', null, 'company'],
			['COMPANY_POSTAL_CODE', null, 'company'],
			['COMPANY_CITY', null, 'company'],
			['COMPANY_COUNTRY', null, 'company'],
			['COMPANY_PHONE', null, 'company'],
			['COMPANY_MAIL', null, 'company'],

			['MAIL_SMTP_HOST', 'ssl0.ovh.net', 'mail'],
			['MAIL_SMTP_USERNAME', 'client@octopouce.mu', 'mail'],
			['MAIL_SMTP_PASSWORD', 'wjAXUTVUwgs6', 'mail'],
			['MAIL_SMTP_PORT', '587', 'mail'],
			['MAIL_SMTP_ENCRYPTION', null, 'mail'],
			['MAIL_FROM_NAME', 'Thor', 'mail'],
			['MAIL_FROM_EMAIL', 'noreply@octopouce.mu', 'mail'],
			['MAIL_TO_DEV', 'kevin@octopouce.mu', 'mail'],
		];
	}
}
