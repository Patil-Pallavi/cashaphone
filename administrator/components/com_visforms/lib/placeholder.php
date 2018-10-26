<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class VisformsPlaceholder {

	protected $param;
	protected $rawData;
	protected $field;
	protected static $customParams = array();
	public static $nonFieldPlaceholder = array(
		'formtitle' => 'COM_VISFORMS_PLACEHOLDER_FORM_TITLE',
		'id' => 'COM_VISFORMS_ID',
		'created' => 'COM_VISFORMS_FIELD_CREATED_LABEL',
		'modified' => 'COM_VISFORMS_MODIFIED_AT',
		'ipaddress' => 'COM_VISFORMS_IP'
	);

	public function __construct($param, $rawData, $field) {
		$this->param = $param;
		$this->rawData = $rawData;
		$this->field = $field;
	}

	public static function getInstance ($pParam = '', $rawData, $pType = 'Default', $field = null) {
		$className = 'VisformsPlaceholder' . ucfirst($pType);
		if (!class_exists($className)) {
			// Try to load specific placeholder class
			JLoader::register($className, JPATH_ADMINISTRATOR . '/components/com_visforms/lib/placeholder/' . strtolower($pType . '.php'));
		}
		if (!class_exists($className)) {
			$className = 'VisformsPlaceholderDefault';
			// Fall back to default class
			JLoader::register($className, JPATH_ADMINISTRATOR . '/components/com_visforms/lib/placeholder/default.php');
		}
		if (class_exists($className)) {
			return new $className($pParam, $rawData, $field);
		}
		return false;
	}

	// returns an array of strings that can be added as params to the placeholder
	public static function getParamStringsArrayForType($pType) {
		$var = 'customParams';
		$className = 'VisformsPlaceholder' . ucfirst($pType);
		if (!class_exists($className)) {
			// Try to load specific placeholder class
			JLoader::register($className, JPATH_ADMINISTRATOR . '/components/com_visforms/lib/placeholder/' . strtolower($pType . '.php'));
		}
		if (!class_exists($className)) {
			$className = 'VisformsPlaceholderDefault';
			// Fall back to default class
			JLoader::register($className, JPATH_ADMINISTRATOR . '/components/com_visforms/lib/placeholder/default.php');
		}
		if (class_exists($className)) {
			$vars = get_class_vars($className);
			if (!empty($vars) && is_array($vars) && array_key_exists($var, $vars)) {
				$customParams =  $className::$$var;
				foreach ($customParams as $key => $description) {
					$customParams[$key] = JText::_($description);
				}
				return $customParams;
			}
		}
		// no special params for this type
		return self::$$var;
	}

	// returns an array of all placeholders as full string [context:mane|PARAM]
	public static function getAllPlaceholderFullString($text, $context = '') {
		if (empty($context)) {
			$pattern = '/(?:\$\{|\[)(?:[a-zA-Z0-9\-_][a-zA-Z0-9\-_]*:)?[a-zA-Z0-9]{1}[a-zA-Z0-9\-_]*(?:\|[A-Z]*)?(?:\}|\])/';
		}
		else {
			$pattern = '/(?:\$\{|\[)(?:'.$context.':)?[a-zA-Z0-9]{1}[a-zA-Z0-9\-_]*(?:\|[A-Z]*)?(?:\}|\])/';
		}
		if (preg_match_all($pattern, $text, $matches)) {
			if (!empty($matches)) {
				return $matches[0];
			}
		}
		return false;
	}

	// returns an array of all placeholders as associative array with up to 4 parts (original placeholder, context, name, param)
	public static function getAllPlaceholderPartsArray($text, $context = '') {
		$matches = self::getAllPlaceholderFullString($text, $context);
		$list = array();
		if (!empty($matches)) {
			foreach ($matches as $match) {
				$list[] = self::getPlaceholderParts($match);
			}
		}
		return $list;
	}

	public static function getPlaceholderName ($placeholder) {
		$parts = self::getPlaceholderParts($placeholder);
		if (is_array($parts) && isset($parts['name'])) {
			return $parts['name'];
		}
		else {
			return '';
		}
	}

	public static function getPlaceholderParam ($placeholder) {
		$parts = self::getPlaceholderParts($placeholder);
		if (is_array($parts) && isset($parts['param'])) {
			return $parts['param'];
		}
		else {
			return '';
		}
	}

	public static function getPlaceholderContext ($placeholder) {
		$parts = self::getPlaceholderParts($placeholder);
		if (is_array($parts) && isset($parts['context'])) {
			return $parts['context'];
		}
		else {
			return '';
		}
	}

	// $placeholder: placeholderstring ${context:name|PARAM}, returns associative array with up to 4 parts (original placeholder, context, name, param)
	public static function getPlaceholderParts($placeholder) {
		if (empty($placeholder)) {
			return array();
		}
		else {
			$parts = array();
			$parts['placeholder'] = $placeholder;
			$oldFormat = (strpos($placeholder, ']') !== false ) ? true : false;
			$firstSplit = explode('|', trim(trim($placeholder, '$'), '{}\[]'));
			if (count($firstSplit) === 2) {
				$parts['param'] = array_pop($firstSplit);
			}
			if (!empty($firstSplit[0])) {
				$secondSplit = explode(':', $firstSplit[0]);
				if (count($secondSplit) === 1)  {
					$parts['name'] = ($oldFormat) ? strtolower($secondSplit[0]) : $secondSplit[0];
				}
				else {
					$parts['context'] = $secondSplit[0];
					$parts['name'] = $secondSplit[1];
				}
			}

			return $parts;
		}
	}

	protected function extractParams() {
		$params = array();
		if (!empty($this->param)) {
			$str = trim($this->param, '\{}');
		}
		return $params;
	}

	abstract public function getReplaceValue();
}