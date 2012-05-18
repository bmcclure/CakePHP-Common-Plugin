<?php
App::uses('Validation', 'Utility');

class SslProxyController extends CommonAppController {

	public $uses = array();

	public function view() {
		if (empty($this->request->query['link'])) {
			throw new NotFoundException('Missing link parameter');
		}

		$link = rawurldecode($this->request->query['link']);

		if (!Validation::url($link)) {
			throw new NotFoundException('Invalid link');
		}

		$request = new Nodes\Curl($link);
		$request->exec();

		$responseCode = $request->getResponseCode();
		if ($responseCode != 200) {
			throw new NotFoundException('Response code was not 200 OK');
		}

		$responseHeaders = $request->getResponseHeaders();
		$this->response->disableCache();
		$this->response->type($request->getResponseHeader('content-type'));
		$this->response->body($request->getResponseBody());
		$this->response->send();
		$this->_end();
	}
}
