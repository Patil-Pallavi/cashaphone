<?php
/**
 * Visform view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

// no direct access
defined('_JEXEC') or die( 'Restricted access' );
require_once(JPATH_ADMINISTRATOR . '/components/com_visforms/views/itemviewbase.php');

class VisformsViewVisform extends VisFormsItemViewBase
{
    function __construct($config = array()) {
        parent::__construct($config);
        $this->editViewName = "visform";
        $this->controllerName = 'visform';
    }

    protected function setMembers() { }

    protected function getTitle() {
        $text = $this->isNew ? JText::_( 'COM_VISFORMS_FORM_NEW' ) : JText::_( 'COM_VISFORMS_FORM_EDIT' );
        return JText::_('COM_VISFORMS_FORM') . VisformsHelper::appendTitleAppendixFormat($text);
    }

    protected function setToolbar() {
        if ($this->canDo->get('core.create')) {
            JToolbarHelper::save2copy("$this->controllerName.save2copy");
        }

        if (!$this->checkedOut) {
            if ($this->canDo->get('core.edit')) {
                JToolbarHelper::custom("$this->controllerName.fields",'forms','forms','COM_VISFORMS_FIELDS',false) ;
            }
        }

        if ($this->form->getValue('saveresult') == '1') {
            JToolbarHelper::custom("$this->controllerName.datas",'archive','archive','COM_VISFORMS_DATAS',false) ;
        }
    }

    protected function getFIdUrlQueryName() { return 'id'; }
}
