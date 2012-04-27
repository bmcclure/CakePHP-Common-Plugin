<?php
namespace Nodes;

/**
 * Common static methods uses throughout the backend
 *
 * Adds support for namespaces to autoloading
 *
 * @copyright Nodes ApS 2010-2011 <tech@nodes.dk>
 * @author Christian Winther <cw@nodes.dk>
 * @since 14.02.2011
 */
class Common {

	public static function autoLinkText($text, $options = array()) {
		static $Html;

		if (!$Html) {
			if (!class_exists('HtmlHelper', false)) {
				\App::import('Helper', 'Html');
			}

			$Html = new \HtmlHelper();
			$Html->tags = $Html->loadConfig();
		}

		// Email
		$atom = '[a-z0-9!#$%&\'*+\/=?^_`{|}~-]';
		$text = preg_replace_callback('/(' . $atom . '+(?:\.' . $atom . '+)*@[a-z0-9-]+(?:\.[a-z0-9-]+)*)/i', function($matches) use ($Html, $options) {
			return $Html->link($matches[0], "mailto:" . $matches[0], $options);
		}, $text);

		// http / web
		$text = preg_replace_callback('#(?<!href="|">)((?:https?|ftp|nntp)://[^\s<>()]+)#i', function($matches) use ($Html, $options) {
			return $Html->link($matches[0], $matches[0], $options);
		}, $text);

		// http / web - part 2
		$text = preg_replace_callback('#(?<!href="|">)(?<!http://|https://|ftp://|nntp://)(www\.[^\n\%\ <]+[^<\n\%\,\.\ <])(?<!\))#i', function($matches) use ($Html, $options) {
			return $Html->link($matches[0], "http://" . $matches[0], $options);
		}, $text);

		return $text;
	}

	/**
	 * Verify if a string is a valid UUID string
	 *
	 * TODO Use Validation::uuuid
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

		return preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $str);
	}

	/**
	 * Try to extract a key from CakePHPs param array
	 *
	 * A named key can be in two places, in root of $params or inside $params['named']
	 *
	 * @param array $params The CakePHP params array
	 * @param string $key The key to look for
	 * @return mixed
	 */
	public static function extractNamedParam($params, $key) {
		if (isset($params['named'][$key])) {
			return $params['named'][$key];
		}

		if (isset($params[$key])) {
			return $params[$key];
		}

		return null;
	}

	/**
	 * Used primary for the admin menu, to see if Controller ($object) and Action ($property)
	 * is present in a comma-separated list - with support for wildcards
	 *
	 * @param string $object
	 * @param string $property
	 * @param string $rules
	 * @param boolean $allowed
	 * @return boolean
	 */
	public static function requestAllowed($object, $property, $rules, $allowed = false) {
		preg_match_all('/\s?(!?[^:,]+):([^,:]+)/is', $rules, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			list ( $rawMatch, $allowedObject, $allowedProperty ) = $match;

			$allowedObject = str_replace('*', '.*', $allowedObject);
			$allowedProperty = str_replace('*', '.*', $allowedProperty);

			$negativeCondition = false;
			if (substr($allowedObject, 0, 1) == '!') {
				$allowedObject = substr($allowedObject, 1);
				$negativeCondition = true;
			}

			if (preg_match('/^' . $allowedObject . '$/i', $object) && preg_match('/^' . $allowedProperty . '$/i', $property)) {
				$allowed = !$negativeCondition;
			}
		}
		return $allowed;
	}

	/**
	* Remove app absolute paths and strip them down to constant strings
	*
	* Its used to avoid information leak about the platform and hosting envoriment
	*
	* The following constants will be replaced with stringified version
	* - WWW_ROOT
	* - CAKE
	* - APP
	* - ROOT
	* - WEBROOT_DIR
	*
	* @platform
	* @param string $str
	* @param
	* @return string
	*/
	public static function stripRealPaths($str) {
		$str = str_replace(WWW_ROOT, 'WWW_ROOT/', $str);
		$str = str_replace(CAKE, 'CAKE/', $str);
		$str = str_replace(APP, 'APP/', $str);
		$str = str_replace(ROOT, 'ROOT/', $str);
		$str = str_replace(realpath(WEBROOT_DIR), 'WEBROOT_DIR/', $str);

		return $str;
	}

