<?php
/**
 * Mod_Visforms Form
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   mod_visforms
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 
	
if ($visforms->published != '1') {
    return;
    
}

//retrieve helper variables from params
$nbFields=$params->get('nbFields');
$required = $params->get('required');
$upload = $params->get('upload');
$textareaRequired = $params->get('textareaRequired');
$hasHTMLEditor = $params->get('hasHTMLEditor');
$return = JHTMLVisforms::base64_url_encode(JUri::getInstance()->toString());
$firstControl = $params->get('firstControl');
$setFocus = $params->get('setFocus');
$steps = $params->get('steps');
$context = $params->get('context');
$successMessage = $params->get('successMessage');
?>

<div class="visforms-form"><?php
    if (isset($visforms->errors) && is_array($visforms->errors) && count($visforms->errors) > 0) {
        $layout = new JLayoutFile('visforms.error.messageblock', null);
        $layout->setOptions(array('component' => 'com_visforms'));
        echo $layout->render(array('errormessages' => $visforms->errors, 'context' => 'form'));
    }

    if ($menu_params->get('show_title') == 1) {?>
		<h1><?php echo $visforms->title; ?></h1><?php
	}

    $layout = new JLayoutFile('visforms.success.messageblock', null);
	$layout->setOptions(array('component' => 'com_visforms'));
	echo $layout->render(array('message' => $successMessage, 'parentFormId' => $visforms->parentFormId)); ?>

    <div class="alert alert-danger error-note" style="display: none;"></div><?php
    $layout = new JLayoutFile('visforms.scripts.validation', null);
    $layout->setOptions(array('component' => 'com_visforms'));
    echo $layout->render(array('visforms' => $visforms, 'textareaRequired' => $textareaRequired, 'hasHTMLEditor' => $hasHTMLEditor, 'parentFormId' => $visforms->parentFormId, 'steps' => $steps));
    if (strcmp ( $visforms->description , "" ) != 0) { ?>
        <div class="category-desc"><?php
            JPluginHelper::importPlugin('content');
            echo JHtml::_('content.prepare', $visforms->description); ?>
        </div><?php
    }

    //display form with appropriate layout
    switch($visforms->formlayout) {
        case 'btdefault' :
        case 'bthorizontal' :
        case 'bt3default' :
        case 'bt3horizontal' :
            require JModuleHelper::getLayoutPath('mod_visforms', $params->get('layout', 'default') . '_btdefault');
            break;
        case  'mcindividual' :
        case  'bt3mcindividual' :
            require JModuleHelper::getLayoutPath('mod_visforms', $params->get('layout', 'default') . '_mcindividual');
            break;
        default :
            require JModuleHelper::getLayoutPath('mod_visforms', $params->get('layout', 'default') . '_visforms');
            break;
    }

    if ($visforms->poweredby == '1') {
        echo JHtml::_('visforms.creditsFrontend');
    }
    if (!empty($visforms->showmessageformprocessing)) { ?>
        <div id="<?php echo $visforms->parentFormId; ?>_processform" style="display:none"><div class="processformmessage"><?php
                echo $visforms->formprocessingmessage; ?>
            </div></div><?php
    }
	$layout = new JLayoutFile('visforms.scripts.map', null);
	$layout->setOptions(array('component' => 'com_visforms'));
	echo $layout->render(array('form' => $visforms)); ?>
</div>
