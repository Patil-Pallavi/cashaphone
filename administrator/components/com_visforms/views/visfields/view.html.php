<?php
/**
 * Visfields view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

defined('_JEXEC') or die( 'Restricted access' );
require_once(JPATH_ADMINISTRATOR . '/components/com_visforms/views/itemsviewbase.php');

class VisformsViewVisfields extends VisFormsItemsViewBase
{
	protected $form;

	function __construct($config = array()) {
        parent::__construct($config);
        $this->viewName     = 'visfields';
        $this->editViewName = 'visfield';
    }

	protected function setMembers() {
        $fid            = JFactory::getApplication()->input->getInt('fid', -1);
        $this->canDo    = VisformsHelper::getActions($fid);
        $this->form	    = $this->get('Form');
    }

    protected function getTitle() {
        $title = $this->get('Formtitle');
        if( !empty($title)) {
            $title = JText::_( 'COM_VISFORMS_FIELDS' ) . ' ' . JText::_('COM_VISFORMS_OF_FORM_PLAIN') . ' ' . $title;
        }
        return $title;
    }

    protected function setToolbar() {
        if ($this->canDo->get('core.create')) {
            JToolbarHelper::addNew('visfield.add');
        }

        if ($this->canDo->get('core.edit.state')) {
            JToolbarHelper::publishList('visfields.publish');
            JToolbarHelper::unpublishList('visfields.unpublish');
            JToolbarHelper::checkin('visfields.checkin');
        }

        if ($this->canDo->get('core.delete')) {
            JToolbarHelper::deleteList('COM_VISFORMS_DELETE_FIELD_TRUE', 'visfields.delete', 'COM_VISFORMS_DELETE');
        }

        if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')) {
            JToolbarHelper::editList('visfield.edit');
        }

        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');
        // Add a batch button
        if ($this->user->authorise('core.create', 'com_visforms')
            && $this->user->authorise('core.edit', 'com_visforms')
            && $this->user->authorise('core.edit.state', 'com_visforms'))
        {
            JHtml::_('bootstrap.modal', 'collapseModal');
            $title = JText::_('JTOOLBAR_BATCH');

            // Instantiate a new JLayoutFile instance and render the batch button
            $layout = new JLayoutFile('joomla.toolbar.batch');
            $html = $layout->render(array('title' => $title));
            $bar->appendButton('Custom', $html, 'batch');
        }

        JToolbarHelper::custom('visfields.forms','forms','forms',JText::_('COM_VISFORMS_SUBMENU_FORMS'), false) ;
        JToolbarHelper::custom('visfields.form','file-2','file2-',JText::_('COM_VISFORMS_BACK_TO_FORM'), false) ;
    }
}
