<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 07/03/2018
 */

namespace Octopouce\AdminBundle\Service\Api;

use Octopouce\AdminBundle\Service\Transformer\OptionTransformer;
use Facebook\Facebook;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * Class FacebookService
 */
class FacebookService {
	/**
	 * @var Facebook
	 */
	private $client;

	/**
	 * @var string
	 */
	private $pageId;

	/**
	 * @var boolean
	 */
	private $enabled = false;

	/**
	 * FacebookService constructor.
	 */
	public function __construct(OptionTransformer $optionTransformer) {
		$options = $optionTransformer->getOptionsWithKeyName();

		if(boolval($options['FACEBOOK_ENABLE']->getValue())) {

			$this->setPageId( $options['FACEBOOK_PAGE_ID']->getValue() );


			$this->client = new Facebook( [
				'app_id'                => $options['FACEBOOK_APP_ID']->getValue(),
				'app_secret'            => $options['FACEBOOK_APP_SECRET']->getValue(),
				'default_graph_version' => 'v2.10',
				//'default_access_token' => '{access-token}', // optional
			] );

			$this->client->setDefaultAccessToken( $this->client->getApp()->getAccessToken() );
			$this->enabled = true;

		}
	}

	/**
	 * @return string
	 */
	public function getPageId(): string {
		return $this->pageId;
	}

	/**
	 * @param string $pageId
	 */
	public function setPageId( string $pageId ): void {
		$this->pageId = $pageId;
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool {
		return $this->enabled;
	}

	private function getRequest($request, $args = null){
		if(!$this->isEnabled()) return null;

		$params = '';
		if($args && is_array($args)){
			$params = '?';
			$cpt = 0;
			foreach ($args as $arg){
				$cpt++;
				$params = $params . 'fields='.$arg;
				if($cpt < count($args)){
					$params = $params . '&';
				}
			}
		}

		try {
			$response = $this->client->get($request.$params);
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			throw new Exception('Graph returned an error: ' . $e->getMessage());
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			throw new Exception('Facebook SDK returned an error: ' . $e->getMessage());
		}

		return $response;
	}


	/**
	 * @return Facebook
	 */
	public function getClient() {
		return $this->client;
	}


	public function getPage($args = null){
		if(!$this->isEnabled()) return null;


		$response = $this->getRequest('/'.$this->getPageId(), $args);

		return $response->getGraphNode();

	}
}











