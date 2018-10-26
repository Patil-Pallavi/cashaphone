<?php
/**
 * Visforms field file class
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
 * Visforms field file
 *
 * @package        Joomla.Site
 * @subpackage    com_visforms
 * @since        1.6
 */
class VisformsFieldFile extends VisformsField
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
        //file inputs are not sumitted with the post
        $this->postValue = "";
        //no edit or queryValue for file upload fields
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
        $this->addValidationAttribsForUpload();
        $this->setIsConditional();
        $this->setFieldDefaultValue();
        $this->setCustomJs();
        $this->setFieldsetCounter();
    }

    /**
     * The the default value of the field which is displayed in the form according field defition, query params, user inputs
     */

    protected function setFieldDefaultValue()
    {

        //file upload fields do not use the post value but the $_FILES var, but if we have a POST Value, we set dataSource property
        if ((count($_POST) > 0) && isset($_POST['postid']) && ($_POST['postid'] == $this->form->id))
        {
            $this->field->dataSource = 'post';
            return;
        }
        //Nothing to do
        return;
    }

    /**
     * Method to convert post values into a string that can be stored in db and attach it as property to the field object
     */
    protected function setDbValue()
    {
        return;
    }

    protected function setRedirectParam()
    {
        return;
    }
	
    protected function addValidationAttribsForUpload()
    {
        $uploadMaxFileSize = VisformsmediaHelper::toBytes(ini_get('upload_max_filesize'));
        $maxfilesize = (((int) $uploadMaxFileSize > 0) && (((int) $this->form->maxfilesize === 0) || ($this->form->maxfilesize * 1024) > $uploadMaxFileSize)) ? $uploadMaxFileSize : $this->form->maxfilesize*1024;
        $allowedExtensions = (!empty($this->field->allowedextensions)) ? $this->field->allowedextensions : $this->form->allowedextensions;
        $this->field->validate_filesize = $maxfilesize;
        $this->field->validate_fileextension = "'" . $allowedExtensions . "'";
    }
}