<?php
/**
 * visdata model for Visforms
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

class VisformsModelVisdatas extends JModelList
{
	/**
	* data of selected form
	*
	* @var array
	* @since Joomla 1.6
	*/
	public $_data = Array();
	public $unSearchable = array('signature');
	public $unSortable = array('signature');
	
	/**
	* Visdata form id
	*
	* @var protected $_id Form Id
	*
	* @since Joomla 1.6
	*/
	protected $_id = null;
	
	public function __construct($config = array()) {
        if (!(empty($config['id']))) {
            $id = $config['id'];
        }
        else {
            $id = JFactory::getApplication()->input->getInt('fid', -1);
        }
		$this->setId($id);

		// get an array of fieldnames that can be used to sort data in data table
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'a.id', 'a.ipaddress', 'a.published', 'a.created', 'a.ismfd', 'a.created_by', 'a.modified',
                'id', 'ipaddress', 'published', 'ismfd', 'created_by', 'modified'
			);
		}
		
		// get all form field id's from database
		$db	= JFactory::getDbo();	
        $query = $db->getQuery(true);
        $query->select($db->quoteName('c.id'))
            ->from($db->quoteName('#__visfields') . ' as c ')
            ->where($db->quoteName('c.fid') . " = " . $id);
		$db->setQuery( $query );
		$fields = $db->loadObjectList();
		
		// add field id's to filter_fields
		foreach ($fields as $field) {
			$config['filter_fields'][] = "a.F" . $field->id;
            $config['filter_fields'][] = "F" . $field->id;
		}
		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null) { // Initialise variables.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
		// list state information
		parent::populateState('a.id', 'asc');
	}
	
	protected function getStoreId($id = '') {
		// compile the store id
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		return parent::getStoreId($id);
	}
	
	protected function getListQuery() {
		// create a new query object
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
        $fields = $this->getPublishedDatafields();
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'*'
			)
		);
		$tn = "#__visforms_" . $this->_id;
		$query->from($tn . ' AS a');

		// filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// filter by search
		$filter = $this->getFilter();		
		if (!($filter === '')) {
			$query->where($filter);
		}

		// add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.id');
		$orderDirn	= $this->state->get('list.direction', 'asc');
        // we store dates as strings in database. If sort order field is of type date we have to convert the strings before we order the recordsets
        foreach ($fields as $field) {
            $fName = 'F'.$field->id;
            if (($field->typefield == 'date') && (($orderCol == $fName) || ($orderCol == 'a.' . $fName))) {
                $formats = explode(';', $field->defaultvalue['f_date_format']);
                $format = $formats[1]; 
                $orderCol = ' STR_TO_DATE(' . $orderCol . ', '. $db->quote($format).  ') ';
                break;
            }
	        if (($field->typefield == 'number') && (($orderCol == $fName) || ($orderCol == 'a.' . $fName))) {
		        $orderCol = '(' . $orderCol .  ' * 1)';
		        break;
	        }
        }
		$query->order(($orderCol.' '.$orderDirn));
		return $query;
	}
	
	/**
	 * Method to set the form identifier
	 *
	 * @param	int form identifier
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function setId($id) {
		// set id and wipe data
		$this->_id = $id;
	}

	/**
	 * Method to set the text for SQL where statement for search filter
	 *
	 * @return string where statement for SQL
	 * @since	1.6
	 */
	public function getFilter() {
		// get Filter parameters
		$visFilter = $this->getState('filter.search');
		$filter = '';	
		if ($visFilter != '') {
			$filter = $filter." (";
			$fields = $this->getPublishedDatafields();
			$keywords = explode(" ", $visFilter);
			$k=count( $keywords );
			for ($j=0; $j < $k; $j++) {
                $n=count( $fields );
				for ($i=0; $i < $n; $i++) {
                    $rowField = $fields[$i];
					if ($rowField->showFieldInDataView && empty($rowField->unSearchable)) {
                        $prop="F".$rowField->id;
						$filter = $filter." upper(".$prop.") like upper('%".$keywords[$j]."%') or ";
					}
				}
				$filter = $filter." ipaddress like '%".$keywords[$j]."%' or ";
			}
			$filter = rtrim($filter,'or '); 
			$filter = $filter." )";
		}
		return $filter;
	}

	//when we call getDatafields directly from view.html with get() method, we cannot add parameters and get unpublished fields as well
	public function getPublishedDatafields() {
		return $this->getDatafields(' and c.published = 1');
	}
	
	/**
	 * Method to retrieves the fields list
	 *
	 * @return array Array of objects containing the data from the database
	 * @since	1.6
	 */
	public function getDatafields($where = "") {
		// lets load the data if it doesn't already exist
        $query = ' SELECT * from #__visfields as c where c.fid='.$this->_id;
        if ($where != '') {
            $query .= $where;
        }
        $query .= ' ORDER BY c.ordering ASC ';

        $datafields = $this->_getList( $query );
        foreach($datafields as $dataField) {
            $dataField->defaultvalue = VisformsHelper::registryArrayFromString($dataField->defaultvalue);
            if($dataField->typefield == "fieldsep" || $dataField->typefield == "image" || $dataField->typefield == "submit" || $dataField->typefield == "reset") {
                $dataField->showFieldInDataView = false;
            }
            else {
                $dataField->showFieldInDataView = true;
            }
            if (in_array($dataField->typefield, $this->unSortable)) {
            	$dataField->unSortable = true;
            } else {
	            $dataField->unSortable = false;
            }
	        if (in_array($dataField->typefield, $this->unSearchable)) {
		        $dataField->unSearchable = true;
	        } else {
		        $dataField->unSearchable = false;
	        }
	        if ($dataField->typefield == "signature") {
            	$dataField->canvasWidth = (isset($dataField->defaultvalue['f_signature_canvasWidth'])) ? $dataField->defaultvalue['f_signature_canvasWidth'] : 280;
		        $dataField->canvasHeight = (isset($dataField->defaultvalue['f_signature_canvasHeight'])) ? $dataField->defaultvalue['f_signature_canvasHeight'] : 120;
	        }
        }
		return $datafields;
	}
    
    /**
	 * Method to test whether a record can be exported.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   11.1
	 */
	public function canExport($fid) {
        $user = JFactory::getUser();
		// check form settings
		if ($fid != -1) {
            return $user->authorise('core.export.data', 'com_visforms.visform.' . (int) $fid);
		}
		else {
			// use component settings
            return $user->authorise('core.export.data', 'com_visforms');
        }
    }
    
    public function getFilterForm($data = array(), $loadData = true) {
		$form = parent::getFilterForm($data, $loadData);
		if (empty($form)) {
            return false;
		}
        
        // configure sort list - create two options for each visforms form field (asc and desc) and replace definition of fullordering field in filter_visdatas.xml
        $xml = 
            '<field
			name="fullordering"
			type="list"
			label="COM_VISFORMS_LIST_FULL_ORDERING"
			description="COM_VISFORMS_LIST_FULL_ORDERING_DESC"
			onchange="this.form.submit();"
			default="a.id ASC"
			>
			<option value="">JGLOBAL_SORT_BY</option>
            <option value="a.id ASC">JGRID_HEADING_ID_ASC</option>
			<option value="a.id DESC">JGRID_HEADING_ID_DESC</option>
            <option value="a.published ASC">JSTATUS_ASC</option>
			<option value="a.published DESC">JSTATUS_DESC</option>
            <option value="a.created ASC">JDATE_ASC</option>
			<option value="a.created DESC">JDATE_DESC</option>
			<option value="a.ipaddress ASC">COM_VISFORMS_SORT_IP_ASC</option>
			<option value="a.ipaddress DESC">COM_VISFORMS_SORT_IP_DESC</option>
            <option value="a.ismfd ASC">COM_VISFORMS_SORT_ISMFD_ASC</option>
			<option value="a.ismfd DESC">COM_VISFORMS_SORT_ISMFD_DESC</option>
            <option value="a.created_by ASC">COM_VISFORMS_SORT_CREATED_BY_ASC</option>
			<option value="a.created_by DESC">COM_VISFORMS_SORT_CREATED_BY_DESC</option>
			<option value="a.modified ASC">COM_VISFORMS_SORT_MODIFIED_AT_ASC</option>
			<option value="a.modified DESC">COM_VISFORMS_SORT_MODIFIED_AT_DESC</option>
			'
        ;
     
        $datafields = $this->getPublishedDatafields();
        foreach($datafields as $dataField) {
            if(isset($dataField->showFieldInDataView) && $dataField->showFieldInDataView == true && empty($dataField->unSortable)) {
                $xml .= '<option value="a.F' . $dataField->id . ' ASC">' . $dataField->name . ' ' . JText::_("COM_VISFORMS_ASC") . '</option>';
                $xml .= '<option value="a.F' . $dataField->id . ' DESC">' . $dataField->name . ' ' . JText::_("COM_VISFORMS_DESC") . '</option>';
            }
        }

        $xml .= '</field>';
        $xmlField = new SimpleXMLElement($xml);
        $form->setField($xmlField, 'list', 'true');
		return $form;
	}
    
    /**
	 * Method create content of one export cell
	 *
     * @param object $row visforms field object
     * @param string $type  Export cell type (field/label)
     * @param object $params export params form form
     * @param string $prop  field property to be exported
	 * @return string   export cell content
	 *
	 * @since Joomla 1.6 
	 */
    public function createExportCell($row, $type = Null, $params = Null, $prop = Null) {
    	$hasSub = VisformsAEF::checkAEF(VisformsAEF::$subscription);
        $data = "";
        if($type == 'field') {
            $prop = $prop;
        }
        else if ($type == 'label') {
            $prop = (!empty($row->customlabelforcsv) && $hasSub) ? 'customlabelforcsv': $type;
        }
        else {
            return $data;
        }

        if ((!isset($prop)) || (!is_string($prop))) {
            return $data;
        }

        if ((!function_exists('iconv')) || (isset($params->usewindowscharset) && ($params->usewindowscharset == 0))) {
            $unicode_str_for_Excel = $row->$prop;
        }
        else {
            // convert characters into window characterset for easier using with excel
            $unicode_str_for_Excel = iconv("UTF-8", "windows-1250//TRANSLIT", $row->$prop);
        }
        
        $unicode_str_for_Excel = JHtmlVisformsselect::removeNullbyte($unicode_str_for_Excel);
        $unicode_str_for_Excel = str_replace("\"", "\"\"", $unicode_str_for_Excel);
        $separator = (isset($params->expseparator)) ? $params->expseparator : ";";

        $pos = strpos($unicode_str_for_Excel, $separator);
        if ($pos === false) {
            $data .= $unicode_str_for_Excel;
        }
        else {
            $data .= "\"".$unicode_str_for_Excel."\"";
        }				

        return $data;
    }
    
    /**
	 * Method create content of export cells for invariant form fields (id, published) placed at the front of each export row
	 *
     * @param object $params export params form form
     * @param object $row visforms field object
     * 
	 * @return string   export cell content
	 *
	 * @since Joomla 1.6 
	 */
    public function createPreFields ($params, $row, $separator = ";") {
        $data = array();
        if (!empty($params->expfieldid)) {
            $data[] = $row->id ;
        }
        if (!empty($params->expfieldpublished)) {
            $data[] = $row->published;
        }
        if (!empty($params->expfieldcreated)) {
            $data[] = VisformsHelper::getFormattedServerDateTime($row->created);
        }
        if (!empty($params->expfieldcreatedby)) {
            $data[] = $row->created_by;
        }

        return implode($separator, $data);
    }
    
    /**
	 * Method create content of export cells for invariant form fields (ipaddress) placed at the end of each export row
	 *
     * @param object $params export params form form
     * @param object $row visforms field object
     * 
	 * @return string   export cell content
	 *
	 * @since Joomla 1.6 
	 */
    public function createPostFields ($params, $row, $separator = ";") {
        $data = array();
        if (!empty($params->expfieldip)) {
            $data[] = $row->ipaddress;
        }
        if (!empty($params->expfieldismfd)) {
            $data[] = $row->ismfd;
        }
		if (!empty($params->expfieldmodifiedat)) {
			$data[] = VisformsHelper::getFormattedServerDateTime($row->modified);
		}
	    return implode($separator, $data);
    }
    
    public function createExportBuffer ($params = null, $cIds = array()) {
        if (!(is_object($params))) {
            return "";
        }
        // get submitted form dataset
		$items = $this->getItems();
        // get fields to export from database
        // according to export parameters of field and form
        $where = ' and c.includefieldonexport = 1';
        $where .= (!(empty($params->exppublishfieldsonly))) ? ' and c.published = 1' : '';
        $where .= " and c.typefield NOT in('submit', 'image', 'reset', 'fieldsep', 'pagebreak')";
		$fields = $this->getDatafields($where);
		$lineBuffer = array();
        $buffer = array();
		$nbItems = count($items);
		$nbFields = count($fields);
        $separator = (isset($params->expseparator)) ? $params->expseparator : ";";
		
		// create table headers from field names
        // previous default was, that headers were always created
        if ((!(isset($params->includeheadline))) || ((isset($params->includeheadline)) && ($params->includeheadline == 1))) {
            if (!empty($params->expfieldid)) {
	            $lineBuffer[] = JText::_( 'COM_VISFORMS_ID' );
            }
            if (!empty($params->expfieldpublished)) {
	            $lineBuffer[] = JText::_( 'COM_VISFORMS_PUBLISHED' );
            }
            if (!empty($params->expfieldcreated)) {
	            $lineBuffer[] = JText::_( 'COM_VISFORMS_FIELD_CREATED_LABEL' );
            }
            if (!empty($params->expfieldcreatedby)) {
	            $lineBuffer[] = JText::_( 'COM_VISFORMS_FIELD_CREATED_BY_LABEL' );
            }

            for ($i=0; $i < $nbFields; $i++) {
                $rowField = $fields[$i];
	            $lineBuffer[] = $this->createExportCell($rowField, 'label', $params);
            }
            if (!empty($params->expfieldip)) {
	            $lineBuffer[] = JText::_( 'COM_VISFORMS_IP' );
            }
            if (!empty($params->expfieldismfd)) {
	            $lineBuffer[] = JText::_( 'COM_VISFORMS_MODIFIED' );
            }
			if (!empty($params->expfieldmodifiedat)) {
				$lineBuffer[] = JText::_( 'COM_VISFORMS_MODIFIED_AT' );
			}
	        $line = implode($separator, $lineBuffer);
	        if ($line !== '') {
		        $buffer[] = $line  . " \n";
	        }
        }
        // create data sets from rows
		for ($i=0; $i < $nbItems; $i++) {
            $row = $items[$i];
			$lineBuffer = array();
            // exclude unpublished data sets according to form settings
            if(!(empty($params->exppublisheddataonly)) && !$row->published) {
                continue;
            }
            // some data sets are checked, we export only those
            if(count($cIds) > 0) {
                foreach($cIds as $value) {
                    if($row->id == $value) {
                    	$preFields = $this->createPreFields ($params, $row);
                    	if ($preFields !== '') {
		                    $lineBuffer[] = $preFields;
                        }
                        for ($j=0; $j < $nbFields; $j++) {
                            $rowField = $fields[$j];
                            $prop="F".$rowField->id;
                            if ($rowField->typefield == "file") {
                                //we must decode json data and extract required values
                                if (isset($rowField->fileexportformat) && ((int) $rowField->fileexportformat === 1)) {
	                                $row->$prop = JHtml::_('visforms.getUploadFilePath', $row->$prop);
                                }
                                else if (isset($rowField->fileexportformat) && ((int) $rowField->fileexportformat === 2)) {
	                                $row->$prop = JHtml::_('visforms.getUploadFileFullPath', $row->$prop);
                                }
                                else {
	                                $row->$prop = JHtml::_('visforms.getUploadFileName', $row->$prop);
                                }
                            }
                            if ($rowField->typefield == 'location') {
	                            $tmp = VisformsHelper::registryArrayFromString($row->$prop);
	                            $row->$prop = implode(', ', $tmp);
                            }
                            $lineBuffer[] = $this->createExportCell($row, 'field', $params, $prop);
                        }
	                    $postFields = $this->createPostFields ($params, $row);
	                    if ($postFields !== '') {
		                    $lineBuffer[] = $postFields;
	                    }
	                    $line = implode($separator, $lineBuffer);
	                    if ($line !== '') {
		                    $buffer[] = $line . " \n";
	                    }
                    }
                }
            }
            // no data sets checked, we export all data sets
            else {
	            $preFields = $this->createPreFields ($params, $row, $separator);
	            if ($preFields !== '') {
		            $lineBuffer[] = $preFields;
	            }
                for ($j=0; $j < $nbFields; $j++) {
                    $rowField = $fields[$j];
                    $prop="F".$rowField->id;
                    if ($rowField->typefield == "file") {
                        //we must decode json data and extract required values
                        if (isset($rowField->fileexportformat) && ((int) $rowField->fileexportformat === 1)) {
	                        $row->$prop = JHtml::_('visforms.getUploadFilePath', $row->$prop);
                        }
                        else if (isset($rowField->fileexportformat) && ((int) $rowField->fileexportformat === 2)) {
	                        $row->$prop = JHtml::_('visforms.getUploadFileFullPath', $row->$prop);
                        }
                        else {
	                        $row->$prop = JHtml::_('visforms.getUploadFileName', $row->$prop);
                        }
                    }
                    if ($rowField->typefield == 'location') {
	                    $tmp = VisformsHelper::registryArrayFromString($row->$prop);
	                    $row->$prop = implode(', ', $tmp);
                    }
                    $lineBuffer[] = $this->createExportCell($row, 'field', $params, $prop);
                }
	            $postFields = $this->createPostFields ($params, $row, $separator);
	            if ($postFields !== '') {
		            $lineBuffer[] = $postFields;
	            }
	            $line = implode($separator, $lineBuffer);
	            if ($line !== '') {
		            $buffer[] = $line . " \n";
	            }
            }
		}
		$csv = implode('', $buffer);
        if (!empty($csv)) {
        	$csv = rtrim($csv, "\n");
        }
	    return $csv;
    }
}
