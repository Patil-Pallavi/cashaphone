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
if (!empty($displayData) && isset($displayData['text'])) {
	$text = $displayData['text'];
	$extension = (!empty($displayData['extension'])) ? $displayData['extension'] : 'component';
	$viewType = (!empty($displayData['viewType'])) ? $displayData['viewType'] : 'row';
	$htmlTag = (!empty($displayData['htmlTag'])) ? $displayData['htmlTag'] : 'td';
	$class = (!empty($displayData['class'])) ? ' class="' . $displayData['class'] . '"' : '';
	$pparams = (!empty($displayData['pparams'])) ? $displayData['pparams'] : array();
	if ($viewType === 'column') {
		switch ($extension) {
			case 'vfdataview' :
				if (isset($pparams['maxtextlength']) && !empty($pparams['maxtextlength']) && (strlen($text) > $pparams['maxtextlength'])) {
					$text = substr($text,0,$pparams['maxtextlength'])."...";
				}
				break;
			default:
				if (strlen($text) > 255) {
					$text = substr($text, 0, 255) . "...";
				}
				break;
		}
	}
	echo '<' . $htmlTag . $class . '>' . $text . '</' . $htmlTag . '>';
}