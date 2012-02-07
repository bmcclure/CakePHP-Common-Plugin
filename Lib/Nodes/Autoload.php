<?php
namespace Nodes;

/**
 * Nodes autoload class
 *
 * Adds support for namespaces to autoloading
 *
 * @copyright Nodes ApS 2010-2011 <tech@nodes.dk>
 * @author Christian Winther <cw@nodes.dk>
 * @since 14.02.2011
 */
class Autoload {
	/**
	 * List of search paths
	 *
	 * @var array
	 */
	protected static $paths = array();

	/**
	 * Search for a class
	 *
	 * This is the actual callback defined in spl_autoload_register
	 *
	 * @see http://php.net/spl_autoload_register
	 * @param string $class PHP Class name to map to PHP file
	 * @return boolean
	 */
	public static function load($class) {
		// Only load namespaced classes and classes with two underscores (Zend_* and friends)
		if (false === strstr($class, '\\') && substr_count($class, '_') < 2) {
			return false;
		}

		// Scan the filesystem for the php file
		$path = static::path($class);

		// If we found the file, require it
		return $path && require_once $path;
	}

	/**
	 * Search for a php file mapping to the class name
	 *
	 * @param string $class
	 * @return boolean
	 */
	protected static function path($className) {
		// Check if class looks like a Zend_* class nameing
		if (substr_count($className, '_') > 1) {
			// Convert underscores to DIRECTORY_SEPARATOR in paths
			$filename = str_replace('_', DS, $className) . '.php';
			// Search include_paths
			$paths = explode(PATH_SEPARATOR, get_include_path());
			foreach ($paths as $path) {
				if (substr($path, -1) == DS) {
					$fullpath = $path . $filename;
				} else {
					$fullpath = $path . DS . $filename;
				}

				if (file_exists($fullpath)) {
					return $fullpath;
				}
			}
		}

		$className = ltrim($className, '\\');
		$fileName  = '';
		$namespace = '';
		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		// Search each path and check if the file exist
		foreach (self::$paths as $path) {
			$fullPath = $path . $fileName;
			if (file_exists($fullPath)) {
				return $fullPath;
			}
		}

		return false;
	}

	/**
	 * Add a new search path
	 *
	 * If $path is an array, addPath will be called for each value
	 *
	 * @param string|array $path Path to a folder
	 * @return void
	 */
	public static function addPath($path) {
		if (is_array($path)) {
			return array_map(array('static', 'addPath'), $path);
		}
		static::$paths[] = $path;
	}

	/**
	 * Register \nodes\Autoload with spl_autoload
	 *
	 * @return boolean
	 */
	public static function register() {
		return spl_autoload_register(array('static', 'load'), true);
	}

	/**
	 * Unregister \nodes\Autoload from spl_autoload
	 *
	 * @return boolean
	 */
	public static function unregister() {
		return spl_autoload_unregister(array('static', 'load'));
	}
}