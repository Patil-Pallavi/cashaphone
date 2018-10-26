<?php
/**
 * viscpanel model for Visforms
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

class VisformsModelViscpanel extends JModelLegacy
{
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function getItems() {
        $model = JModelLegacy::getInstance('Visforms', 'VisformsModel', array('ignore_request' => true));
        return $model->getItems();
    }

    public function getSubmitFieldCount() {
        $model = JModelLegacy::getInstance('Visfields', 'VisformsModel', array('ignore_request' => true));
        return $model->getSubmitFieldCount();
    }

    public function storeDlid() {
        $extensions = "('files_vfmultipageforms', 'files_vfbt3layouts', 'files_vffrontedit', 'Plugin Visforms - Mail Attachments', 'Plugin Content Visforms Form View', 'Plugin Visforms - Maxsubmissions', 'Plugin Visforms - Delay Double Registration', 'plg_search_visformsdata', 'Plugin Content Visforms Data View', 'Visforms - Custom Mail Address', 'files_vfcustomfieldtypes', 'vfsubscription')";
        $dlId = $this->getState('dlid');
        $extra_query = (!empty($dlId)) ? "dlid=$dlId" : "";
        $return = true;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__update_sites')
            ->set('extra_query = ' . $db->quote($extra_query))
            //->where('name = "vfsubscription"');
            ->where('name in ' . $extensions);
        $db->setQuery($query);
        try {
            $db->execute();
        } catch (RuntimeException $e) {
            JFactory::getApplication()->enqueueMessage($e->getMessage() . ' Problems saving download id', 'error');
            $return = false;
        }
        $component = JComponentHelper::getComponent('com_visforms');
        $component->params->set('downloadid', $dlId);
        $componentId = $component->id;
        $table = JTable::getInstance('extension');
        $table->load($componentId);
        $table->bind(array('params' => $component->params->toString()));
        if (!$table->check()) {
            JFactory::getApplication()->enqueueMessage('Invalid params', 'error');
            return false;
        }
        if (!$table->store()) {
            JFactory::getApplication()->enqueueMessage('Problems saving params', 'error');
            return false;
        }
        return $return;
    }

    public function storeGotSubUpdateInfo() {
        $component = JComponentHelper::getComponent('com_visforms');
        $component->params->set('gotSubUpdateInfo', true);
        $componentId = $component->id;
        $table = JTable::getInstance('extension');
        $table->load($componentId);
        $table->bind(array('params' => $component->params->toString()));
        if (!$table->check()) {
            return false;
        }
        if (!$table->store()) {
            return false;
        }
        return true;
    }
}