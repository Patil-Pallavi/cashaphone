<?php
/**
 * Visforms html for form footer without summary page
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

if (!empty($displayData)) :
    if (isset($displayData['form']) && isset($displayData['nbFields']) && isset($displayData['hasRequired'])) :
        $form = $displayData['form'];
        $nbFields = $displayData['nbFields'];
        $hasRequired = $displayData['hasRequired'];
        $backButtonText = (!empty($form->backbtntext)) ? $form->backbtntext : JText::_('COM_VISFORMS_STEP_BACK');
        if (!empty($form->mpdisplaytype) && !empty($form->accordioncounter))
        {
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        //Explantion for * if at least one field is requiered above captcha
        if ($hasRequired == true && $form->required == 'captcha')
        {
            echo JHtml::_('visforms.getRequired', $form);
        }
        if (isset($form->captcha) && ($form->captcha == 1 || $form->captcha == 2))
        {
            echo JHtml::_('visforms.getCaptchaHtml', $form);
        }
        //Explantion for * if at least one field is requiered above submit
        if ($hasRequired == true && $form->required == 'bottom')
        {
            echo JHtml::_('visforms.getRequired', $form);
        }
        echo '<div class="clearfix"></div>';
        if (empty($form->hasBt3Layout))
        {
            echo '<div class="form-actions">';
        }
        if (!empty($form->steps) && (int) $form->steps > 1)
        {
            echo ' <input type="button" class="btn back_btn" value="' . $backButtonText . '" /> ';
        }
        for ($i=0;$i < $nbFields; $i++)
        {
            $field = $form->fields[$i];
            if (isset($field->isButton) && $field->isButton === true)
            {
                echo $field->controlHtml;
            }
        }
        if (empty($form->hasBt3Layout))
        {
            echo '</div>';
        }
    endif;
endif; ?>