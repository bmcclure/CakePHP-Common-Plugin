<?php
/**
 * Common class
 *
 * List of static helper methods
 *
 * @platform
 * @package Core.Lib
 * @copyright Nodes ApS 2010-2012 <tech@nodes.dk>
 */
class Common {
    /**
    * Constant with regular expression for validating UUID fields
    *
    * @platform
    * @var string
    */
    const VALID_UUID = '[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{12}';

    /**
     * Verify if a string is a valid UUID string
     *
     * @platform
     * @param string $str The string to validate
     * @param boolean $nullIsValid If $str is null, should it be considered valid?
     * @return boolean
     */
    public static function validUUID($str, $nullIsValid = false) {
        // Check if we got a NULL case
        if ($nullIsValid && empty($str)) {
            return true;
        }

        // Quick checks
        if (!is_string($str) || strlen($str) !== 36) {
            return false;
        }

        // Check if the string matches
        return 1 === preg_match(sprintf('#^%s$#sim', self::VALID_UUID), $str);
    }

    /**
    * Remove app absolute paths and strip them down to constant strings
    *
    * Its used to avoid information leak about the platform and hosting envoriment
    *
    * The following constants will be replaced with stringified version
    * - WEBROOT_DIR
    * - APP
    * - ROOT
    * - CAKE
    *
    * @platform
    * @param string $str
    * @return string
    */
    public static function stripRealPaths($str) {
        $str = str_replace(realpath(WEBROOT_DIR), 'WEBROOT_DIR', $str);
        $str = str_replace(realpath(APP), 'APP', $str);
        $str = str_replace(realpath(ROOT), 'ROOT', $str);
        $str = str_replace(realpath(CAKE), 'CAKE', $str);

        return $str;
    }
}