	/**
	 * Check if a string can be evaluated to boolean
	 *
	 * @param mixed $str
	 * @param array $additionalTrueValues List of additional values that should evaluate to true
	 * @param boolean $default Default boolean return value
	 * @return boolean TRUE if the $str exists in $trueList
	 */
	public static function evaluateBoolean($str, $additionalTrueValues = array(), $default = false) {
		$trueList = array(true, 1, '1', 'y', 'yes', 'true', 'ja', 'on');

		// Merge additional true values if needed
		if (is_array($additionalTrueValues) && !empty($additionalTrueValues)) {
			$trueList = array_merge($trueList, $additionalTrueValues);
		}

		// Check if $str can be evaluated to true
		if (false !== array_search($str, $trueList, true)) {
			return true;
		}

		// Or return default value
		return $default;
	}

	/**
	 * Clean a string so its suitable for slugs / URLs in the browser
	 *
	 * Removes special chars and maps high-level utf8 chars to their more simple
	 * version - like the danish 'å' to 'aa'
	 *
	 * @param string $url
	 * @return string
	 */
	public static function cleanUrl($url) {
		if ('' == $url) {
			return $url;
		}

		if (!self::seemsUtf8($url)) {
			$url = mb_convert_encoding($url, 'UTF-8');
		}

		$url = trim($url);
		$url = strtolower($url);

		$search = array(chr(230), chr(248), chr(229));

		foreach ($search as $b) {
			$url = str_replace(utf8_encode($b), urlencode(utf8_encode($b)), $url);
		}

		$url = self::remove_accents($url);
		$url = preg_replace('|[^%a-z0-9-~+_;,/\(\)]|iu', '-', $url);
		$url = str_replace(';//', '://', $url);
		$url = preg_replace('#[-]{2,}#', '-', $url);
		$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
		$url = str_replace('/', '', $url);
		return $url;
	}

	/**
	* Clean a filename.
	* Replaces æøå (Danish chars) to ae, oe and aa
	* @param string $filename
	*
	* @return string
	*/
	public static function cleanFilename($filename) {
		$replacement = array(',' => '-', 'æ' => 'ae', 'ø' => 'oe', 'å' => 'aa', 'Æ' => 'AE', 'ø' => 'OE', 'Å' => 'AA', '?' => '-');
		return str_replace(array_keys($replacement), array_values($replacement), $filename);
	}

