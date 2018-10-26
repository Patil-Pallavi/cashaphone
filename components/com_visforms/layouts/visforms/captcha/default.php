<?php
/**
 * Visforms captcha html for default layout
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
            $html[] = '<div class="captchaCont required">';
            //Create a div with the right class where we can put the validation errors into
            $html[] = '<div class="fc-tbxrecaptcha_response_field"></div>';
            //showcaptchalabe == 0: show label!
            $html[] = (!(isset($form->showcaptchalabel)) || ($form->showcaptchalabel == 0)) ? '<label class ="visCSSlabel" id="captcha-lbl" for="recaptcha_response_field">' . JHtmlVisforms::createCaptchaTip($form) . '</label>' : '<label class ="asterix-ancor visCSSlabel"></label>';
            if ($clear)
            {
                $html[] = '<div class="clr"> </div>';
            }
            switch ($form->captcha)
            {
                case 1 :
                    $html[] = '<img id="captchacode' . $form->id . '" class="captchacode" src="index.php?option=com_visforms&task=visforms.captcha&sid=c4ce9d9bffcf8ba3357da92fd49c2457&id=' . $form->id . '" align="absmiddle"> &nbsp; ';

                    $html[] = '<img alt="' . JText::_('COM_VISFORMS_REFRESH_CAPTCHA') . '" class="captcharefresh' . $form->id . '" src="' . JURI::root(true) . '/components/com_visforms/captcha/images/refresh.gif' . '" align="absmiddle"> &nbsp;';
                    $html[] = '<input class="visCSStop10" type="text" id="recaptcha_response_field" name="recaptcha_response_field" />';
                    break;
                case 2:
                    $captcha = JCaptcha::getInstance('recaptcha');
                    $html[] = $captcha->display(null, 'dynamic_recaptcha_1', 'required');
                    break;
                default :
                    return '';
            }
            $html[] = '</div>';
        }
        echo implode('', $html);
    endif;  
endif; ?>

        