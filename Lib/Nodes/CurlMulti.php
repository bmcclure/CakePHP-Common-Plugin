<?php
namespace Nodes;

/**
* cURL Multi class
*
* Takes a list of \Nodes\Curl objects and executes them in parallel
*
* @platform
* @package Core.Lib
* @copyright Nodes ApS 2010-2012 <tech@nodes.dk>
*/
class CurlMulti {

	protected $objects;

	public function __construct($objects) {
		if (!is_array($objects)) {
			throw new Curl\Exception('Objects must be an array');
		}

		foreach ($objects as $index => $object) {
			if (!($object instanceof Curl)) {
				throw new Curl\Exception(sprintf('Object at index %d is not a \Nodes\Curl instance', $index));
			}
		}

		reset($objects);
		$this->objects = $objects;
	}

	public function execute() {
		$curl_multi_handle = curl_multi_init();
		foreach ($this->objects as $object) {
			curl_multi_add_handle($curl_multi_handle, $object->createCurlResource()->getCurlResource());
		}

		// Execute the handles
		$running = null;
		do {
			curl_multi_exec($curl_multi_handle, $running);
		} while($running > 0);

		// Iterate the handles
		foreach ($this->objects as $object) {
			$object->setResponseBody(curl_multi_getcontent($object->getCurlResource()));
			curl_multi_remove_handle($curl_multi_handle, $object->getCurlResource());
		}

		curl_multi_close($curl_multi_handle);
	}
}