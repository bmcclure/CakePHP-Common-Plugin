<?php
namespace Nodes;

/**
 * Javascript class for outputting variables to html / json
 *
 * @copyright Nodes ApS 2010-2011 <tech@nodes.dk>
 * @author Christian Winther <cw@nodes.dk>
 * @since 09.03.2011
 */
class Javascript {

/**
 * Add a translation key
 *
 * Will be accessible by Nodes.translate($key) in JS
 *
 * @param string $key
 * @param mixed $value
 * @return void
 */
	public static function translate($key, $value) {
		static::write(sprintf('L10n.%s', $key), $value);
	}

/**
 * Write a configuration key that will be published to
 *
 * Nodes.Configuration in frontend
 *
 * @param string $path
 * @param mixed $value
 * @return void
 */
	public static function write($path, $value) {
		\Configure::write('Javascript.' . $path, $value);
	}

/**
 * Get configuration object
 *
 * Make sure to execute any callable values in the array
 * before returning it
 *
 * @return array
 */
	public static function getConfiguration() {
		$all = \Configure::read('Javascript');

		return array_map('static::_executeCallbacks', $all);
	}

/**
 * Make sure to execute callbacks recursively
 *
 * @param mixed
 * @return mixed
 */
	protected static function _executeCallbacks($value) {
		if (is_callable($value)) {
			return call_user_func($value);
		}

		if (is_array($value)) {
			return array_map('static::_executeCallbacks', $value);
		}

		return $value;
	}
}