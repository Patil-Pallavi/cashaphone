<?php
/**
 * Cap Banner Module Entry Point
 * 
 * @package    Cash a Phone site
 * @subpackage Modules
 Developing_a_custom_banner_module 
 */

// No direct access
defined('_JEXEC') or die;
// Include the archive functions only once
JLoader::register('ModCapBannerHelper', __DIR__ . '/helper.php');

//$hello = ModCapBannerHelper::getBanner($params);
require JModuleHelper::getLayoutPath('mod_cap_banner');