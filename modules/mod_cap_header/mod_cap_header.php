<?php
/**
 * Cap Header Module Entry Point
 * 
 * @package    Cash a Phone site
 * @subpackage Modules
 Developing_a_custom_header_Module 
 */

// No direct access
defined('_JEXEC') or die;
// Include the archive functions only once
JLoader::register('ModCapHeaderHelper', __DIR__ . '/helper.php');

$hello = ModCapHeaderHelper::getHeader($params);
require JModuleHelper::getLayoutPath('mod_cap_header');