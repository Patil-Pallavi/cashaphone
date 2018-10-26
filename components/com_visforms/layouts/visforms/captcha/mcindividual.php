<?php
/**
 * Visforms captcha html for multi column layout
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
require_once JPATH_ADMINISTRATOR.'/components/com_visforms/helpers/html/visforms.php';

if (!empty($displayData)) : 
    if (isset($displayData['form'])) :
        $form  = $displayData['form'];
        $clear = (!empty($displayData['clear'])) ? true : false;
        $html = array();
        if (isset($form->captcha))
        {
            $captchaHeight = ((!empty($form->viscaptchaoptions)) && (!empty($form->viscaptchaoptions['image_height'])) && (!($form->captcha == 2))) ? (int)$form->viscaptchaoptions['image_height'] : 0;
            $styleHeight = (!empty($captchaHeight)) ? 'style="height: ' . $captchaHeight . 'px; line-height: ' . $captchaHeight . 'px;" ' : '';
            $labelcolspan = (($form->captcha == 2) && (!empty($form->grecaptcha2label_bootstrap_size))) ? (int) $form->grecaptcha2label_bootstrap_size : ((empty($form->showcaptchalabel)) ? 3 : 1);
            $colspan = 12 - $labelcolspan;
            //Create a div with the right class where we can put the validation errors into
            $html[] = '<div class="fc-tbxrecaptcha_response_field"></div>';
            $html[] = '<div class="row-fluid required">';
            $html[] = (!(isset($form->showcaptchalabel)) || ($form->showcaptchalabel == 0)) ? '<label class="span'.$labelcolspan.'" ' . $styleHeight . 'id="captcha-lbl" for="recaptcha_response_field">' . JHtmlVisforms::createCaptchaTip($form) . '</label>' : '<span class ="span'.$labelcolspan.' asterix-ancor"></span>';
            switch ($form->captcha)
            {
                case 1 :
                    $html[] = '<div class="span5">';
                    $html[] = '<img id="captchacode' . $form->id . '" class="captchacode" src="index.php?option=com_visforms&task=visforms.captcha&sid=c4ce9d9bffcf8ba3357da92fd49c2457&id=' . $form->id . '" align="absmiddle"> &nbsp; ';

                    $html[] = '<img alt="' . JText::_('COM_VISFORMS_REFRESH_CAPTCHA') . '" class="captcharefresh' . $form->id . '" src="' . JURI::root(true) . '/components/com_visforms/captcha/images/refresh.gif' . '" align="absmiddle"> &nbsp;';
                    $html[] = '</div><div class="span4" '.$styleHeight.'>';
                    $html[] = '<input class="visCSStop10" type="text" id="recaptcha_response_field" name="recaptcha_response_field" />';
                    $html[] = '</div>';
                    break;
                case 2:
                    $captcha = JCaptcha::getInstance('recaptcha');
                    $html[] = $captcha->display(null, 'dynamic_recaptcha_1', 'span'. $colspan . ' required');
                    break;
                default :
                    return '';
            }
            $html[] = '</div>';
        }
        echo implode('', $html);
    endif;  
endif; ?>

        