<?php
App::uses('Nodes\Common', 'Common.Nodes');

class CommonTest extends CakeTestCase {

	public function testCleanFilename() {
		$input		= "This, ladies and gentlemen, is a filename";
		$result		= \Nodes\Common::cleanFilename($input);
		$expected	= "This- ladies and gentlemen- is a filename";

		$this->assertSame($expected, $result);
	}

	public function testStripRealPaths() {
		$path		= WWW_ROOT;
		$result		= \Nodes\Common::stripRealPaths($path);
		$expected	= 'WWW_ROOT' . DS;
		$this->assertSame($expected, $result);

		$path		= CAKE;
		$result		= \Nodes\Common::stripRealPaths($path);
		$expected	= 'CAKE' . DS;
		$this->assertSame($expected, $result);

		$path		= APP;
		$result		= \Nodes\Common::stripRealPaths($path);
		$expected	= 'APP' . DS;
		$this->assertSame($expected, $result);

		$path		= ROOT;
		$result		= \Nodes\Common::stripRealPaths($path);
		$expected	= 'ROOT' . DS;
		$this->assertSame($expected, $result);
	}

	/**
	 * EvaluateBoolean - default values
	 *
	 * @return void
	 */
	public function testEvaluateBooleanNormal() {
		$test_values = array(true, 1, '1', 'y', 'yes', 'true', 'ja', 'on');
		foreach ($test_values as $value) {
			$result		= \Nodes\Common::evaluateBoolean($value);
			$this->assertTrue($result, var_export($value, true));
		}

		$result		= \Nodes\Common::evaluateBoolean('notBoolean');
		$this->assertFalse($result, var_export('notBoolean', true));
	}

	/**
	 * EvaluateBoolean - additional
	 *
	 * @return void
	 */
	public function testEvaluateBooleanNormalPlusAdditional() {
		$test_values = array(true, 1, '1', 'y', 'yes', 'true', 'ja', 'on', 'yesyes');
		foreach ($test_values as $value) {
			$result		= \Nodes\Common::evaluateBoolean($value, array('yesyes'));
			$this->assertTrue($result, var_export($value, true));
		}
	}

	/**
	 * EvaluateBoolean - additional negative
	 *
	 * @return void
	 */
	public function testEvaluateBooleanNormalPlusAdditionalNegative() {
		$result		= \Nodes\Common::evaluateBoolean('notBoolean2', array('notBoolean'));
		$this->assertFalse($result, var_export('notBoolean', true));
	}

	/**
	 * EvaluateBoolean - inverted
	 *
	 * @return void
	 */
	public function testEvaluateBooleanInverted() {
		$test_values = array(false, 0, '0', 'n', 'no', 'false', 'nej', 'off');
		foreach ($test_values as $value) {
			$result		= \Nodes\Common::evaluateBoolean($value, array(), true);
			$this->assertTrue($result, var_export($value, true));
		}

		$result		= \Nodes\Common::evaluateBoolean('notBoolean', array(), true);
		$this->assertTrue($result, var_export('notBoolean', true));
	}

	/**
	 * EvaluateBoolean - additional + inverted
	 *
	 * @return void
	 */
	public function testEvaluateBooleanNormalPlusAdditionalInverted() {
		$test_values = array(false, 0, '0', 'n', 'no', 'false', 'nej', 'off', 'nono');
		foreach ($test_values as $value) {
			$result		= \Nodes\Common::evaluateBoolean($value, array('nono'), true);
			$this->assertTrue($result, var_export($value, true));
		}

		$result		= \Nodes\Common::evaluateBoolean('notBoolean', array('notBoolean'));
		$this->assertTrue($result, var_export('notBoolean', true));
	}

	/**
	 * testEvaluateBooleanNull - null test
	 *
	 * @return void
	 */
	public function testEvaluateBooleanNull() {
		$result		= \Nodes\Common::evaluateBoolean(null);
		$this->assertFalse($result, var_export(null, true));
	}
}
