<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 16/03/2018
 */

namespace Octopouce\AdminBundle\Service;

use App\Entity\Account\User;
use Octopouce\AdminBundle\Service\Api\FacebookService;
use Octopouce\AdminBundle\Service\Api\GoogleAnalyticsService;
use Octopouce\AdminBundle\Service\Api\TwitterService;
use Octopouce\AdminBundle\Service\Api\YoutubeService;
use Doctrine\ORM\EntityManagerInterface;
use Octopouce\AdminBundle\Service\Transformer\OptionTransformer;
use Symfony\Component\Cache\Simple\FilesystemCache;

class DashboardService {

	/**
	 * @var GoogleAnalyticsService
	 */
	private $analyticsService;

	/**
	 * @var FacebookService
	 */
	private $facebookService;

	/**
	 * @var TwitterService
	 */
	private $twitterService;

	/**
	 * @var YoutubeService
	 */
	private $youtubeService;

	/**
	 * @var FilesystemCache
	 */
	private $cache;

	/**
	 * @var int
	 */
	private $ttl = 3600;

	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/** @var OptionTransformer */
	private $optionTransformer;

	private $enabled;

	/**
	 * DashboardService constructor.
	 *
	 * @param GoogleAnalyticsService $analyticsService
	 * @param FacebookService $facebookService
	 * @param TwitterService $twitterService
	 * @param YoutubeService $youtubeService
	 */
	public function __construct( GoogleAnalyticsService $analyticsService, FacebookService $facebookService, TwitterService $twitterService, YoutubeService $youtubeService, EntityManagerInterface $em, OptionTransformer $optionTransformer) {
		$this->analyticsService = $analyticsService;
		$this->facebookService  = $facebookService;
		$this->twitterService   = $twitterService;
		$this->youtubeService   = $youtubeService;
		$this->optionTransformer = $optionTransformer->getOptionsWithKeyName();
		$this->cache = new FilesystemCache(isset($this->optionTransformer['PROJECT_URL']) ? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->optionTransformer['PROJECT_URL']->getValue()))) : $this->optionTransformer['PROJECT_NAME']->getValue());
		$this->em = $em;
	}

	public function setEnabled( $enabled )
	{
		$this->enabled = $enabled;
	}


	public function getData(){
		if($this->enabled === false) {
			return $this->enabled;
		}

		$response = [];

		$firstDayLastWeek = new \DateTime();
		$firstDayLastWeek->modify('Last Week')->setTime(0,0,0);
		$lastDayLastWeek = new \DateTime();
		$lastDayLastWeek->modify('Last Week')->modify('Next Monday');

		$firstDayThisWeek = new \DateTime();
		$firstDayThisWeek->modify('monday this week')->setTime(0,0,0);
		$lastDayThisWeek = new \DateTime();
		$lastDayThisWeek->modify('Next Monday');


		$ga = $this->getStatsAnalytics();
		if($ga) $response['ga'] = $ga;

		$facebook = $this->getStatsFacebook();
		if($facebook) $response['rs']['facebook'] = $facebook;

		$twitter = $this->getStatsTwitter();
		if($twitter) $response['rs']['twitter'] = $twitter;

		$youtube = $this->getStatsYoutube();
		if($youtube) $response['rs']['youtube'] = $youtube;

		$response['perf']['newUsers'] = $this->getPerfOfTheWeekNewUsers($firstDayThisWeek, $lastDayThisWeek);
		$response['perf']['newVisitors'] = $this->getPerfOfTheWeekNewVisitors($firstDayLastWeek, $lastDayLastWeek, $firstDayThisWeek, $lastDayThisWeek);

		return $response;
	}

	private function getPerfOfTheWeekNewUsers($firstDayThisWeek, $lastDayThisWeek){
		if(!$this->cache->has('perf.newUsers')){
			$users = $this->em->getRepository(User::class)->countAll();
			if($users) {
				$usersThisWeek = $this->em->getRepository(User::class)->countByCreatedAtTo($firstDayThisWeek, $lastDayThisWeek);
				$perfNewUsers = ($usersThisWeek - $users) / $users * 100;
				$this->cache->set('perf.newUsers', $perfNewUsers, $this->ttl);
			} else {
				$this->cache->set('perf.newUsers', 0, $this->ttl);
			}
		}else{
			$perfNewUsers = $this->cache->get('perf.newUsers');
		}

		return $perfNewUsers;
	}

	private function getPerfOfTheWeekNewVisitors($firstDayLastWeek, $lastDayLastWeek, $firstDayThisWeek, $lastDayThisWeek){


		if(!$this->cache->has('perf.newVisitors')){
			$visitorsLastWeek = $this->analyticsService->getNewUsersDateRange(null, $firstDayLastWeek->format('Y-m-d'), $lastDayLastWeek->format('Y-m-d'));
			$visitorsThisWeek = $this->analyticsService->getNewUsersDateRange(null, $firstDayThisWeek->format('Y-m-d'), $lastDayThisWeek->format('Y-m-d'));
            $perfNewVisitors = 0;
			//$perfNewVisitors = ($visitorsThisWeek - $visitorsLastWeek) / $visitorsLastWeek * 100;
			$this->cache->set('perf.newVisitors', $perfNewVisitors, $this->ttl);
		}else{
			$perfNewVisitors = $this->cache->get('perf.newVisitors');
		}

		return $perfNewVisitors;
	}

	public function clearCache($name = null){
		if($name && $name != 'all'){
			$this->cache->delete($name);
		}else{
			$this->cache->delete('stats');
			$this->cache->delete('stats.google');
			$this->cache->delete('stats.facebook');
			$this->cache->delete('stats.twitter');
			$this->cache->delete('stats.youtube');
			$this->cache->delete('perf');
			$this->cache->delete('perf.newVisitors');
			$this->cache->delete('perf.newUsers');
		}

	}

	private function getStatsAnalytics(){
		if(!$this->cache->has('stats.google')){
			$ga = $this->analyticsService->getDataDashboard();
			$this->cache->set('stats.google', $ga, $this->ttl);
		}else{
			$ga = $this->cache->get('stats.google');
		}

		return $ga;
	}

	private function getStatsFacebook() {
		if(!$this->cache->has('stats.facebook')){
			$facebookPage = $this->facebookService->getPage(['fan_count']);
			$this->cache->set('stats.facebook', $facebookPage, $this->ttl);
		}else{
			$facebookPage = $this->cache->get('stats.facebook');
		}
		return $facebookPage;
	}

	private function getStatsTwitter() {
		if(!$this->cache->has('stats.twitter')){
			$twitterProfile = $this->twitterService->getUser();
			$this->cache->set('stats.twitter', $twitterProfile, $this->ttl);
		}else{
			$twitterProfile = $this->cache->get('stats.twitter');
		}

		return $twitterProfile;
	}


	private function getStatsYoutube() {
		if(!$this->cache->has('stats.youtube')){
			$youtubeChannel = $this->youtubeService->getChannel();
			$this->cache->set('stats.youtube', $youtubeChannel, $this->ttl);
		}else{
			$youtubeChannel = $this->cache->get('stats.youtube');
		}

		return $youtubeChannel;
	}
}
