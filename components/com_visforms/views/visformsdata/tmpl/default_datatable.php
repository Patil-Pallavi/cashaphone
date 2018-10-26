<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$visformsVersion = JHTMLVisforms::getVersion();
$aefAllowFrontendDataEdit = VisformsAEF::checkAEF(VisformsAEF::$allowfrontenddataedit);
$canEdit =  ($this->canDo->get('core.edit.data') && $aefAllowFrontendDataEdit);
$canEditOwn =  ($this->canDo->get('core.edit.own.data') && $aefAllowFrontendDataEdit);
$canPublish = ($this->canDo->get('core.edit.data.state') && $aefAllowFrontendDataEdit && (version_compare($visformsVersion, '3.7.1', 'ge')));
$layout = $this->getLayout();
$userId = JFactory::getUser()->get('id');
$redirectUri  = '&return=' . strtr(base64_encode(JUri::getInstance()->toString()), '+/=', '-_,');
$i = 0;
?>

<table class="visdatatabledatavertical visdata visdatatable jlist-table<?php
if (isset($this->menu_params['show_tableborder']) && $this->menu_params['show_tableborder'] == 1) {
	echo " visdatatableborder";
}
if (isset($this->menu_params['viewclass'])) {
	echo $this->menu_params['viewclass'];
} ?>
    "><?php
	if (isset($this->menu_params['show_columnheader']) && $this->menu_params['show_columnheader'] == 1) { ?>
		<thead>
	<tr><?php
		if (!empty($this->form->displaycounter)) {
			echo '<'.$this->labelHtmlTag.'></'.$this->labelHtmlTag.'>';
		}
		echo JLayoutHelper::render('visforms.datas.labels.id', array('form' => $this->form, 'label' => 'COM_VISFORMS_ID', 'name' => 'displayid', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-id', 'context' => $this->context), null, array('component' => 'com_visforms'));
		if ($layout === 'dataeditlist') {
			if ($canEdit || $canEditOwn) {
				echo '<'.$this->labelHtmlTag.' width="3%" class="'.$this->labelClass.' data-edit"></'.$this->labelHtmlTag.'>';
			}
			if ($canPublish) {
				echo '<'.$this->labelHtmlTag.' class="'.$this->labelClass.' data-publish"></'.$this->labelHtmlTag.'>';
			}
		}
		foreach ($this->fields as $rowField) {
			if (!empty($rowField->useassearchfieldonly)) {
				continue;
			}
			if (isset($rowField->frontdisplay) && ($rowField->frontdisplay == 1 || $rowField->frontdisplay == 2)) {
				echo JLayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => $rowField->label, 'dbName' => 'F'. $rowField->id, 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-f' . $rowField->id, 'context' => $this->context, 'unSortable' =>  $rowField->unSortable), null, array('component' => 'com_visforms'));
			}
		}
		echo JLayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => JText::_('COM_VISFORMS_IP_ADDRESS'), 'name' => 'displayip', 'dbName' => 'ipaddress', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-ip', 'context' => $this->context), null, array('component' => 'com_visforms'));
		echo JLayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => JText::_('COM_VISFORMS_MODIFIED'), 'name' => 'displayismfd', 'dbName' => 'ismfd', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-mfd', 'context' => $this->context), null, array('component' => 'com_visforms'));
		echo JLayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => JText::_('COM_VISFORMS_SUBMISSIONDATE'), 'name' => 'displaycreated', 'dbName' => 'created', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-created', 'context' => $this->context), null, array('component' => 'com_visforms'));
		echo JLayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => JText::_('COM_VISFORMS_MODIFICATION_DATE'), 'name' => 'displaymodifiedat', 'dbName' => 'modified', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-modifiedat', 'context' => $this->context), null, array('component' => 'com_visforms'));?>
	</tr>
		</thead><?php
	}
	foreach ($this->items as $row) {
		$link = JRoute::_( 'index.php?option=com_visforms&view=visformsdata&layout='.$this->detailLinkLayout.'&id='.$this->id.'&cid='.$row->id.'&Itemid='.$this->itemid ); ?>
		<tr class="sectiontableentry1"><?php
		if (!empty($this->form->displaycounter)) {
			echo '<'.$this->valueHtmlTag.'>'.++$this->displayCounter.'</'.$this->valueHtmlTag.'>';
		}
		echo JLayoutHelper::render('visforms.datas.fields.id', array('form' => $this->form, 'data' => $row, 'link' => $link, 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-id', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));
		if ($layout === 'dataeditlist') {
			if ($canEdit ||$canEditOwn) {
				$editUrl = JUri::base() . 'index.php?option=com_visforms&view=edit&layout=edit&task=edit.editdata&id=' . (int) $this->form->id . '&cid=' . (int) $row->id . $redirectUri . '&Itemid='.$this->itemid;
				echo '<'.$this->valueHtmlTag.'>';
				if (($canEdit || (isset($row->created_by) && $row->created_by == $userId)) && ((!empty($row->published)) || ((empty($row->published)) && $canPublish))) {
					echo '<a class="hasTooltip" href="' . $editUrl . '" data-original-title="'.JText::_('COM_VISFORMS_EDIT').'"><i class="visicon-edit"></i></a>';
				}
				echo '</'.$this->valueHtmlTag.'>';
			}
			if ($canPublish) {
				echo '<'.$this->valueHtmlTag.'>';
				echo '<input id="cb'.$i.'" type="checkbox" onclick="Joomla.isChecked(this.checked, document.getElementById(\''.$this->context.'adminForm\');" value="'.$row->id.'" name="cid[]" style="display:none;">';
				if ($row->published) { ?>
				<a class="btn btn-micro active hasTooltip" title="" onclick="return vflistItemTask('cb<?php echo $i; ?>','visformsdata.unpublish')" href="javascript:void(0);" data-original-title="<?php echo JText::_('JLIB_HTML_UNPUBLISH_ITEM'); ?>">
						<span class="icon-publish"></span>
					</a><?php
				} else { ?>
				<a class="btn btn-micro active hasTooltip" title="" onclick="return vflistItemTask('cb<?php echo $i; ?>','visformsdata.publish')" href="javascript:void(0);" data-original-title="<?php echo JText::_('JLIB_HTML_PUBLISH_ITEM'); ?>">
						<span class="icon-unpublish"></span>
					</a><?php
				}
				echo '</'.$this->valueHtmlTag.'>';
			}
			$i++;
		}
		foreach ($this->fields as $rowField) {
			if (!empty($rowField->useassearchfieldonly)) {
				continue;
			}
			if (isset($rowField->frontdisplay) && ($rowField->frontdisplay == 1 || $rowField->frontdisplay == 2)) {
				$prop="F".$rowField->id;
				$texte = (isset($row->$prop)) ? $row->$prop : '';
				echo $texte = JLayoutHelper::render('visforms.datas.fields', array('form' => $this->form, 'field' => $rowField, 'data' => $row, 'text' => $texte, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-f' . $rowField->id, 'extension' => $this->extension, 'view' => 'list', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));
			}
		}
		echo JLayoutHelper::render('visforms.datas.fields.defaultoverhead', array('form' => $this->form, 'text' => $row->ipaddress, 'name' => 'displayip', 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-ip', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));
		echo JLayoutHelper::render('visforms.datas.fields.ismfd', array('form' => $this->form, 'text' => $row->ismfd, 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-mfd', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));
		echo JLayoutHelper::render('visforms.datas.fields.created', array('form' => $this->form, 'data' => $row, 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-created', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));

		echo JLayoutHelper::render('visforms.datas.fields.modifiedat', array('form' => $this->form, 'data' => $row, 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-modifiedat', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));?>

		</tr><?php
	} ?>
</table>