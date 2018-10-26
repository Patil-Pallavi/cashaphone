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
//displaydata: form, data, extension, htmltag, class, pparams
if (!empty($displayData) && isset($displayData['form']) && isset($displayData['data']) ) {
	$form = $displayData['form'];
	$data = $displayData['data'];
	$extension = (!empty($displayData['extension'])) ? $displayData['extension'] : 'component';
	$htmlTag = (!empty($displayData['htmlTag'])) ? $displayData['htmlTag'] : 'td';
	$class = (!empty($displayData['class'])) ? ' class="'.$displayData['class'] .'"' : '';
	$pparams = (!empty($displayData['pparams'])) ? $displayData['pparams'] : array();
	$viewType = (!empty($displayData['viewType'])) ? $displayData['viewType'] : 'column';
	$displayCreated = false;
	$displayCreatedTime = false;
	switch ($extension) {
		case 'vfdataview' :
			if (isset($pparams['displaycreated']) && $pparams['displaycreated'] == 'true' && isset($form->displaycreated) && !empty($form->displaycreated )) {
				$displayCreated = true;
			}
			if ((isset($pparams['displaycreatedtime']) && ($pparams['displaycreatedtime'] == 'true')) && (isset($form->displaycreatedtime) && (($form->displaycreatedtime == "1") || ($form->displaycreatedtime == "2") || ($form->displaycreatedtime == "3")))) {
				$displayCreatedTime = true;
			}
			break;
		default:
			if ($viewType == 'column' && (isset($form->displaycreated)) && (($form->displaycreated == "1") || ($form->displaycreated == "2"))) {
				$displayCreated = true;
			}
			if ($viewType == 'row' && (isset($form->displaycreated)) && (($form->displaycreated == "1") || ($form->displaycreated == "3"))) {
				$displayCreated = true;
			}
			if ($viewType == 'column' && isset($form->displaycreatedtime) && (($form->displaycreatedtime == "1") || ($form->displaycreatedtime == "2"))) {
				$displayCreatedTime = true;
			}
			if ($viewType == 'row' && isset($form->displaycreatedtime) && (($form->displaycreatedtime == "1") || ($form->displaycreatedtime == "3"))) {
				$displayCreatedTime = true;
			}
			break;
	}
	if (!empty($displayCreated)) {
		$date = JFactory::getDate($data->created, 'UTC');
		$date->setTimezone(new DateTimeZone(JFactory::getConfig()->get('offset')));
		if (!empty($displayCreatedTime)) {
			$formatedDate = $date->format(JText::_('DATE_FORMAT_LC4') . " H:i:s", true, false);
		} else {
			$formatedDate = $date->format(JText::_('DATE_FORMAT_LC4'), true, false);
		}
		echo '<' . $htmlTag . $class . '>' . $formatedDate . '</' . $htmlTag . '>';
	}
}