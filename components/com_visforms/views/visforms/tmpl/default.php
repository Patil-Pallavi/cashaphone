<?php
/**
 * Visforms default view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if ($this->visforms->published != '1') {
    return;
}

$this->nbFields = count($this->visforms->fields);
//get some infos to look whether it's neccessary to add Javascript or special HTML-Code or not
//variables are set to true if they are true for at least one field
$this->required = false;
$this->upload = false;
$this->textareaRequired = false;
$this->hasHTMLEditor = false;
//helper, used to set focus on first visible field
$this->firstControl = true;

for ($i = 0; $i < $this->nbFields; $i++) {
    $field = $this->visforms->fields[$i];
    //set the control variables
    if (isset($field->attribute_required) && ($field->attribute_required == "required")) {
        $this->required = true;
    }
    if (isset($field->typefield) && $field->typefield == "file") {
        $this->upload = true;
    }
    if (isset($field->textareaRequired) && $field->textareaRequired === true) {
        $this->textareaRequired = true;
    }
    if (isset($field->hasHTMLEditor) && $field->hasHTMLEditor == true) {
        $this->hasHTMLEditor = true;
    }
}
?>

<div class="visforms-form<?php echo $this->menu_params->get('pageclass_sfx'); ?>" id="visformcontainer"><?php
    if (isset($this->visforms->errors) && is_array($this->visforms->errors) && count($this->visforms->errors) > 0) {
        $layout = new JLayoutFile('visforms.error.messageblock', null);
        $layout->setOptions(array('component' => 'com_visforms'));
        echo $layout->render(array('errormessages' => $this->visforms->errors, 'context' => 'form'));
    }

    if ($this->menu_params->get('show_page_heading') == 1) {
        if (!$this->menu_params->get('page_heading') == "") { ?>
            <h1><?php echo $this->menu_params->get('page_heading'); ?></h1><?php
        } else { ?>
            <h1><?php echo $this->visforms->title; ?></h1><?php
        }
    }
    $layout = new JLayoutFile('visforms.success.messageblock', null);
    $layout->setOptions(array('component' => 'com_visforms'));
    echo $layout->render(array('message' => $this->successMessage, 'parentFormId' => $this->visforms->parentFormId)); ?>

    <div class="alert alert-danger error-note" style="display: none;"></div><?php
        $layout = new JLayoutFile('visforms.scripts.validation', null);
        $layout->setOptions(array('component' => 'com_visforms'));
        echo $layout->render(array('visforms' => $this->visforms, 'textareaRequired' => $this->textareaRequired, 'hasHTMLEditor' => $this->hasHTMLEditor, 'parentFormId' => $this->visforms->parentFormId, 'steps' => $this->steps));

    if (strcmp($this->visforms->description, "") != 0) { ?>
        <div class="category-desc"><?php
            JPluginHelper::importPlugin('content');
            echo JHtml::_('content.prepare', $this->visforms->description); ?>
        </div><?php
    }

    //display form with appropriate layout
    switch ($this->visforms->formlayout) {
        case 'btdefault' :
        case 'bthorizontal' :
        case 'bt3default' :
        case 'bt3horizontal' :
            echo $this->loadTemplate('btdefault');
            break;
        case  'mcindividual' :
        case  'bt3mcindividual' :
            echo $this->loadTemplate('mcindividual');
            break;
        default :
            echo $this->loadTemplate('visforms');
            break;
    }

    if ($this->visforms->poweredby == '1') {
        echo JHtml::_('visforms.creditsFrontend');
    }
    if (!empty($this->visforms->showmessageformprocessing)) { ?>
    <div id="<?php echo $this->visforms->parentFormId; ?>_processform" style="display:none"><div class="processformmessage"><?php
            echo $this->visforms->formprocessingmessage; ?>
        </div></div><?php
    }
    $layout = new JLayoutFile('visforms.scripts.map', null);
    $layout->setOptions(array('component' => 'com_visforms'));
    echo $layout->render(array('form' => $this->visforms));
    ?>

</div>
