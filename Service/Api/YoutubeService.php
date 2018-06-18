<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 13/03/2018
 */

namespace Octopouce\AdminBundle\Service\Api;


use Octopouce\AdminBundle\Service\Transformer\OptionTransformer;
use Symfony\Component\Config\Definition\Exception\Exception;

class YoutubeService {
	/**
	 * @var \Google_Client
	 */
	private $client;
	/**
	 * @var \Google_Service_YouTube
	 */
	private $youtube;

	/**
	 * @var string
	 */
	private $channelId;

	/**
	 * @var boolean
	 */
	private $enabled = false;

	/**
	 * YoutubeService constructor.
	 */
	public function __construct(OptionTransformer $optionTransformer) {
		$options = $optionTransformer->getOptionsWithKeyName();

		if(boolval($options['YOUTUBE_ENABLE']->getValue())) {

			$this->client = new \Google_Client();
			$this->client->setApplicationName( $options['PROJECT_NAME']->getValue() ? $options['PROJECT_NAME']->getValue() : 'Thor' );
			if ( $options['GOOGLE_API_KEY']->getValue() ) {
				$this->client->setDeveloperKey( $options['GOOGLE_API_KEY']->getValue() );
			}

			$this->client->addScope( [ 'https://www.googleapis.com/auth/youtube' ] );
			$this->client->setAuthConfig( '../var/' . $options['GOOGLE_GA_JSON']->getValue() );

			$this->setYoutube( new \Google_Service_YouTube( $this->client ) );
			$this->setChannelId( $options['YOUTUBE_CHANNEL_ID']->getValue() );
			$this->enabled = true;
		}
	}


	/**
	 * @param \Google_Service_YouTube $youtube
	 */
	public function setYoutube( \Google_Service_YouTube $youtube ): void {
		$this->youtube = $youtube;
	}


	/**
	 * @return \Google_Service_YouTube
	 */
	public function getYoutube() {
		return $this->youtube;
	}

	/**
	 * @param \Google_Client $client
	 */
	public function setClient( \Google_Client $client ): void {
		$this->client = $client;
	}

	/**
	 * @return \Google_Client
	 */
	public function getClient() {
		return $this->client;
	}

	/**
	 * @return string
	 */
	public function getChannelId(): string {
		return $this->channelId;
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool {
		return $this->enabled;
	}



	/**
	 * @param string $channelId
	 */
	public function setChannelId( string $channelId ): void {
		$this->channelId = $channelId;
	}

	public function getChannel(){
		if(!$this->isEnabled()) return null;

		$response = $this->getYoutube()->channels->listChannels('statistics', [
			'forUsername' => $this->getChannelId()
		]);

		return $response->items[0];
	}


}