	/**
	 * array arrayDeepMerge ( array array1 [, array array2 [, array ...]] )
	 *
	 * Like array_merge
	 *
	 *	arrayDeepMerge() merges the elements of one or more arrays together so
	 * that the values of one are appended to the end of the previous one. It
	 * returns the resulting array.
	 *	If the input arrays have the same string keys, then the later value for
	 * that key will overwrite the previous one. If, however, the arrays contain
	 * numeric keys, the later value will not overwrite the original value, but
	 * will be appended.
	 *	If only one array is given and the array is numerically indexed, the keys
	 * get reindexed in a continuous way.
	 *
	 * Different from array_merge
	 *	If string keys have arrays for values, these arrays will merge recursively.
	 */
	public static function arrayDeepMerge() {
		switch (func_num_args()) {
			case 0 :
				return false;
			case 1 :
				return func_get_arg(0);
			case 2 :
				$newArrayKeyrgs = func_get_args();
				$newArrayKeyrgs[2] = array();
				if (is_array($newArrayKeyrgs[0]) && is_array($newArrayKeyrgs[1])) {
					foreach (array_unique(array_merge(array_keys($newArrayKeyrgs[0]), array_keys($newArrayKeyrgs[1]))) as $key) {
						$isKey0 = array_key_exists($key, $newArrayKeyrgs[0]);
						$isKey1 = array_key_exists($key, $newArrayKeyrgs[1]);
						if ($isKey0 && $isKey1 && is_array($newArrayKeyrgs[0][$key]) && is_array($newArrayKeyrgs[1][$key])) {
							$newArrayKeyrgs[2][$key] = self::arrayDeepMerge($newArrayKeyrgs[0][$key], $newArrayKeyrgs[1][$key]);
						}
						else
							if ($isKey0 && $isKey1) {
								$newArrayKeyrgs[2][$key] = $newArrayKeyrgs[1][$key];
							}
							else
								if (!$isKey1) {
									$newArrayKeyrgs[2][$key] = $newArrayKeyrgs[0][$key];
								}
								else
									if (!$isKey0) {
										$newArrayKeyrgs[2][$key] = $newArrayKeyrgs[1][$key];
									}
					}
					return $newArrayKeyrgs[2];
				}
				else {
					return $newArrayKeyrgs[1];
				}
			default :
				$newArrayKeyrgs = func_get_args();
				$newArrayKeyrgs[1] = self::arrayDeepMerge($newArrayKeyrgs[0], $newArrayKeyrgs[1]);
				array_shift($newArrayKeyrgs);
				return self::arrayDeepMerge($newArrayKeyrgs);
				break;
		}
	}

