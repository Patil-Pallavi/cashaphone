<?php

/**
 * Visform field Selectfromdb
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 */

// no direct access
defined('JPATH_PLATFORM') or die;

require_once(JPATH_ADMINISTRATOR . '/components/com_visforms/helpers/html/visforms.php');

JFormHelper::loadFieldClass('list');


class JFormFieldSelectfromdb extends JFormFieldList
{

	public $type = 'Selectfromdb';

	protected function getInput() {
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= $this->disabled ? ' disabled' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
		$textfieldname = "a.title";
		$where = '';
		$order = '';
		$valueprefix = '';
		$textprefix = '';
		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
		if (!(empty($this->element['table']))) {
			$table = $this->getAttribute('table');
			unset($this->element['table']);
		}
		if (!(empty($this->element['textfieldname']))) {
			$textfieldname = $this->getAttribute('textfieldname');
			unset($this->element['textfieldname']);
		}
		if (!(empty($this->element['where']))) {
			$where = $this->getAttribute('where');
			unset($this->element['where']);
		}
		if (!(empty($this->element['order']))) {
			$order = $this->getAttribute('order');
			unset($this->element['order']);
		}
		if (!(empty($this->element['textprefix']))) {
			$textprefix = $this->getAttribute('textprefix');
			unset($this->element['textprefix']);
		}
		if (!(empty($this->element['valueprefix']))) {
			$valueprefix = $this->getAttribute('valueprefix');
			unset($this->element['valueprefix']);
		}
		// Get the field options.
		$options = $this->getOptions();
		return JHtml::_('visforms.createSelectFromDb', $table, $this->name, $this->value, $attr, $options, $this->id, $textfieldname, $where, $order, $textprefix, $valueprefix);
	}
}
