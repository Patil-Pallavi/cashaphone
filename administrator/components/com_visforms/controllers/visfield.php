<?php
/**
 * @author       Aicha Vack
 * @package     Joomla.Administrator
 * @subpackage  com_content
 * @link         http://www.vi-solutions.de
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die( 'Restricted access' );

class VisformsControllerVisfield extends JControllerForm
{
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	
	public function batch($model = null) {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		// set the model
		$model = $this->getModel('Visfield', '', array());
		// preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_visforms&view=visfields' . $this->getRedirectToListAppend(), false));
		return parent::batch($model);
	}
	
	
	protected function postSaveHook(JModelLegacy $model, $validData = array()) {
		$model->assureCreateDataTableFields();
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
		$jVersion = new JVersion();
		$fid = $this->input->getInt('fid', 0);
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&fid=' . $fid;
		if (version_compare($jVersion->getShortVersion(), '3.6.0', 'ge')) {
			$append .= '&extension=com_visforms.visform.'.$fid;
		}
		return $append;
	}
	
	protected function getRedirectToListAppend() {
		$fid = $this->input->getInt('fid', 0);
		$append = '';
		// setup redirect info
		if ($fid != 0) {
			$append .= '&fid=' . $fid;
		}
		parent::getRedirectToListAppend();
		return $append;
	}
	
	protected function allowEdit($data = array(), $key = 'id') {
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$fid = $fid = $this->input->getInt('fid');
		$assetId = 'com_visforms.visform.' . $fid . '.visfield.' . $recordId;
		$user = JFactory::getUser();
		$userId = $user->get('id');
		// check general edit permission first
		if ($user->authorise('core.edit', $assetId)) {
			return true;
		}

		// fallback on edit.own
		// first test if the permission is available
		if ($user->authorise('core.edit.own', $assetId)) {
			// now test the owner is the user
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId) {
				// need to do a lookup from the model
				$record = $this->getModel()->getItem($recordId);
				if (empty($record)) {
					return false;
				}
				$ownerId = $record->created_by;
			}

			// if the owner matches 'me' then do the test
			if ($ownerId == $userId) {
				return true;
			}
		}
		
		return false;
	}
}