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
	$displayModifiedAt = false;
	$displayModifiedAtTime = false;
	switch ($extension) {
		case 'vfdataview' :
			if (isset($pparams['displaymodifiedat']) && $pparams['displaymodifiedat'] == 'true' && isset($form->displaymodifiedat) && !empty($form->displaymodifiedat )) {
				$displayModifiedAt = true;
			}
			if ((isset($pparams['displaycreatedtime']) && ($pparams['displaycreatedtime'] == 'true')) && (isset($form->displaycreatedtime) && (($form->displaycreatedtime == "1") || ($form->displaycreatedtime == "2") || ($form->displaycreatedtime == "3")))) {
				$displayModifiedAtTime = true;
			}
			break;
		default:
			if ($viewType == 'column' && (isset($form->displaymodifiedat)) && (($form->displaymodifiedat == "1") || ($form->displaymodifiedat == "2"))) {
				$displayModifiedAt = true;
			}
			if ($viewType == 'row' && (isset($form->displaymodifiedat)) && (($form->displaymodifiedat == "1") || ($form->displaymodifiedat == "3"))) {
				$displayModifiedAt = true;
			}
			if ($viewType == 'column' && isset($form->displaymodifiedattime) && (($form->displaymodifiedattime == "1") || ($form->displaymodifiedattime == "2"))) {
				$displayModifiedAtTime = true;
			}
			if ($viewType == 'row' && isset($form->displaymodifiedattime) && (($form->displaymodifiedattime == "1") || ($form->displaymodifiedattime == "3"))) {
				$displayModifiedAtTime = true;
			}
			break;
	}
	if (!empty($displayModifiedAt)) {
		if ($data->modified === '0000-00-00 00:00:00') {
			echo '<' . $htmlTag . $class . '></' . $htmlTag . '>';
		} else {
			$date = JFactory::getDate($data->modified, 'UTC');
			$date->setTimezone(new DateTimeZone(JFactory::getConfig()->get('offset')));
			if (!empty($displayModifiedAtTime)) {
				$formatedDate = $date->format(JText::_('DATE_FORMAT_LC4') . " H:i:s", true, false);
			} else {
				$formatedDate = $date->format(JText::_('DATE_FORMAT_LC4'), true, false);
			}
			echo '<' . $htmlTag . $class . '>' . $formatedDate . '</' . $htmlTag . '>';
		}
	}
}