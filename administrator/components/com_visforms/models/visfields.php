<?php
/**
 * visfields model for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

defined('_JEXEC') or die();

class VisformsModelVisfields extends JModelList
{
    private $fid;

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'label', 'a.label',
				'published', 'a.published',
				'typefield', 'a.typefield',
				'ordering', 'a.ordering',
				'dataordering', 'a.dataordering',
			);
		}
		parent::__construct($config);
        $app = JFactory::getApplication();
		$this->fid = $app->input->getInt('fid',  0);
	}
	
	protected function populateState($ordering = null, $direction = null) {
		// Initialise variables.
		$app = JFactory::getApplication();
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
		// list state information.
		parent::populateState('a.id', 'asc');
        // force a language
		$forcedLanguage = $app->input->get('forcedLanguage');
		if (!empty($forcedLanguage)) {
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}
	}
	
	protected function getStoreId($id = '') {
		// compile the store id
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		return parent::getStoreId($id);
	}
	
	protected function getListQuery() {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				'*')
        );
		$tn = "#__visfields";
		$query->from($tn . ' AS a')
            ->where('a.fid='.$this->fid);

		// filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(a.published = 0 OR a.published = 1)');
		}
		
		// filter by search in label
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(a.label LIKE '.$search.' OR a.name LIKE '.$search.')');
		}
		
		// add the list ordering clause
		$orderCol	= $this->state->get('list.ordering', 'a.id');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		return $query;
	}

    public function getFid() { return $this->fid; }

    public function getFormtitle () {
        $db	= $this->getDbo();
		$query = $db->getQuery(true);
        $query->select($db->quoteName('title'))
            ->from($db->quoteName('#__visforms'))
            ->where('id='.$this->fid);
        $db->setQuery($query);
        $title = $db->loadResult();
        return $title;
    }

    public function getSubmitFieldCount() {
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
        $query->select($db->qn('id'))
            ->from($db->qn('#__visfields'))
            ->where($db->qn('typefield') . '=' . $db->q('submit'));
        $db->setQuery($query);
        try {
            $result = $db->loadColumn();
            $count = count($result);
        } catch (RuntimeException $e) {
            $count = 0;
        }
        return $count;
    }
}