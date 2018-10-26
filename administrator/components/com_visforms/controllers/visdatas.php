<?php
/**
 * visdatas controller for Visforms
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
use Joomla\Utilities\ArrayHelper;

class VisformsControllerVisdatas extends JControllerAdmin
{

	public function __construct($config = array()) {
        parent::__construct($config);
        $fid = JFactory::getApplication()->input->getInt('fid', 0);
        $this->view_list = 'visdatas&fid=' . $fid;
        $this->text_prefix = 'COM_VISFORMS_DATA';
	}

	public function getModel($name = 'Visdata', $prefix = 'VisformsModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Method to export data saved in database to csv
	 *
	 * @return void
	 *
	 * @since Joomla 1.6 
	 */
	public function export() {
        // form id
		$fid = JFactory::getApplication()->input->getInt('fid', -1);
        // get the data model
        $model = $this->getModel('visdatas');
        // return if user has no export permission
        if(!$model->canExport($fid)) {
            JFactory::getApplication()->redirect('index.php?option=com_visforms&view=visdatas&fid='.$fid , 'COM_VISFORMS_EXPORT_NOT_PERMITTED', 'warning');
        }
        
        $cIds = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
        ArrayHelper::toInteger($cIds);
		// get the form model and form settings
        $formModel = $this->getModel('visform');
        $form = $formModel->getItem($fid);
        $params = new stdClass();
        
        foreach ($form->exportsettings as $name => $value) {
            // make names shorter and set all export settings as properties of form object
            $params->$name = $value;   
        }
        
        // create a export data string (body of csv file)
        $buffer = $model->createExportBuffer($params, $cIds);
			
		header("Expires: Sun, 1 Jan 2000 12:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-disposition: attachment; filename=visforms_" . date("Ymd").".csv");  
        echo $buffer;
        JFactory::getApplication()->close();
	}
       
    /**
	 * Method to redirect to forms view
	 *
	 * @return void
	 *
	 * @since Joomla 1.6 
	 */
    public function forms() {
        $this->setRedirect('index.php?option=com_visforms&view=visforms');
    }
    
    /**
	 * Method to redirect to form view (not yet used)
	 *
	 * @return void
	 *
	 * @since Joomla 1.6 
	 */
    public function form() {
        $fid = JFactory::getApplication()->input->getInt('fid', 0);
        $app = JFactory::getApplication();
        $context = "com_visforms.edit.visform.id";
        if ($fid != 0) {
            $app->setUserState($context, (array) $fid);
        }
        $this->setRedirect('index.php?option=com_visforms&view=visform&layout=edit&id=' . $fid);
    }
    
    protected function postDeleteHook(JModelLegacy $model, $cid = NULL) {
        foreach ($cid as $id) {
            $model->deleteOrgData($id);
        }
    }
    
    public function reset(){
        $model = $this->getModel('visdata');
        $cid = $this->input->get('cid', array(), 'array');
        foreach ($cid as $id) {
            $model->restoreToUserInputs($id);
            $model->deleteOrgData($id);
        }
        $nText = $this->text_prefix . '_N_ITEMS_RESET';
        $this->setMessage(JText::plural($nText, count($cid)));
        $this->setRedirect('index.php?option=com_visforms&view='. $this->view_list);
    }
}