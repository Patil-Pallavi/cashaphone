<?php
/**
 * Mod Visforms Form
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

JHTMLVisforms::includeScriptsOnlyOnce();
	
if ($visforms->published != '1') 
{
    return;
}


?>

<form action="<?php echo Juri::base(true) . '/' . htmlspecialchars($formLink, ENT_COMPAT, 'UTF-8'); ?>" method="post" name="visform" id="<?php echo $visforms->parentFormId; ?>" class="visform <?php echo $visforms->formCSSclass; ?>"<?php if($upload == true) { ?> enctype="multipart/form-data"<?php } ?>>
<?php
//add a progressbar
if (((!empty($visforms->displaysummarypage)) || ($steps > 1)) && (!empty($visforms->displayprogress)))
{
	$layout = new JLayoutFile('visforms.progress.default', null);
	$layout->setOptions(array('component' => 'com_visforms'));
	echo $layout->render(array('parentFormId' => $visforms->parentFormId, 'steps' => $steps, 'displaysmallbadges' => $visforms->displaysmallbadges, 'displaysummary' => $visforms->displaysummarypage));
}
for ($f = 1; $f < $steps + 1; $f++)
{
	$active = ($f === 1) ? ' active' : '';
	echo '<fieldset class="fieldset-' . $f . $active . '">';
	if ($f === 1)
	{
		//Explantion for * if at least one field is requiered at the top of the form
		if ($required == true && $visforms->required == 'top')
		{
			?>
			<div class="vis_mandatory visCSSbot10 visCSStop10"><?php echo JText::_('COM_VISFORMS_REQUIRED'); ?> *</div>
			<?php
		}

		//first hidden fields at the top of the form
		for ($i = 0; $i < $nbFields; $i++)
		{
			$field = $visforms->fields[$i];
			if ($field->typefield == "hidden")
			{
				echo $field->controlHtml;
			}
		}
	}

	//then inputs, textareas, selects and fieldseparators
	for ($i=0;$i < $nbFields; $i++)
	{ 
        $field = $visforms->fields[$i];
        if ($field->typefield != "hidden" && !isset($field->isButton) && ($field->fieldsetcounter === $f))
        {
			//set focus to first visible field
			if ((!empty($setFocus)) && ($firstControl == true) && ((!(isset($field->isDisabled))) || ($field->isDisabled == false)))
			{
				$script = '';
				$script .= 'jQuery(document).ready( function(){';
				$script .= 'jQuery("#' . $field->errorId . '").focus();';
				$script .= '});';
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration($script);
				$firstControl = false;
			}
            //display the control
            echo $field->controlHtml;
        }   	
    }
    if ($f === $steps)
    {
        //no summary page
        if (empty($visforms->displaysummarypage))
        {
            $layout = new JLayoutFile('visforms.footers.default.nosummary', null);
            $layout->setOptions(array('component' => 'com_visforms'));
            echo $layout->render(array('form' => $visforms, 'nbFields' => $nbFields, 'hasRequired' => $required));
        }
        //with summary page
        else
        {
            $layout = new JLayoutFile('visforms.footers.default.withsummary', null);
            $layout->setOptions(array('component' => 'com_visforms'));
            echo $layout->render(array('form' => $visforms, 'nbFields' => $nbFields, 'hasRequired' => $required, 'summarypageid' => $visforms->parentFormId));
        }
    }
	echo '</fieldset>';
}
?>
    <input type="hidden" name="return" value="<?php echo $return; ?>" />
	<input type="hidden" value="<?php echo $visforms->id; ?>" name="postid" />
	<input type="hidden" value="<?php echo $context; ?>" name="context" />
	<input type="hidden" value="pagebreak" name="addSupportedFieldType[]" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
