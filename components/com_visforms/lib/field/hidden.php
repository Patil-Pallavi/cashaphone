<?php
/**
 * Visforms field hidden class
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

/**
 * Visforms field hidden
 *
 * @package        Joomla.Site
 * @subpackage    com_visforms
 * @since        1.6
 */
class VisformsFieldHidden extends VisformsField
{
    /**
     *
     * Constructor
     *
     * @param object $field field object as extracted from database
     * @param object $form form object as extracted from database
     */

    public function __construct($field, $form)
    {
        parent::__construct($field, $form);
        //store potentiall query Values for this field in the session
        $this->setQueryValue();
        $this->postValue = $this->input->post->get($field->name, '', 'STRING');
    }

    /**
     * Preprocess field. Set field properties according to field defition, query params, user inputs
     */

    protected function setField()
    {
        //preprocessing field
        $this->extractDefaultValueParams();
        $this->extractRestrictions();
        $this->mendBooleanAttribs();
        $this->setIsConditional();
        $fillWith = $this->fillWith();
        if ($fillWith !== false)
        {
            //if we have a special default value set in field declaration we use this
            $this->field->attribute_value = $fillWith;
        }
        $this->setEditValue();
        $this->setConfigurationDefault();
        $this->setFieldDefaultValue();
        $this->setDbValue();
        $this->setRedirectParam();
        $this->setCustomJs();
    }

    /**
     * The the default value of the field which is displayed in the form according field defition, query params, user inputs
     */

    protected function setFieldDefaultValue()
    {
        $field = $this->field;

        if ($this->input->getCmd('task', '') == 'editdata')
        {
            if ((isset($this->field->editValue)))
            {
                $this->field->attribute_value = $this->field->editValue;
            }
            $this->field->dataSource = 'db';
            return;
        }
        //if we have a POST Value, we use this
        if ((count($_POST) > 0) && isset($_POST['postid']) && ($_POST['postid'] == $this->form->id))
        {
            //this will create a error message on form display
            $this->validateUserInput('postValue');
            if (isset($_POST[$field->name]))
            {
                $this->field->attribute_value = $this->postValue;
            }
            $this->field->dataSource = 'post';
            return;
        }
        //if we have a GET Value and field may use GET values, we uses this
        if (isset($field->allowurlparam) && ($field->allowurlparam == true))
        {
            $urlparams = JFactory::getApplication()->getUserState('com_visforms.urlparams.' . $this->form->context, null);
            if (!empty($urlparams) && (is_array($urlparams)) && (isset($urlparams[$this->field->name])))
            {
                $queryValue = $urlparams[$this->field->name];
            }
            if(isset($queryValue))
            {
                $this->field->attribute_value = $queryValue;
                $this->field->dataSource = 'query';
                return;
            }
        }
        //Nothing to do
        return;
    }

    /**
     *
     * Method to validate user inputs, if not: set field property isValid to false and set error message
     * @param string $inputType user input type (postValue, queryValue)
     */
    protected function validateUserInput($inputType)
    {
        $type = $this->type;
        $value = $this->$inputType;
        //we can't validate the user input, if the field is filled with a unigue value by default
        if (!empty($this->field->filluid))
        {
            return;
        }
        //only check, that user input === attribute_value if a attribute_value is set (!=="")
        if((!isset($this->field->attribute_value)) || ($this->field->attribute_value === ''))
        {
            return true;
        }
        //user input must match the attribute value; if not user input is set this is invalide, too
        if (isset($value) && VisformsValidate::validate('equalto', array('value' => $value, 'cvalue' => $this->field->configurationDefault)))
        {
            return;
        }
        else
        {
            //invalid user inputs - set field->isValid to false
            $this->field->isValid = false;
            //set the Error Message
            $error = JText::sprintf('COM_VISFORMS_INVALID_HIDDEN_FIELD_USER_INPUT_DOES_NOT_MATCH_DEFAULT', $this->field->label);
            $this->setErrorMessageInForm($error);
            return;
        }
    }

    /**
     * Method to convert post values into a string that can be stored in db and attach it as property to the field object
     */
    protected function setDbValue()
    {
        if (isset($this->field->dataSource) && $this->field->dataSource == 'post')
        {
            $this->field->dbValue = $this->postValue;
        }
        $test = true;
    }

    protected function setRedirectParam()
    {
        if (isset($this->field->dataSource) && $this->field->dataSource == 'post' && (!empty($this->field->addtoredirecturl)))
        {
            $this->field->redirectParam = $this->postValue;
        }
    }

    protected function fillWith()
    {
        //if we have a special default value set in field declaration we use this
        $field = $this->field;
        if (isset($field->filluid) && ($field->filluid == "1"))
        {
            return uniqid($this->field->attribute_value, true);
        }
        return false;
    }

    protected function setConfigurationDefault()
    {
        $this->field->configurationDefault = $this->field->attribute_value;
        $task = $this->input->getCmd('task', '');
        if (($task !== 'editdata') && ($task !== 'saveedit'))
        {
            $urlparams = JFactory::getApplication()->getUserState('com_visforms.urlparams.' . $this->form->context, null);
            if (!empty($urlparams) && (is_array($urlparams)) && (isset($urlparams[$this->field->name])))
            {
                $queryValue = $urlparams[$this->field->name];
            }
            //if form was originally called with valid url params, reset to this url params
            $this->field->configurationDefault = (isset($this->field->allowurlparam) && ($this->field->allowurlparam == true) && isset($queryValue)) ? $queryValue : $this->field->attribute_value;
        }
    }

    protected function setEditValue()
    {
        $task = $this->input->getCmd('task', '');
        if (($task === 'editdata') || ($task === 'saveedit'))
        {
            $this->field->editValue = "";
            $data = $this->form->data;
            $datafieldname = "F" . $this->field->id;
            if (isset($data->$datafieldname))
            {
                $filter = JFilterInput::getInstance();
                $this->field->editValue = $filter->clean($data->$datafieldname, 'STRING');
            }
        }
    }
}