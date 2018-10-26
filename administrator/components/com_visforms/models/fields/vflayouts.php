<?php
/**
 * Visform field typefield
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');
JLoader::register('VisformsAEF', JPATH_ADMINISTRATOR . '/components/com_visforms/helpers/aef/aef.php');

/**
 * Form Field class for Visforms.
 * Supports list field types.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldVflayouts extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Vflayouts';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
		
		// Get the field options.
		$options = (array)$this->getOptions();
		$hasBt3 = VisformsAEF::checkAEF(VisformsAEF::$bootStrap3Layouts);
		if (!empty($hasBt3))
		{
			$bt3default = new StdClass();
			$bt3default->value = 'bt3default';
			$bt3default->text = JText::_('COM_VISFORMS_LAYOUT_BOOTSTRAP3_DEFAULT');
			$bt3default->disabled = false || ($this->readonly && 'bt3default' != $this->value);
			$bt3default->checked = false;
			$bt3default->selected = false;
			$options[] = $bt3default;

			$bt3horizontal = new StdClass();
			$bt3horizontal->value = 'bt3horizontal';
			$bt3horizontal->text = JText::_('COM_VISFORMS_LAYOUT_BOOTSTRAP3_HORIZONTAL');
			$bt3horizontal->disabled = false || ($this->readonly && 'bt3horizontal' != $this->value);
			$bt3horizontal->checked = false;
			$bt3horizontal->selected = false;
			$options[] = $bt3horizontal;

			$bt3mcindividual = new StdClass();
			$bt3mcindividual->value = 'bt3mcindividual';
			$bt3mcindividual->text = JText::_('COM_VISFORMS_BOOTSTRAP3_MULTICOLUMN_INDIVIDUAL');
			$bt3mcindividual->disabled = false || ($this->readonly && 'bt3mcindividual' != $this->value);
			$bt3mcindividual->checked = false;
			$bt3mcindividual->selected = false;
			$options[] = $bt3mcindividual;
		}

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		else
		// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		return parent::getOptions();
	}
}
