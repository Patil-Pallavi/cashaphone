<?php
/**
 * Visformsdata detail view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

//no direct access
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
if (empty($this->item)) {
	$app->enqueueMessage(JText::_('COM_VISFORMS_FORM_DATA_RECORD_SET_MISSING'), 'error');
	return;
}
if (empty($this->item->published)) {
	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}
$menuitems = $app->getMenu()->getItems('link', 'index.php?option=com_visforms&view=visformsdata&layout=data&id=' . $this->id);
$this->labelHtmlTag = 'td';
$this->valueHtmlTag = 'td';
$this->labelClass = 'vfdvlabel';
$this->valueClass = 'vfdvvalue';
$this->extension = 'component';
$this->viewType = 'row';
?>
<div class="visforms-form<?php echo $this->menu_params->get('pageclass_sfx'); ?> visforms-data com-visforms com-visforms-detail"><?php
    if ($this->menu_params->get('show_page_heading') == 1) {
		if (!$this->menu_params->get('page_heading') == "") { ?>
            <h1><?php echo $this->menu_params->get('page_heading'); ?></h1><?php
		} else if (!empty($this->form->frontdetailtitle)) {
			echo '<h1>' . $this->form->frontdetailtitle . '</h1>';
		} else if (!empty($this->form->fronttitle)) {
			echo '<h1>' . $this->form->fronttitle . '</h1>';
		} else {
			echo '<h1>' . $this->form->title . '</h1>';
		}
	}
	foreach ($menuitems as $item) {
		if (isset($item->id) && ($item->id == $this->itemid)) {
			$linkback = "index.php?option=com_visforms&view=visformsdata&layout=data&Itemid=" . $this->itemid . "&id=" . $this->id;
			echo '<a class="btn" href="' . JRoute::_($linkback) . '">';
			echo JText::_('COM_VISFORMS_BACK_TO_LIST');
			echo '</a>';
			break;
		}
	}
	echo $this->loadTemplate('detailtable');
	?>
    <?php
    if ($this->form->poweredby == '1') { ?>
        <div id="vispoweredby"><a href="http://vi-solutions.de" target="_blank"><?php echo JText::_('COM_VISFORMS_POWERED_BY'); ?></a></div><?php
    } ?>
</div>