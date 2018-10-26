<?php
/**
 * Visforms controller for VisCpanel
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class VisformsControllerViscpanel extends JControllerLegacy
{
    function __construct($config = array()){
        parent::__construct($config);
    }

    /**
     * display the visforms CSS
     *
     * @return void
     * @since Joomla 1.6
     */
    public function edit_css() {
        $this->setRedirect("index.php?option=com_visforms&task=vistools.editCSS");
    }

    public function dlid() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $dlId = $this->input->post->get('downloadid', '', 'string');
        $this->setRedirect(JRoute::_('index.php?option=com_visforms&view=viscpanel', false));
        $model = $this->getModel('Viscpanel', 'VisformsModel');
        $model->setState('dlid', $dlId);
        if (!$model->storeDlid()) {
            return false;
        }
        return true;
    }

    public function gotSubUpdateInfo() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $this->setRedirect(JRoute::_('index.php?option=com_visforms&view=viscpanel', false));
        $model = $this->getModel('Viscpanel', 'VisformsModel');
        if (!$model->storeGotSubUpdateInfo()) {
            return false;
        }
        return true;
    }
}