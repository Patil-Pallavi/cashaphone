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

JHTMLVisforms::includeScriptsOnlyOnce();

if ($this->visforms->published != '1')
{
    return;
}
?>

<form action="<?php echo JRoute::_($this->formLink); ?>" method="post" name="visform"
      id="<?php echo $this->visforms->parentFormId; ?>"
      class="visform <?php echo $this->visforms->formCSSclass; ?>"<?php if ($this->upload == true)
{ ?> enctype="multipart/form-data"<?php } ?>>
    <?php
    //add a progressbar
    if (((!empty($this->visforms->displaysummarypage)) || ($this->steps > 1)) && (!empty($this->visforms->displayprogress)))
    {
        $layout = new JLayoutFile('visforms.progress.default', null);
        $layout->setOptions(array('component' => 'com_visforms'));
        echo $layout->render(array('parentFormId' => $this->visforms->parentFormId, 'steps' => $this->steps, 'displaysmallbadges' => $this->visforms->displaysmallbadges, 'displaysummary' => $this->visforms->displaysummarypage));

    }
    for ($f = 1; $f < $this->steps + 1; $f++)
    {
        $active = ($f === 1) ? ' active' : '';
        echo '<fieldset class="fieldset-' . $f . $active . '">';
        if ($f === 1)
        {
            //Explantion for * if at least one field is requiered at the top of the form
            if ($this->required == true && $this->visforms->required == 'top')
            {
                ?>
                <div class="vis_mandatory visCSSbot10 visCSStop10"><?php echo JText::_('COM_VISFORMS_REQUIRED'); ?>*
                </div>
                <?php
            }

            //first hidden fields at the top of the form
            for ($i = 0; $i < $this->nbFields; $i++)
            {
                $field = $this->visforms->fields[$i];
                if ($field->typefield == "hidden")
                {
                    echo $field->controlHtml;
                }
            }
        }

        //then inputs, textareas, selects and fieldseparators
        for ($i = 0; $i < $this->nbFields; $i++)
        {
            $field = $this->visforms->fields[$i];
            if ($field->typefield != "hidden" && !isset($field->isButton) && ($field->fieldsetcounter === $f))
            {

                //set focus to first visible field
                if ((!empty($this->setFocus)) && ($this->firstControl == true) && ((!(isset($field->isDisabled))) || ($field->isDisabled == false)))
                {
                    $script = '';
                    $script .= 'jQuery(document).ready( function(){';
                    $script .= 'jQuery("#' . $field->errorId . '").focus();';
                    $script .= '});';
                    $doc = JFactory::getDocument();
                    $doc->addScriptDeclaration($script);
                    $this->firstControl = false;
                }

                //display the control
                echo $field->controlHtml;
            }
        }
        if ($f === $this->steps)
        {
            //no summary page
            if (empty($this->visforms->displaysummarypage))
            {
                $layout = new JLayoutFile('visforms.footers.default.nosummary', null);
                $layout->setOptions(array('component' => 'com_visforms'));
                echo $layout->render(array('form' => $this->visforms, 'nbFields' => $this->nbFields, 'hasRequired' => $this->required));
            }
            //with summary page
            else
            {
                $layout = new JLayoutFile('visforms.footers.default.withsummary', null);
                $layout->setOptions(array('component' => 'com_visforms'));
                echo $layout->render(array('form' => $this->visforms, 'nbFields' => $this->nbFields, 'hasRequired' => $this->required, 'summarypageid' => $this->visforms->parentFormId));
            }
        }
        echo '</fieldset>';
    }
    ?>
    <input type="hidden" value="<?php echo $this->visforms->id; ?>" name="postid"/>
    <input type="hidden" value="pagebreak" name="addSupportedFieldType[]" />
    <?php
    $input = JFactory::getApplication()->input;
    $tmpl = $input->get('tmpl');
    if (isset($tmpl))
    {
        echo '<input type="hidden" value="' . $tmpl . '" name="tmpl" />';
    }
    $creturn = $input->get('creturn');
    if (isset($creturn))
    {
        echo '<input type="hidden" value="' . $creturn . '" name="creturn" />';
    }
    if (!empty($this->return))
    {
        echo '<input type="hidden" value="' . $this->return . '" name="return" />';
    }
    ?>
    <?php echo JHtml::_('form.token'); ?>
</form>
