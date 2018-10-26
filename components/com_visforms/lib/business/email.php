<?php
/**
 * Visforms field email business class
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once(__DIR__ . '/text.php');

/**
 * Perform business logic on field email
 *
 * @package		Joomla.Site
 * @subpackage	com_visforms
 * @since		1.6
 */
class VisformsBusinessEmail extends VisformsBusinessText
{

	protected function setField()
	{
		$this->setIsDisabled();
		$this->setCustomJs();
		if (isset($this->field->dataSource) && $this->field->dataSource == 'post')
		{
			$this->validatePostValue();
		}
		$this->addShowWhenForForm();
	}
    protected function setCustomJs() {
    	if (!isset($this->field->validate_mailExists)) {
    		return;

	    }
	    $field = $this->field;
    	$extraAttribs = ' placeholder=\"'. JText::_('COM_VISFORMS_ENTER_VERIFICATION_CODE_PLACEHOLDER') .'\"';
    	$extraAttribs .= (!empty($this->field->attribute_value)) ? ' required = \"required\" aria-required=\"true\" ' : '';
    	$extraAttribs .= (isset($field->isDisabled) && ($field->isDisabled == true)) ? ' disabled=\"disabled\"' : '';
    	$extraClass = (isset($field->isDisabled) && ($field->isDisabled == true)) ? ' ignore' : '';
	    $script = '
	        jQuery(document).ready( function() {	        
		        jQuery("<div class=\"fc-tbxfield'.$field->id.'_code\"></div><span class=\"btn verifyMailBtn\" onclick=\"verifyMail(\'field'.$field->id.'\',\''.$field->fid.'\',\''.JSession::getFormToken().'\'); return false;\">'. JText::_('COM_VISFORMS_VERIFY') .'</span><input type=\"text\" id=\"field'.$field->id.'_code\" name=\"'.$field->name.'_code\" class=\"form-control verificationCode'.$extraClass.'\"'. $extraAttribs .' />").insertAfter("#field'.$field->id.'");
		        jQuery("#field'.$field->id.'").on("change", function () {
		            if (jQuery(this).val()) {
		                jQuery("#field'.$field->id.'_code").prop("required", true);
		            }
		            else {
		                jQuery("#field'.$field->id.'_code"  ).prop("required", false);
		            }
		        });
	        });
	    ';
	    $this->field->customJs[] = $script;
    }

	protected function validatePostValue() {
		parent::validatePostValue();
		// addtional validation of email verification code
		if ($this->field->attribute_value != "" && isset($this->field->validate_mailExists)) {
			$code = JFactory::getApplication()->input->post->get($this->field->name . '_code', '', 'STRING');
			if (VisformsValidate::validate('verificationcode', array('value' => $code, 'verificationAddr' => $this->field->attribute_value)) !== true) {
				$error = JText::sprintf('COM_VISFORMS_POST_VALIDATION_CODE_INVALID', $this->field->label);
				$this->field->isValid = false;
				$this->setErrors($error);
			}
			return;
		}
	}
}