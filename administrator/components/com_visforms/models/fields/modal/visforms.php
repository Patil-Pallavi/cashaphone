<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldModal_Visforms extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Visforms';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectVisforms_'.$this->id.'(id, title, object) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '		document.getElementById("'.$this->id.'_name").value = title;';
		$script[] = '		jQuery("#modalVisform' . $this->id . '").modal("hide");';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_visforms&amp;view=visforms&amp;layout=modal&amp;tmpl=component&amp;function=jSelectVisforms_'.$this->id;

		$db	= JFactory::getDbo();
		$db->setQuery(
			'SELECT title' .
			' FROM #__visforms' .
			' WHERE id = '.(int) $this->value
		);
        
        try
		{
			$title = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		if (empty($title)) {
			$title = JText::_('COM_VISFORMS_CHOOSE_FORM');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The active form id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}
        
        // The current article display field.
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
		$html[] = '<a href="#modalVisform' . $this->id . '" class="btn hasTooltip" role="button"  data-toggle="modal" title="'
			. JHtml::tooltipText('COM_VISFORMS_CHANGE_FORM') . '">'
			. '<span class="icon-file"></span> '
			. JText::_('JSELECT') . '</a>';
		$html[] = '</span>';

// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		$html[] = JHtml::_(
			'bootstrap.renderModal',
			'modalVisform' . $this->id,
			array(
				'url' => $link,
				'title' => JText::_('COM_VISFORMS_CHANGE_FORM'),
				'width' => '800px',
				'height' => '300px',
				'footer' => '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">'
					. JText::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
			)
		);

		return implode("\n", $html);
	}
}
