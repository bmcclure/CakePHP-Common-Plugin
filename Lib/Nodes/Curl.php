<?php
namespace Nodes;

/**
* cURL class helper
*
* Please make sure to always use CURLOPT_* constants and not the string versions
* The class will throw exceptions on errors, so remember to catch them
*
* @platform
* @package Core.Lib
* @copyright Nodes ApS 2010-2012 <tech@nodes.dk>
*/
class Curl {
	/**
	* The cURL resource created by curl_init
	*
	* @var resource
	*/
	protected $curlResource;

	/**
	* The default cURL options
	*
	* @var array
	*/
	protected $defaultCurlOptions = array(
		CURLOPT_RETURNTRANSFER	=> true, // Always return the HTTP body
		CURLOPT_CONNECTTIMEOUT	=> 2,	 // If we can't connect for 2 seconds, abort
		CURLOPT_TIMEOUT			=> 5	 // Our request should be able to complete within 5 seconds
	);

	/**
	* The current curl options
	*
	* @var array
	*/
	protected $curlOptions = array();

	/**
	* The response body from an executed HTTP request
	*
	* @var string
	*/
	protected $responseBody;

	/**
	* Array with response headers with all header keys lowered
	*
	* @var array
	*/
	protected $responseHeadersArray = array();

	/**
	* Array with response headers (Raw)
	*
	* @var array
	*/
	protected $responseHeadersArrayRaw = array();

	/**
	* The last cURL error
	*
	* @var string
	*/
	protected $curlError;

	/**
	* Constructor
	*
	* @param string $url		The URL to query
	* @param array $options		cURL options
	*/
	public function __construct($url = null, $options = array()) {
		// Merge all options
		$this->curlOptions = $this->defaultCurlOptions + $options + array(CURLOPT_URL => $url);
	}

	/**
	* Execute a HTTP GET request
	*
	* @return mixed
	*/
	public function get() {
		$this->curlOptions = $this->curlOptions + array(CURLOPT_CUSTOMREQUEST => 'GET');
		return $this->exec();
	}

	/**
	* Execute a HTTP POST request
	*
	* @param array $data POST data
	* @return mixed
	*/
	public function post($data) {
		return $this->exec($this->curlOptions + array(CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => http_build_query($data)));
	}

	/**
	* Execute a HTTP PUT request
	*
	* @param array $data POST data
	* @return mixed
	*/
	public function put($data) {
		return $this->exec($this->curlOptions + array(CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_POSTFIELDS => http_build_query($data)));
	}

	/**
	* Execute a HTTP DELETE request
	*
	* @return mixed
	*/
	public function delete() {
		return $this->exec($this->curlOptions + array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
	}

	/**
	* Execute a cURL request
	*
	* Throws an CurlException on any cURL errors
	*
	* @param array $options
	* @return mixed
	*/
	public function exec($options = array()) {
		$options = $this->curlOptions + $options + array(CURLOPT_HEADERFUNCTION => array($this, 'curlHeaderCallback'));

		$this->curlResource = curl_init();
		curl_setopt_array($this->curlResource, $options);

		$this->responseBody = curl_exec($this->curlResource);
		if ($this->hasError()) {
			throw new Curl\Exception($this->getError());
		}

		return $this;
	}

	/**
	* Parse headers from the current cURL request
	*
	* @param object $resURL
	* @param string $strHeader	The raw HTTP header
	* @return integer
	*/
	public function curlHeaderCallback($resURL, $header) {
		if (false !== strstr($header, ':')) {
			list($key, $value) = explode(':', $header);

			$this->responseHeadersArray[strtolower($key)] = $value;
			$this->responseHeadersArrayRaw[$key] = $value;
		}

		return strlen($header);
	}

	/**
	* Get the response content type
	*
	* @return string
	*/
	public function getResponseType() {
		return curl_getinfo($this->curlResource, CURLINFO_CONTENT_TYPE);
	}

	/**
	* Get the response code
	*
	* @return integer
	*/
	public function getResponseCode() {
		return curl_getinfo($this->curlResource, CURLINFO_HTTP_CODE);
	}

	/**
	* Get a specific response header by key
	*
	* @param string $key 	The HTTP header name
	* @param boolean $raw 	Should we check the raw response (key has to match in case)
	* @return mixed
	*/
	public function getResponseHeader($key, $raw = false) {
		$headers = $this->getResponseHeaders($raw);

		$value = null;
		if (array_key_exists($key, $this->responseHeadersArrayRaw)) {
			$value = $this->responseHeadersArrayRaw[$key];
		}

		return $value;
	}

	/**
	* Get the array with all HTTP response headers
	*
	* @param boolean $raw Should we return the raw response (key has not been normalized)
	* @return array
	*/
	public function getResponseHeaders($raw = false) {
		if ($raw) {
			$this->responseHeadersArrayRaw;
		}
		return $this->responseHeadersArray;
	}

	/**
	* Get the HTTP response body from the server
	*
	* Will try to decode data based on response content type unless raw is true
	*
	* @param boolean $raw	Return the RAW response body
	* @return mixed
	*/
	public function getResponseBody($raw = false) {
		if ($raw) {
			return $this->responseBody;
		}

		$type = $this->getResponseType();
		// Handle responses like: text/javascript; charset=UTF-8
		list($type, $encoding) = split(';', $type);
		switch($type) {
			case 'text/json':
			case 'text/javascript':
				return json_decode($this->responseBody, true);
			default:
				return $this->responseBody;
		}
	}

	/**
	* Check if the cURL object has any errors
	*
	* @return boolean
	*/
	public function hasError() {
		return '' !== $this->getError();
	}

	/**
	* Get the last cURL error
	*
	* @return string
	*/
	public function getError() {
		return curl_error($this->curlResource);
	}

	/**
	* Set option for the cURL resource
	*
	* If key is an array, its expected to be a key => value pair and will be merged with curlOptions
	*
	* @param string|array	$key
	* @param string			$value
	* @return void
	*/
	public function setOption($key, $value = null) {
		if (is_array($key)) {
			$this->curlOptions = $this->curlOptions + $key;
			return;
		}
		$this->curlOptions[$key] = $value;
	}

	public function __clone() {
		$instance = new $this(null, $this->curlOptions);
		return $instance;
	}
}