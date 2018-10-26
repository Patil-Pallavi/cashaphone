<?php
/**
 * Visforms field textarea class
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
 * Visforms field textarea
 *
 * @package        Joomla.Site
 * @subpackage    com_visforms
 * @since        1.6
 */
class VisformsFieldTextarea extends VisformsField
{
    //no setting of $this->postValue or $this->editValue in a constructor for this field tyes. Values are sett in $this->getDefaultInputs()
    //no queryValue allowed for textareas!

    /**
     * Preprocess field. Set field properties according to field defition, query params, user inputs
     */

    protected function setField()
    {
        //preprocessing field
        $this->extractDefaultValueParams();
        $this->extractRestrictions();
        $this->mendBooleanAttribs();
        $this->setIndividualProperties();
        $this->getDefaultInputs();
        $this->setIsConditional();
        $this->setEditValue();
        $this->setConfigurationDefault();
        $this->setFieldDefaultValue();
        $this->setDbValue();
        $this->setCustomJs();
        $this->setFieldsetCounter();
    }

    /**
     * The the default value of the field which is displayed in the form according field defition, query params, user inputs
     */

    protected function setFieldDefaultValue()
    {
        $field = $this->field;
        if (JFactory::getApplication()->input->getCmd('task', '') == 'editdata')
        {
            if ((isset($this->field->editValue)))
            {
                $this->field->initvalue = $this->field->editValue;
            }
            $this->field->dataSource = 'db';
            return;
        }
        //if we have a POST Value, we use this
        if ((count($_POST) > 0) && isset($_POST['postid']) && ($_POST['postid'] == $this->form->id))
        {
            if (isset($_POST[$field->name]))
            {
                $this->field->initvalue = $this->postValue;
            }
            else
            {
                $this->field->initvalue = $this->field->configurationDefault;
            }
            $this->field->dataSource = 'post';
            return;
        }

        //No query (GET) values for textareas


        //Nothing to do
        return;
    }

    /**
     * add individual properties to field declaration
     */
    protected function setIndividualProperties()
    {
        $field = $this->field;
        //we have an HTMLEditor and have to check that it is not empty
        if (isset($field->attribute_required) && $field->attribute_required == 'required' && isset($field->HTMLEditor) && $field->HTMLEditor == '1' && (!(isset($field->attribute_readonly)) || $field->attribute_readonly != "readonly"))
        {
            $this->field->textareaRequired = true;
        }
        //We have an HTMLEditor
        if (isset($field->HTMLEditor) && $field->HTMLEditor == '1' && (!(isset($field->attribute_readonly)) || ($field->attribute_readonly != "readonly")))
        {
            $this->field->hasHTMLEditor = true;
        }
    }

    /**
     * Method to get user inputs. Input format according to field settings
     */
    private function getDefaultInputs()
    {
        $field = $this->field;
        if (isset($field->hasHTMLEditor) && $field->hasHTMLEditor == true)
        {
            $this->postValue = $this->input->post->get($field->name, '', 'RAW');
        }
        else
        {
            $this->postValue = $this->input->post->get($field->name, '', 'STRING');
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
    }

    protected function setRedirectParam()
    {
        return;
    }

    protected function setConfigurationDefault()
    {
        $task = $this->input->getCmd('task', '');
        $this->field->configurationDefault = $this->field->initvalue;
        //if ($task === 'send')
        if (($task !== 'editdata') && ($task !== 'saveedit'))
        {
            $urlparams = JFactory::getApplication()->getUserState('com_visforms.urlparams.' . $this->form->context, null);
            if (!empty($urlparams) && (is_array($urlparams)) && (isset($urlparams[$this->field->name])))
            {
                $queryValue = $urlparams[$this->field->name];
            }
            //if form was originally called with valid url params, reset to this url params
            $this->field->configurationDefault = (isset($this->field->allowurlparam) && ($this->field->allowurlparam == true) && isset($queryValue)) ? $queryValue : $this->field->initvalue;
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
                if (isset($this->field->hasHTMLEditor) && $this->field->hasHTMLEditor == true)
                {
                    $this->field->editValue = $filter->clean($data->$datafieldname, 'RAW');
                }
                else
                {
                    $this->field->editValue = $filter->clean($data->$datafieldname, 'STRING');
                }
            }
        }
    }
}