<?php
App::uses('Nodes\Common', 'Common.Nodes');

class CommonTest extends CakeTestCase {

	public function testCleanFilename() {
		$input = "This, ladies and gentlemen, is a filename";
		$result = \Nodes\Common::cleanFilename($input);
		$expected = "This- ladies and gentlemen- is a filename";

		$this->assertSame($expected, $result);
	}

}
