<?php
/**
 * Visforms view for Visforms
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
require_once(JPATH_ADMINISTRATOR . '/components/com_visforms/views/itemsviewbase.php');

class VisformsViewVisforms extends VisFormsItemsViewBase
{
	public $update_message;

    function __construct($config = array()) {
        parent::__construct($config);
        $this->viewName     = 'visforms';
        $this->editViewName = 'visform';
    }

    protected function setMembers() {
        $this->canDo = VisformsHelper::getActions();

        // show update message once
        $this->update_message = $this->app->getUserState('com_visforms.update_message');
        if (isset($this->update_message)) {
            $this->appsetUserState('com_visforms.update_message', null);
        }
    }

    protected function getTitle() {
        return JText::_('COM_VISFORMS_SUBMENU_FORMS');
    }

	protected function setToolbar()
	{
		if ($this->canDo->get('core.create')) {
            JToolbarHelper::addNew('visform.add');
		}

		if ($this->canDo->get('core.edit.state')) {
            JToolbarHelper::publishList('visforms.publish');
			JToolbarHelper::unpublishList('visforms.unpublish');
			JToolbarHelper::checkin('visforms.checkin');
		}

		if ($this->canDo->get('core.delete')) {
            JToolbarHelper::deleteList('COM_VISFORMS_DELETE_FORM_TRUE', 'visforms.delete', 'COM_VISFORMS_DELETE');
		}

		if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')) {
            JToolbarHelper::editList('visform.edit');
		}

		// add a batch button

        $user  = JFactory::getUser();
        if ($user->authorise('core.create', 'com_visforms')
            && $user->authorise('core.edit', 'com_visforms')
            && $user->authorise('core.edit.state', 'com_visforms'))
        {
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');
			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');
			$html = $layout->render(array('title' => $title));
            // Get the toolbar object instance
            $bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Custom', $html, 'batch');
		}
	}
}
