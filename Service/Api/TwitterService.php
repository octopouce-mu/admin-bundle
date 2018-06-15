<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 13/03/2018
 */

namespace Octopouce\AdminBundle\Service\Api;


use Octopouce\AdminBundle\Service\Transformer\OptionTransformer;

class TwitterService {
	/**
	 * @var string
	 */
	private $oauth_access_token;
	/**
	 * @var string
	 */
	private $oauth_access_token_secret;
	/**
	 * @var string
	 */
	private $consumer_key;
	/**
	 * @var string
	 */
	private $consumer_secret;
	/**
	 * @var array
	 */
	private $postfields;
	/**
	 * @var string
	 */
	private $getfield;
	/**
	 * @var mixed
	 */
	protected $oauth;
	/**
	 * @var string
	 */
	public $url;
	/**
	 * @var string
	 */
	public $requestMethod;
	/**
	 * The HTTP status code from the previous request
	 *
	 * @var int
	 */
	protected $httpStatusCode;

	/**
	 * @var string
	 */
	private $screenName;

	/**
	 * @var boolean
	 */
	private $enabled = false;

	/**
	 * TwitterService constructor.
	 *
	 * @param OptionTransformer $optionTransformer
	 */
	public function __construct(OptionTransformer $optionTransformer)
	{
		if (!function_exists('curl_init'))
		{
			throw new \RuntimeException('TwitterAPIExchange requires cURL extension to be loaded, see: http://curl.haxx.se/docs/install.html');
		}

		$options = $optionTransformer->getOptionsWithKeyName();

		if(boolval($options['TWITTER_ENABLE']->getValue())) {
			$this->setOauthAccessToken( $options['TWITTER_OAUTH_TOKEN']->getValue() );
			$this->setOauthAccessTokenSecret( $options['TWITTER_OAUTH_TOKEN_SECRET']->getValue() );
			$this->setConsumerKey( $options['TWITTER_CONSUMER_KEY']->getValue() );
			$this->setConsumerSecret( $options['TWITTER_CONSUMER_SECRET']->getValue() );

			$this->setScreenName( $options['TWITTER_SCREEN_NAME']->getValue() );
			$this->enabled = true;

		}
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool {
		return $this->enabled;
	}



	/**
	 * @return string
	 */
	public function getScreenName(): string {
		return $this->screenName;
	}

	/**
	 * @param string $screenName
	 */
	public function setScreenName( string $screenName ): void {
		$this->screenName = $screenName;
	}

	/**
	 * @return string
	 */
	public function getOauthAccessToken(): string {
		return $this->oauth_access_token;
	}

	/**
	 * @param string $oauth_access_token
	 */
	public function setOauthAccessToken( string $oauth_access_token ): void {
		$this->oauth_access_token = $oauth_access_token;
	}

	/**
	 * @return string
	 */
	public function getOauthAccessTokenSecret(): string {
		return $this->oauth_access_token_secret;
	}

	/**
	 * @param string $oauth_access_token_secret
	 */
	public function setOauthAccessTokenSecret( string $oauth_access_token_secret ): void {
		$this->oauth_access_token_secret = $oauth_access_token_secret;
	}

	/**
	 * @return string
	 */
	public function getConsumerKey(): string {
		return $this->consumer_key;
	}

	/**
	 * @param string $consumer_key
	 */
	public function setConsumerKey( string $consumer_key ): void {
		$this->consumer_key = $consumer_key;
	}

	/**
	 * @return string
	 */
	public function getConsumerSecret(): string {
		return $this->consumer_secret;
	}

	/**
	 * @param string $consumer_secret
	 */
	public function setConsumerSecret( string $consumer_secret ): void {
		$this->consumer_secret = $consumer_secret;
	}



	/**
	 * Set postfields array, example: array('screen_name' => 'J7mbo')
	 *
	 * @param array $array Array of parameters to send to API
	 *
	 * @throws \Exception When you are trying to set both get and post fields
	 *
	 * @return TwitterAPIExchange Instance of self for method chaining
	 */
	public function setPostfields(array $array)
	{
		if (!is_null($this->getGetfield()))
		{
			throw new \Exception('You can only choose get OR post fields (post fields include put).');
		}
		if (isset($array['status']) && substr($array['status'], 0, 1) === '@')
		{
			$array['status'] = sprintf("\0%s", $array['status']);
		}
		foreach ($array as $key => &$value)
		{
			if (is_bool($value))
			{
				$value = ($value === true) ? 'true' : 'false';
			}
		}
		$this->postfields = $array;
		// rebuild oAuth
		if (isset($this->oauth['oauth_signature']))
		{
			$this->buildOauth($this->url, $this->requestMethod);
		}
		return $this;
	}
	/**
	 * Set getfield string, example: '?screen_name=J7mbo'
	 *
	 * @param string $string Get key and value pairs as string
	 *
	 * @throws \Exception
	 *
	 * @return \TwitterAPIExchange Instance of self for method chaining
	 */
	public function setGetfield($string)
	{
		if (!is_null($this->getPostfields()))
		{
			throw new \Exception('You can only choose get OR post / post fields.');
		}
		$getfields = preg_replace('/^\?/', '', explode('&', $string));
		$params = array();
		foreach ($getfields as $field)
		{
			if ($field !== '')
			{
				list($key, $value) = explode('=', $field);
				$params[$key] = $value;
			}
		}
		$this->getfield = '?' . http_build_query($params, '', '&');
		return $this;
	}
	/**
	 * Get getfield string (simple getter)
	 *
	 * @return string $this->getfields
	 */
	public function getGetfield()
	{
		return $this->getfield;
	}
	/**
	 * Get postfields array (simple getter)
	 *
	 * @return array $this->postfields
	 */
	public function getPostfields()
	{
		return $this->postfields;
	}
	/**
	 * Build the Oauth object using params set in construct and additionals
	 * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1
	 *
	 * @param string $url           The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json
	 * @param string $requestMethod Either POST or GET
	 *
	 * @throws \Exception
	 *
	 * @return \TwitterAPIExchange Instance of self for method chaining
	 */
	public function buildOauth($url, $requestMethod)
	{
		if (!in_array(strtolower($requestMethod), array('post', 'get', 'put', 'delete')))
		{
			throw new \Exception('Request method must be either POST, GET or PUT or DELETE');
		}
		$consumer_key              = $this->consumer_key;
		$consumer_secret           = $this->consumer_secret;
		$oauth_access_token        = $this->oauth_access_token;
		$oauth_access_token_secret = $this->oauth_access_token_secret;
		$oauth = array(
			'oauth_consumer_key' => $consumer_key,
			'oauth_nonce' => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_token' => $oauth_access_token,
			'oauth_timestamp' => time(),
			'oauth_version' => '1.0'
		);
		$getfield = $this->getGetfield();
		if (!is_null($getfield))
		{
			$getfields = str_replace('?', '', explode('&', $getfield));
			foreach ($getfields as $g)
			{
				$split = explode('=', $g);
				/** In case a null is passed through **/
				if (isset($split[1]))
				{
					$oauth[$split[0]] = urldecode($split[1]);
				}
			}
		}
		$postfields = $this->getPostfields();
		if (!is_null($postfields)) {
			foreach ($postfields as $key => $value) {
				$oauth[$key] = $value;
			}
		}
		$base_info = $this->buildBaseString($url, $requestMethod, $oauth);
		$composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
		$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
		$oauth['oauth_signature'] = $oauth_signature;
		$this->url           = $url;
		$this->requestMethod = $requestMethod;
		$this->oauth         = $oauth;
		return $this;
	}
	/**
	 * Perform the actual data retrieval from the API
	 *
	 * @param boolean $return      If true, returns data. This is left in for backward compatibility reasons
	 * @param array   $curlOptions Additional Curl options for this request
	 *
	 * @throws \Exception
	 *
	 * @return string json If $return param is true, returns json data.
	 */
	public function performRequest($return = true, $curlOptions = array())
	{
		if (!is_bool($return))
		{
			throw new \Exception('performRequest parameter must be true or false');
		}
		$header =  array($this->buildAuthorizationHeader($this->oauth), 'Expect:');
		$getfield = $this->getGetfield();
		$postfields = $this->getPostfields();
		if (in_array(strtolower($this->requestMethod), array('put', 'delete')))
		{
			$curlOptions[CURLOPT_CUSTOMREQUEST] = $this->requestMethod;
		}
		$options = $curlOptions + array(
				CURLOPT_HTTPHEADER => $header,
				CURLOPT_HEADER => false,
				CURLOPT_URL => $this->url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 10,
			);
		if (!is_null($postfields))
		{
			$options[CURLOPT_POSTFIELDS] = http_build_query($postfields, '', '&');
		}
		else
		{
			if ($getfield !== '')
			{
				$options[CURLOPT_URL] .= $getfield;
			}
		}
		$feed = curl_init();
		curl_setopt_array($feed, $options);
		$json = curl_exec($feed);
		$this->httpStatusCode = curl_getinfo($feed, CURLINFO_HTTP_CODE);
		if (($error = curl_error($feed)) !== '')
		{
			curl_close($feed);
			throw new \Exception($error);
		}
		curl_close($feed);
		return $json;
	}
	/**
	 * Private method to generate the base string used by cURL
	 *
	 * @param string $baseURI
	 * @param string $method
	 * @param array  $params
	 *
	 * @return string Built base string
	 */
	private function buildBaseString($baseURI, $method, $params)
	{
		$return = array();
		ksort($params);
		foreach($params as $key => $value)
		{
			$return[] = rawurlencode($key) . '=' . rawurlencode($value);
		}
		return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
	}
	/**
	 * Private method to generate authorization header used by cURL
	 *
	 * @param array $oauth Array of oauth data generated by buildOauth()
	 *
	 * @return string $return Header used by cURL for request
	 */
	private function buildAuthorizationHeader(array $oauth)
	{
		$return = 'Authorization: OAuth ';
		$values = array();
		foreach($oauth as $key => $value)
		{
			if (in_array($key, array('oauth_consumer_key', 'oauth_nonce', 'oauth_signature',
				'oauth_signature_method', 'oauth_timestamp', 'oauth_token', 'oauth_version'))) {
				$values[] = "$key=\"" . rawurlencode($value) . "\"";
			}
		}
		$return .= implode(', ', $values);
		return $return;
	}
	/**
	 * Helper method to perform our request
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $data
	 * @param array  $curlOptions
	 *
	 * @throws \Exception
	 *
	 * @return string The json response from the server
	 */
	public function request($url, $method = 'get', $data = null, $curlOptions = array())
	{
		if (strtolower($method) === 'get')
		{
			$this->setGetfield($data);
		}
		else
		{
			$this->setPostfields($data);
		}
		return $this->buildOauth($url, $method)->performRequest(true, $curlOptions);
	}
	/**
	 * Get the HTTP status code for the previous request
	 *
	 * @return integer
	 */
	public function getHttpStatusCode()
	{
		return $this->httpStatusCode;
	}

	/**
	 * @return mixed
	 */
	public function getUser(){
		if(!$this->isEnabled()) return null;

		$url = 'https://api.twitter.com/1.1/users/lookup.json';
		$requestMethod = 'GET';

		$fields = 'screen_name='.$this->getScreenName();

		$response = $this->setGetfield('?'.$fields)
                   ->request($url, $requestMethod);

		return json_decode($response)[0];
	}
}