	/**
	 * Check if a string looks like an UTF8 string
	 *
	 * @param string $str
	 * @return boolean true if the string is in utf8 encoding
	 */
	public static function seemsUtf8($Str) { # by bmorel at ssi dot fr
		for($i = 0; $i < strlen($Str); $i++) {
			if (ord($Str[$i]) < 0x80)
				continue;
			elseif ((ord($Str[$i]) & 0xE0) == 0xC0)
				$n = 1;
			elseif ((ord($Str[$i]) & 0xF0) == 0xE0)
				$n = 2;
			elseif ((ord($Str[$i]) & 0xF8) == 0xF0)
				$n = 3;
			elseif ((ord($Str[$i]) & 0xFC) == 0xF8)
				$n = 4;
			elseif ((ord($Str[$i]) & 0xFE) == 0xFC)
				$n = 5;
			else
				return false;
			for($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++ $i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}

	/**
	 * Helper method for \cleanUrl method
	 *
	 * Maps all high-level utf8 chars to their simple ASCII part
	 *
	 * @param string $string
	 * @return string
	 */
	protected static function remove_accents($string) {
		if (!preg_match('/[\x80-\xff]/', $string))
			return $string;

		if (self::seemsUtf8($string)) {
			$chars = array(
				// Decompositions for Latin-1 Supplement
				chr(195) . chr(128) => 'A',
				chr(195) . chr(129) => 'A',
				chr(195) . chr(130) => 'A',
				chr(195) . chr(131) => 'A',
				chr(195) . chr(132) => 'A',
				chr(195) . chr(133) => 'A',
				chr(195) . chr(135) => 'C',
				chr(195) . chr(136) => 'E',
				chr(195) . chr(137) => 'E',
				chr(195) . chr(138) => 'E',
				chr(195) . chr(139) => 'E',
				chr(195) . chr(140) => 'I',
				chr(195) . chr(141) => 'I',
				chr(195) . chr(142) => 'I',
				chr(195) . chr(143) => 'I',
				chr(195) . chr(145) => 'N',
				chr(195) . chr(146) => 'O',
				chr(195) . chr(147) => 'O',
				chr(195) . chr(148) => 'O',
				chr(195) . chr(149) => 'O',
				chr(195) . chr(150) => 'O',
				chr(195) . chr(153) => 'U',
				chr(195) . chr(154) => 'U',
				chr(195) . chr(155) => 'U',
				chr(195) . chr(156) => 'U',
				chr(195) . chr(157) => 'Y',
				chr(195) . chr(159) => 's',
				chr(195) . chr(160) => 'a',
				chr(195) . chr(161) => 'a',
				chr(195) . chr(162) => 'a',
				chr(195) . chr(163) => 'a',
				chr(195) . chr(164) => 'a',
				chr(195) . chr(165) => 'a',
				chr(195) . chr(167) => 'c',
				chr(195) . chr(168) => 'e',
				chr(195) . chr(169) => 'e',
				chr(195) . chr(170) => 'e',
				chr(195) . chr(171) => 'e',
				chr(195) . chr(172) => 'i',
				chr(195) . chr(173) => 'i',
				chr(195) . chr(174) => 'i',
				chr(195) . chr(175) => 'i',
				chr(195) . chr(177) => 'n',
				chr(195) . chr(178) => 'o',
				chr(195) . chr(179) => 'o',
				chr(195) . chr(180) => 'o',
				chr(195) . chr(181) => 'o',
				chr(195) . chr(182) => 'o',
				chr(195) . chr(182) => 'o',
				chr(195) . chr(185) => 'u',
				chr(195) . chr(186) => 'u',
				chr(195) . chr(187) => 'u',
				chr(195) . chr(188) => 'u',
				chr(195) . chr(189) => 'y',
				chr(195) . chr(191) => 'y',
				// Decompositions for Latin Extended-A
				chr(196) . chr(128) => 'A',
				chr(196) . chr(129) => 'a',
				chr(196) . chr(130) => 'A',
				chr(196) . chr(131) => 'a',
				chr(196) . chr(132) => 'A',
				chr(196) . chr(133) => 'a',
				chr(196) . chr(134) => 'C',
				chr(196) . chr(135) => 'c',
				chr(196) . chr(136) => 'C',
				chr(196) . chr(137) => 'c',
				chr(196) . chr(138) => 'C',
				chr(196) . chr(139) => 'c',
				chr(196) . chr(140) => 'C',
				chr(196) . chr(141) => 'c',
				chr(196) . chr(142) => 'D',
				chr(196) . chr(143) => 'd',
				chr(196) . chr(144) => 'D',
				chr(196) . chr(145) => 'd',
				chr(196) . chr(146) => 'E',
				chr(196) . chr(147) => 'e',
				chr(196) . chr(148) => 'E',
				chr(196) . chr(149) => 'e',
				chr(196) . chr(150) => 'E',
				chr(196) . chr(151) => 'e',
				chr(196) . chr(152) => 'E',
				chr(196) . chr(153) => 'e',
				chr(196) . chr(154) => 'E',
				chr(196) . chr(155) => 'e',
				chr(196) . chr(156) => 'G',
				chr(196) . chr(157) => 'g',
				chr(196) . chr(158) => 'G',
				chr(196) . chr(159) => 'g',
				chr(196) . chr(160) => 'G',
				chr(196) . chr(161) => 'g',
				chr(196) . chr(162) => 'G',
				chr(196) . chr(163) => 'g',
				chr(196) . chr(164) => 'H',
				chr(196) . chr(165) => 'h',
				chr(196) . chr(166) => 'H',
				chr(196) . chr(167) => 'h',
				chr(196) . chr(168) => 'I',
				chr(196) . chr(169) => 'i',
				chr(196) . chr(170) => 'I',
				chr(196) . chr(171) => 'i',
				chr(196) . chr(172) => 'I',
				chr(196) . chr(173) => 'i',
				chr(196) . chr(174) => 'I',
				chr(196) . chr(175) => 'i',
				chr(196) . chr(176) => 'I',
				chr(196) . chr(177) => 'i',
				chr(196) . chr(178) => 'IJ',
				chr(196) . chr(179) => 'ij',
				chr(196) . chr(180) => 'J',
				chr(196) . chr(181) => 'j',
				chr(196) . chr(182) => 'K',
				chr(196) . chr(183) => 'k',
				chr(196) . chr(184) => 'k',
				chr(196) . chr(185) => 'L',
				chr(196) . chr(186) => 'l',
				chr(196) . chr(187) => 'L',
				chr(196) . chr(188) => 'l',
				chr(196) . chr(189) => 'L',
				chr(196) . chr(190) => 'l',
				chr(196) . chr(191) => 'L',
				chr(197) . chr(128) => 'l',
				chr(197) . chr(129) => 'L',
				chr(197) . chr(130) => 'l',
				chr(197) . chr(131) => 'N',
				chr(197) . chr(132) => 'n',
				chr(197) . chr(133) => 'N',
				chr(197) . chr(134) => 'n',
				chr(197) . chr(135) => 'N',
				chr(197) . chr(136) => 'n',
				chr(197) . chr(137) => 'N',
				chr(197) . chr(138) => 'n',
				chr(197) . chr(139) => 'N',
				chr(197) . chr(140) => 'O',
				chr(197) . chr(141) => 'o',
				chr(197) . chr(142) => 'O',
				chr(197) . chr(143) => 'o',
				chr(197) . chr(144) => 'O',
				chr(197) . chr(145) => 'o',
				chr(197) . chr(146) => 'OE',
				chr(197) . chr(147) => 'oe',
				chr(197) . chr(148) => 'R',
				chr(197) . chr(149) => 'r',
				chr(197) . chr(150) => 'R',
				chr(197) . chr(151) => 'r',
				chr(197) . chr(152) => 'R',
				chr(197) . chr(153) => 'r',
				chr(197) . chr(154) => 'S',
				chr(197) . chr(155) => 's',
				chr(197) . chr(156) => 'S',
				chr(197) . chr(157) => 's',
				chr(197) . chr(158) => 'S',
				chr(197) . chr(159) => 's',
				chr(197) . chr(160) => 'S',
				chr(197) . chr(161) => 's',
				chr(197) . chr(162) => 'T',
				chr(197) . chr(163) => 't',
				chr(197) . chr(164) => 'T',
				chr(197) . chr(165) => 't',
				chr(197) . chr(166) => 'T',
				chr(197) . chr(167) => 't',
				chr(197) . chr(168) => 'U',
				chr(197) . chr(169) => 'u',
				chr(197) . chr(170) => 'U',
				chr(197) . chr(171) => 'u',
				chr(197) . chr(172) => 'U',
				chr(197) . chr(173) => 'u',
				chr(197) . chr(174) => 'U',
				chr(197) . chr(175) => 'u',
				chr(197) . chr(176) => 'U',
				chr(197) . chr(177) => 'u',
				chr(197) . chr(178) => 'U',
				chr(197) . chr(179) => 'u',
				chr(197) . chr(180) => 'W',
				chr(197) . chr(181) => 'w',
				chr(197) . chr(182) => 'Y',
				chr(197) . chr(183) => 'y',
				chr(197) . chr(184) => 'Y',
				chr(197) . chr(185) => 'Z',
				chr(197) . chr(186) => 'z',
				chr(197) . chr(187) => 'Z',
				chr(197) . chr(188) => 'z',
				chr(197) . chr(189) => 'Z',
				chr(197) . chr(190) => 'z',
				chr(197) . chr(191) => 's',
				// Euro Sign
				chr(226) . chr(130) . chr(172) => 'E');

			$string = strtr($string, $chars);
		}
		else {
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158) . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194) . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202) . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210) . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218) . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227) . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235) . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243) . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251) . chr(252) . chr(253) . chr(255);

			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars['in'] = array(
				chr(140),
				chr(156),
				chr(198),
				chr(208),
				chr(222),
				chr(223),
				chr(230),
				chr(240),
				chr(254));
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		}

		return $string;
	}
}
