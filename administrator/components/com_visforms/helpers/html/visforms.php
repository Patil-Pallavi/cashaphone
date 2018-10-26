<?php
/**
 * JHTMLHelper for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');


class JHTMLVisforms
{
    //array of loaded css and javascirpt files
    protected static $loaded = array();

    public static function creditsBackend() {
        $html = '<div class="row-fluid"><div class="visformbottom span11 well" style="padding-top: 15px; color: #999; text-align: center;margin-top:40px;">Visforms Version ' . self::getVersion() . ', &copy; 2012 - ' . self::getCopyRightDate() . ' by <a href="http://vi-solutions.de" target="_blank" class="smallgrey">vi-solutions</a>, all rights reserved. visForms is Free Software released under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank" class="smallgrey">GNU/GPL License</a>.</div></div>';
        return $html;
    }

    public static function creditsFrontend() {
        return '<div id="vispoweredby"><a href="http://vi-solutions.de" target="_blank">' . JText::_('COM_VISFORMS_POWERED_BY') . '</a></div>';
    }

    public static function getVersion() {
        $xml_file = JPath::clean(JPATH_ADMINISTRATOR . '/components/com_visforms/visforms.xml');
        $installed_version = '1.0.0';
        if (file_exists($xml_file)) {
            //supress warnings
            libxml_use_internal_errors(true);
            $xml = JFactory::getXML($xml_file);
            $installed_version = $xml->version;
        }
        return $installed_version;
    }

    public static function getFrontendDataEditVersion() {
        return VisformsAEF::getVersion(VisformsAEF::$allowFrontEndDataEdit);
    }

    public static function getPluginFormViewVersion() {
        return VisformsAEF::getVersion(VisformsAEF::$vfFormView);
    }

    public static function checkMySubmissionsMenuItemExists() {
        //don't allow access to data edit view if there is not visforms data edit list menu item
        $app = JFactory::getApplication();
        $menuitems = $app->getMenu()->getItems('link', 'index.php?option=com_visforms&view=mysubmissions');
        if ((!(empty($menuitems))) && (is_array($menuitems)) && (!empty($menuitems[0]->id))) {
            return $menuitems[0]->id;
        }
        return false;
    }

	public static function checkDataViewMenuItemExists($id) {
		//don't allow access to data edit view if there is not visforms data edit list menu item
		$app = JFactory::getApplication();
		$id = (int) $id;
		$menuitems = $app->getMenu()->getItems('link', 'index.php?option=com_visforms&view=visformsdata&layout=dataeditlist&id=' . $id);
		if ((!(empty($menuitems))) && (is_array($menuitems)) && (!empty($menuitems[0]->id))) {
			return $menuitems[0]->id;
		}
		return false;
	}

    public static function getCopyRightDate() {
        $xml_file = JPath::clean(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_visforms' . DIRECTORY_SEPARATOR . 'visforms.xml');
        $crd = JHtml::_('date', 'now', 'Y');
        if (file_exists($xml_file)) {
            //supress warnings
            libxml_use_internal_errors(true);
            $xml = JFactory::getXML($xml_file);
            $cdate = $xml->creationDate;
        }
        return $crd;
    }

    public static function getRequired($form) {
        $html = array();
        switch ($form->formlayout) {
            case 'bthorizontal':
            case 'editbthorizontal' :
                $html[] = '<div class="control-group">';
                $html[] = '<label class="control-label vis_mandatory">' . JText::_('COM_VISFORMS_REQUIRED') . ' *</label>';
                $html[] = '</div>';
                break;
            case 'bt3horizontal':
            case 'editbt3horizontal':
                $html[] = '<div class="form-group">';
                $html[] = '<label class="control-label col-sm-3 vis_mandatory">' . JText::_('COM_VISFORMS_REQUIRED') . ' *</label>';
                $html[] = '</div>';
                break;
            default :
                $html[] = '<label class="vis_mandatory visCSSbot10 visCSStop10">' . JText::_('COM_VISFORMS_REQUIRED') . ' *</label>';
                break;
        }
        return implode('', $html);
    }

    public static function createTip($field) {
        $tip = array();
        $html = "";
        if (!empty($field->custominfo)) {
            $tip = explode('##', $field->custominfo, 2);
        }
        if ($tip) {
            if (!isset($tip[1])) {
                //tip has no title seperated by ##
                $html = JHtml::_('tooltip', $tip[0], '', '', $field->label);
            } else {
                //tip has text and title
                $html = JHtml::_('tooltip', $tip[1], $tip[0], '', $field->label);
            }
        } else {
            //return field label as text
            $html = $field->label;
        }
        return $html;
    }

    public static function createCaptchaTip($form) {
        $html = "";
        //Show Helptext in Tooltip
        $captchalabel = "Captcha";
        if (isset($form->captchalabel)) {
            $captchalabel = $form->captchalabel;
        }
        if (!empty($form->captchacustominfo)) {
            $html = JHtml::_('tooltip', $form->captchacustominfo, '', '', $captchalabel);
        } else {
            $html = $captchalabel;
        }
        return $html;
    }

    public static function getCaptchaHtml($form, $clear = false) {
        if (isset($form->captcha)) {
            switch ($form->formlayout) {
                case "visforms" :
                case "edit" :
                    return self::getCaptchaHtmlLayout($form, 'default', $clear);
                case "btdefault" :
                case "editbtdefault" :
                    return self::getCaptchaHtmlLayout($form, 'btdefault', $clear);
                case "bthorizontal" :
                case "editbthorizontal" :
                    return self::getCaptchaHtmlLayout($form, 'bthorizontal', $clear);
                case "mcindividual" :
                case "editmcindividual" :
                    return self::getCaptchaHtmlLayout($form,'mcindividual', $clear);
                case "bt3default" :
                case "editbt3default" :
                	$html = self::getCaptchaHtmlLayout($form,'bt3default', $clear);
                	if (!empty($html)) {
                		return $html;
	                }
                    return self::getBt3defaultLayoutCaptchaHtml($form, $clear);
                case "bt3horizontal" :
                case "editbt3horizontal" :
		            $html = self::getCaptchaHtmlLayout($form,'bt3horizontal', $clear);
		            if (!empty($html)) {
			            return $html;
		            }
                    return self::getBt3horizontalLayoutCaptchaHtml($form, $clear);
                case "bt3mcindividual" :
                case "editbt3mcindividual" :
		            $html = self::getCaptchaHtmlLayout($form,'bt3mcindividual', $clear);
		            if (!empty($html)) {
			            return $html;
		            }
                    return self::getBt3mcindividualLayoutCaptchaHtml($form, $clear);
                default :
                    break;
            }
        }
        return "";
    }

    protected static function getCaptchaHtmlLayout($form, $layout = "default", $clear = false) {
	    $layout = new JLayoutFile("visforms.captcha.$layout", null);
	    $layout->setOptions(array('component' => 'com_visforms'));
	    $html = $layout->render(array('form' => $form, 'clear' => $clear));
	    return $html;
    }

    protected static function getBt3defaultLayoutCaptchaHtml($form, $clear = false) {
        $html = array();
        if (isset($form->captcha)) {
            //Create a div with the right class where we can put the validation errors into
            $html[] = '<div class="fc-tbxrecaptcha_response_field"></div>';
            //showcaptchalabe == 0: show label!
            $html[] = (!(isset($form->showcaptchalabel)) || ($form->showcaptchalabel == 0)) ? '<div class="required">' : '<div class="required asterix-ancor">';
            $html[] = '<div class="form-group">';
            $html[] = (!(isset($form->showcaptchalabel)) || ($form->showcaptchalabel == 0)) ? '<label id="captcha-lbl" for="recaptcha_response_field">' . self::createCaptchaTip($form) . '</label>' : '<span class ="asterix-ancor"></span>';
            if ($clear) {
                $html[] = '<div class="clr"> </div>';
            }
            switch ($form->captcha) {
                case 1 :
                    $html[] = '<img id="captchacode' . $form->id . '" class="captchacode" src="index.php?option=com_visforms&task=visforms.captcha&sid=c4ce9d9bffcf8ba3357da92fd49c2457&id=' . $form->id . '" align="absmiddle"> &nbsp; ';

                    $html[] = '<img alt="' . JText::_('COM_VISFORMS_REFRESH_CAPTCHA') . '" class="captcharefresh' . $form->id . '" src="' . JURI::root(true) . '/components/com_visforms/captcha/images/refresh.gif' . '" align="absmiddle"> &nbsp;';
                    $html[] = '<input class="" type="text" id="recaptcha_response_field" name="recaptcha_response_field" />';
                    break;
                case 2:
                    $captcha = JCaptcha::getInstance('recaptcha');
                    $html[] = $captcha->display(null, 'dynamic_recaptcha_1', 'required');
                    break;
                default :
                    return '';
            }
            $html[] = '</div>';
            $html[] = '</div>';
        }
        return implode('', $html);
    }

    protected static function getBt3horizontalLayoutCaptchaHtml($form, $clear = false) {
        $html = array();
        if (isset($form->captcha)) {
            $captchaHeight = ((!empty($form->viscaptchaoptions)) && (!empty($form->viscaptchaoptions['image_height'])) && (!($form->captcha == 2))) ? (int) $form->viscaptchaoptions['image_height'] : 0;
            $styleHeight = (!empty($captchaHeight)) ? 'style="height: '.$captchaHeight.'px; line-height: '.$captchaHeight.'px;" ' : '';
            //Create a div with the right class where we can put the validation errors into
            $html[] = '<div class="fc-tbxrecaptcha_response_field"></div>';
            $html[] = '<div class="form-group required">';
            //showcaptchalabe == 0: show label!
            $html[] = (!(isset($form->showcaptchalabel)) || ($form->showcaptchalabel == 0)) ? '<label class="col-sm-3 control-label" id="captcha-lbl" for="recaptcha_response_field" '.$styleHeight .'>' . self::createCaptchaTip($form) . '</label>' : '<span class="col-sm-3 control-label"></span>';
            $html[] = '<div class="col-sm-9">';
            switch ($form->captcha) {
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
            $html[] = '</div>';
        }
        return implode('', $html);
    }

    protected static function getBt3mcindividualLayoutCaptchaHtml($form, $clear = false) {
        $html = array();
        if (isset($form->captcha)) {
            $captchaHeight = ((!empty($form->viscaptchaoptions)) && (!empty($form->viscaptchaoptions['image_height'])) && (!($form->captcha == 2))) ? (int)$form->viscaptchaoptions['image_height'] : 0;
            $styleLabelHeight = (!empty($captchaHeight)) ? 'style="height: ' . $captchaHeight . 'px; line-height: ' . $captchaHeight . 'px;" ' : '';
            $paddingtop = (!empty($captchaHeight)) ? ($captchaHeight - 40)/2 : 0;
            $styleInputHeight = (!empty($captchaHeight)) ? 'style="height: ' . $captchaHeight . 'px;  padding-top: '. $paddingtop .'px;"' : '';

            $html[] = '<div class="row visCSSbot10">';
            $html[] = ($form->captcha == 2) ? '<div class="col-sm-12 required">': '<div class="col-sm-3 required">';
            $html[] = (!(isset($form->showcaptchalabel)) || ($form->showcaptchalabel == 0)) ? '<label ' . $styleLabelHeight . 'id="captcha-lbl" for="recaptcha_response_field">' . self::createCaptchaTip($form) . '</label>' : '<div class="form-group"><span class ="asterix-ancor"></span></div>';
            $html[] = '</div>';
            switch ($form->captcha) {
                case 1 :
                    $html[] = '<div class="col-sm-5">';
                    $html[] = '<img id="captchacode' . $form->id . '" class="captchacode" src="index.php?option=com_visforms&task=visforms.captcha&sid=c4ce9d9bffcf8ba3357da92fd49c2457&id=' . $form->id . '" align="absmiddle"> &nbsp; ';
                    $html[] = '<img alt="' . JText::_('COM_VISFORMS_REFRESH_CAPTCHA') . '" class="captcharefresh' . $form->id . '" src="' . JURI::root(true) . '/components/com_visforms/captcha/images/refresh.gif' . '" align="absmiddle"> &nbsp;';
                    $html[] = '</div><div class="col-sm-4" '. $styleInputHeight.'>';
                    $html[] = '<input class="form-control" type="text" id="recaptcha_response_field" name="recaptcha_response_field" />';
                    //Create a div with the right class where we can put the validation errors into
                    $html[] = '<div class="fc-tbxrecaptcha_response_field" style="position:absolute;bottom:100%;left:0;"></div>';
                    $html[] = '</div>';
                    break;
                case 2:
                    //Create a div with the right class where we can put the validation errors into
                    $html[] = '<div class="fc-tbxrecaptcha_response_field"></div>';
                    $captcha = JCaptcha::getInstance('recaptcha');
                    $html[] = $captcha->display(null, 'dynamic_recaptcha_1', 'col-sm-12 required');
                    break;
                default :
                    return '';
            }
            $html[] = '</div>';
        }
        return implode('', $html);
    }

    public static function getRestrictedId($restrict) {
        return preg_replace('/[^0-9]/', '', $restrict);
    }

	public static function getUploadFileFullPath($registryString) {
		//info about uploaded files are stored in a JSON Object. Earlier versions (up to visforms 1.0.4)just have a string folder/newfilename
		$registry = new JRegistry;
		$registry->loadString($registryString);
		$fileInfo = $registry->toArray();
		if (isset($fileInfo['folder'])) {

			return JUri::root() . $fileInfo['folder'] . '/' . $fileInfo['file'];
		} else {
			return JUri::root() . $registryString;
		}
	}

    public static function getUploadFileLink($registryString) {
	    //info about uploaded files are stored in a JSON Object. Earlier versions (up to visforms 1.0.4)just have a string folder/newfilename
        $registry = new JRegistry;
        $registry->loadString($registryString);
        $fileInfo = $registry->toArray();
        if (isset($fileInfo['folder'])) {
            //return link
            return '<a href="' . JUri::root() . $fileInfo['folder'] . '/' . $fileInfo['file'] . '" target="_blank">' . JUri::root() . $fileInfo['folder'] . '/' . $fileInfo['file'] . '</a>';
        } else {
	        return '<a href="' . JUri::root() . $registryString . '" target="_blank">' . JUri::root() . $registryString . '</a>';
        }
    }

    public static function getUploadFilePath($registryString) {
	    //info about uploaded files are stored in a JSON Object. Earlier versions (up to visforms 1.0.4)just have a string folder/newfilename
        $registry = new JRegistry;
        $registry->loadString($registryString);
        $fileInfo = $registry->toArray();
        if ((isset($fileInfo['file'])) && (isset($fileInfo['folder']))) {
            return $fileInfo['folder'] . '/' . $fileInfo['file'];
        } else {
            return $registryString;
        }
    }

    public static function getUploadFileName($registryString) {
	    //info about uploaded files are stored in a JSON Object. Earlier versions (up to visforms 1.0.4)just have a string folder/newfilename
        $registry = new JRegistry;
        $registry->loadString($registryString);
        $fileInfo = $registry->toArray();
        if (isset($fileInfo['file'])) {
            return $fileInfo['file'];
        } else {
            return basename($registryString);
        }
    }

    public static function getFileOrgName($registryString) {
	    //info about uploaded files are stored in a JSON Object. Earlier versions (up to visforms 1.0.4)just have a string folder/newfilename
	    $registry = new JRegistry;
	    $registry->loadString($registryString);
	    $fileInfo = $registry->toArray();
	    if (isset($fileInfo['file'])) {
		    $newName =  $fileInfo['file'];
	    } else {
		    $newName = basename($registryString);
	    }
	    //newName was created from the original file name plus _ plus microtimehash
	    if (!empty($newName)) {
	    	// get file extesion
		    $dotSplit = explode('.', $newName);
		    if (!empty($dotSplit)) {
			    $extension = array_pop($dotSplit);
			    // remove microtime hash
			    $newBaseName = implode ('.', $dotSplit);
			    if (!empty($extension) && !empty($newBaseName)) {
			    	$nameParts = explode('_', $newBaseName);
			    	if (!empty($nameParts)) {
			    		//remove microtime hash element from array
					    array_pop($nameParts);
					    // rebuild original file name
					    $orgBaseName = implode('_', $nameParts);
					    if (!empty($orgBaseName)) {
					    	return $orgBaseName . '.' . $extension;
					    }
				    }

			    }
		    }
	    }
	    return '';
    }

    public static function includeScriptsOnlyOnce($cssScripts = array('visforms' => true, 'bootstrapform' => false), $jsScripts = array('validation' => true)) {
        // Add css and js links
        $doc = JFactory::getDocument();
        if (!isset ($cssScripts['visforms.min'])) {
            $cssScripts['visforms.min'] = true;
        }
        //include all css files with "custom" in filename
        $customCSS = self::getCustomCssFileNameList();
        $cssScripts = array_merge($cssScripts, $customCSS);
        foreach ($cssScripts as $scriptName => $scriptValue) {
            if (empty(static::$loaded['cssFile'][$scriptName]) && $scriptValue) {
                $doc->addStyleSheet(JURI::root(true) . '/media/com_visforms/css/' . $scriptName . '.css');
                static::$loaded['cssFile'][$scriptName] = true;
            }
        }

        if ($jsScripts['validation'] == true) {
            //we use addCustomTag to load jQuery library and depending scripts. If already included they are stored in this array
            JHtml::_('jquery.framework');
            JHtml::_('script', 'media/com_visforms/js/jquery.validate.min.js', false, false, false, false);
            JHtml::_('script', 'media/com_visforms/js/visforms.js', false, false, false, false);
            self::getValidatorMessagesScript();
            self::getValidatorMethodsScript();
        }
    }

    public static function getValidatorMessagesScript () {
        if (!empty(static::$loaded[__METHOD__])) {
            return;
        }
        $script = 'jQuery(document).ready(function () {
            jQuery.extend(jQuery.validator.messages, {
            required: "' . addslashes(JText::_('COM_VISFORMS_ENTER_REQUIRED')). '",
            remote: "Please fix this field.",
            email: "' . addslashes(JText::_('COM_VISFORMS_ENTER_VALID_EMAIL')) .'",
            url: "' . addslashes(JText::_('COM_VISFORMS_ENTER_VALID_URL')) . '",
            date: "' . addslashes(JText::_('COM_VISFORMS_ENTER_VALID_DATE')) . '",
            dateISO: "Please enter a valid date (ISO).",
            number: "' . addslashes(JText::_('COM_VISFORMS_ENTER_VALID_NUMBER')) . '",
            digits: "' . addslashes(JText::_('COM_VISMORMS_ENTER_VALID_DIGIT')) . '",
            creditcard: "Please enter a valid credit card number.",
            equalTo: "'. addslashes(JText::_('COM_VISFORMS_ENTER_CONFIRM')) . '",
            maxlength: jQuery.validator.format("' . addslashes(JText::_('COM_VISFORMS_ENTER_VAILD_MAXLENGTH')) . '"),
            minlength: jQuery.validator.format("' . addslashes(JText::_('COM_VISFORMS_ENTER_VAILD_MINLENGTH')) . '"),
            rangelength: jQuery.validator.format("' . addslashes(JText::_('COM_VISMORMS_ENTER_VAILD_LENGTH')) . '"),
            range: jQuery.validator.format("' . addslashes(JText::_('COM_VISFORMS_ENTER_VAILD_RANGE')) . '"),
            max: jQuery.validator.format("' . addslashes(JText::_('COM_VISFORMS_ENTER_VAILD_MAX_VALUE')) . '"),
            min: jQuery.validator.format("' . addslashes(JText::_('COM_VISFORMS_ENTER_VAILD_MIN_VALUE')) . '"),
            customvalidation: "' . addslashes(JText::_('COM_VISFORMS_INVALID_INPUT')) . '",
            ispair: "' . addslashes(JText::_('COM_VISFORMS_ISPAIR_VALIDATION_FAILED_JS')) . '"
            });
            });';
        JFactory::getDocument()->addScriptDeclaration($script);
        static::$loaded[__METHOD__] = true;
    }

    public static function getValidatorMethodsScript() {
        if (!empty(static::$loaded[__METHOD__])) {
            return;
        }
        $script = 'jQuery(document).ready(function () {
            jQuery.validator.addMethod("dateDMY", function (value, element) {
                var check = false;
                var re = /^(0[1-9]|[12][0-9]|3[01])[\.](0[1-9]|1[012])[\.]\d{4}$/;
                    if (re.test(value)) {
                        var adata = value.split(".");
                        var day = parseInt(adata[0], 10);
                        var month = parseInt(adata[1], 10);
                        var year = parseInt(adata[2], 10);
                        if (day == 31 && (month == 4 || month == 6 || month == 9 || month == 11)) {
                            check = false; // 31st of a month with 30 days
                        } else if (day >= 30 && month == 2) {
                            check = false; // February 30th or 31st
                        } else if (month == 2 && day == 29 && !(year % 4 == 0 && (year % 100 != 0 || year % 400 == 0))) {
                            check = false; // February 29th outside a leap year
                        } else {
                            check = true; // Valid date
                        }
                    }
                    return this.optional(element) || check;
            });
            jQuery.validator.addMethod("dateMDY", function (value, element) {
                var check = false;
                var re = /^(0[1-9]|1[012])[\/](0[1-9]|[12][0-9]|3[01])[\/]\d{4}$/;
                    if (re.test(value)) {
                        var adata = value.split("/");
                        var month = parseInt(adata[0], 10);
                        var day = parseInt(adata[1], 10);
                        var year = parseInt(adata[2], 10);
                        if (day == 31 && (month == 4 || month == 6 || month == 9 || month == 11)) {
                            check = false; // 31st of a month with 30 days
                        } else if (day >= 30 && month == 2) {
                            check = false; // February 30th or 31st
                        } else if (month == 2 && day == 29 && !(year % 4 == 0 && (year % 100 != 0 || year % 400 == 0))) {
                            check = false; // February 29th outside a leap year
                        } else {
                            check = true; // Valid date
                        }
                    }
                    return this.optional(element) || check;
            });
            jQuery.validator.addMethod("dateYMD", function (value, element) {
                var check = false;
                var re = /^\d{4}[\-](0[1-9]|1[012])[\-](0[1-9]|[12][0-9]|3[01])$/;
                    if (re.test(value)) {
                        var adata = value.split("-");
                        var year = parseInt(adata[0], 10);
                        var month = parseInt(adata[1], 10);
                        var day = parseInt(adata[2], 10);
                        if (day == 31 && (month == 4 || month == 6 || month == 9 || month == 11)) {
                            check = false; // 31st of a month with 30 days
                        } else if (day >= 30 && month == 2) {
                            check = false; // February 30th or 31st
                        } else if (month == 2 && day == 29 && !(year % 4 == 0 && (year % 100 != 0 || year % 400 == 0))) {
                            check = false; // February 29th outside a leap year
                        } else {
                            check = true; // Valid date
                        }
                    }
                    return this.optional(element) || check;
            });
            jQuery.validator.addMethod("filesize", function (value, element, maxsize) {
                var check = false;
                if ((maxsize === 0) || ((!(element.files.length == 0)) && (element.files[0].size < maxsize)))
                {
                    check = true;
                }
                return this.optional(element) || check;
            });
            jQuery.validator.addMethod("fileextension", function (value, element, allowedextension) {
                var check = false;
                allowedextension = allowedextension.replace(/\s/g, "");
                allowedextension = allowedextension.split(",");
                var fileext = jQuery(element).val().split(".").pop().toLowerCase();
                if (jQuery.inArray(fileext, allowedextension) > -1)
                {
                    check = true;
                }
                return this.optional(element) || check;
            });
            jQuery.validator.addMethod("customvalidation", function (value, element, re) {
                return this.optional(element) || re.test(value);
            });
            jQuery.validator.addMethod("ispair", function (value, element, id) {
                var latval = document.getElementById(id+"_lat").value;
                var lngval = document.getElementById(id+"_lng").value;
                //false if on field is empty and the other not
                var check = ((latval === "" && lngval === "") || (latval !== "" && lngval !== ""));
                var relatval = /^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/;
                var relngval = /^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/;
                check = (latval === "" || relatval.test(latval)) && check;
                check = (lngval === "" || relngval.test(lngval)) && check;
                return check;
            });
            jQuery.validator.addMethod("mindate", function(value, element, options) {
	            var check = false;
	            var minDate = "";
	            if (value) {
	                if (options.fromField) {
	                    var fieldId = options.value;
	                    var field = document.getElementById(fieldId);
	                    if (!field) {
	                        return true;
	                    }
	                    if (field.disabled) {
	                        return true;
	                    }
	                    minDate = field.value;
	                    if (!minDate) {
	                        return true;
	                    }
	                } else {
	                    minDate = options.value;
	                }
	                var  format, i = 0, fmt = {}, minDateFormat, j = 0, minDateFmt = {}, day;
	                format = (value.indexOf(".") > -1) ? "dd.mm.yyyy" : ((value.indexOf("/") > -1) ? "mm/dd/yyyy" : "yyyy-mm-dd");
	                format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; });
	                minDateFormat = (minDate.indexOf(".") > -1) ? "dd.mm.yyyy" : ((minDate.indexOf("/") > -1) ? "mm/dd/yyyy" : "yyyy-mm-dd");
	                minDateFormat.replace(/(yyyy|dd|mm)/g, function(part) { minDateFmt[part] = j++; });
	                var minDateParts = minDate.match(/(\d+)/g);
	                var valueParts = value.match(/(\d+)/g);
	                minDate = new Date(minDateParts[minDateFmt["yyyy"]], minDateParts[minDateFmt["mm"]]-1, minDateParts[minDateFmt["dd"]],0,0,0,0);
	                if (options.shift) {
	                    var shift = options.shift;
	                    day = minDate.getDate();
	                    day = day + parseInt(shift);
	                    minDate.setDate(day);
	                }
	                value = new Date(valueParts[fmt["yyyy"]], valueParts[fmt["mm"]]-1, valueParts[fmt["dd"]],0,0,0,0);
	                check = value >= minDate;
                }
                return this.optional(element) || check;
            }, function(options, element) {
            //validation message
             if (options.fromField) {
                    var minDate = "";
                    var fieldId = options.value;
                    var field = document.getElementById(fieldId);
                    if (field) {
                        minDate = field.value;
                    }
                } else {
                    minDate = options.value;
                }
                var format, minDateFormat, j = 0, minDateFmt = {}, day, month, year, valDate;
                minDateFormat = (minDate.indexOf(".") > -1) ? "dd.mm.yyyy" : ((minDate.indexOf("/") > -1) ? "mm/dd/yyyy" : "yyyy-mm-dd");
                minDateFormat.replace(/(yyyy|dd|mm)/g, function(part) { minDateFmt[part] = j++; });
                var minDateParts = minDate.match(/(\d+)/g);
                minDate = new Date(minDateParts[minDateFmt["yyyy"]], minDateParts[minDateFmt["mm"]]-1, minDateParts[minDateFmt["dd"]],0,0,0,0);
                if (options.shift) {
                    var shift = options.shift;
                    day = minDate.getDate();
                    day = day + parseInt(shift);
                    minDate.setDate(day);
                }
                format = options.format;
                valDate = "";
                day = minDate.getDate();
                if (day < 10) {
                    day = "0" + day;
                }
                month = 1 + minDate.getMonth();
                if (month < 10) {
                    month = "0" + month;
                }
                year = minDate.getFullYear();
                switch (format) {
                    case "%Y-%m-%d" :
                        valDate = year + "-" + month + "-" + day;
                        break;
                    case "%m/%d/%Y" :
                        valDate = month + "/" + day  + "/" + year;
                        break;
                    default :
                        valDate = day + "." + month + "." + year;
                        break;
                }
                return jQuery.validator.format("' . addslashes(JText::_('COM_VISFORMS_MINDATE_VALIDATION_FAILED_JS')) . '", valDate);               
            });
            jQuery.validator.addMethod("maxdate", function(value, element, options) {
	            var check = false;
	            var minDate = "";
	            if (value) {
	                if (options.fromField) {
	                    var fieldId = options.value;
	                    var field = document.getElementById(fieldId);
	                    if (!field) {
	                        return true;
	                    }
	                    if (field.disabled) {
	                        return true;
	                    }
	                    minDate = field.value;
	                    if (!minDate) {
	                        return true;
	                    }
	                } else {
	                    minDate = options.value;
	                }
	                var  format, i = 0, fmt = {}, minDateFormat, j = 0, minDateFmt = {}, day;
	                format = (value.indexOf(".") > -1) ? "dd.mm.yyyy" : ((value.indexOf("/") > -1) ? "mm/dd/yyyy" : "yyyy-mm-dd");
	                format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; });
	                minDateFormat = (minDate.indexOf(".") > -1) ? "dd.mm.yyyy" : ((minDate.indexOf("/") > -1) ? "mm/dd/yyyy" : "yyyy-mm-dd");
	                minDateFormat.replace(/(yyyy|dd|mm)/g, function(part) { minDateFmt[part] = j++; });
	                var minDateParts = minDate.match(/(\d+)/g);
	                var valueParts = value.match(/(\d+)/g);
	                minDate = new Date(minDateParts[minDateFmt["yyyy"]], minDateParts[minDateFmt["mm"]]-1, minDateParts[minDateFmt["dd"]],0,0,0,0);
	                if (options.shift) {
	                    var shift = options.shift;
	                    day = minDate.getDate();
	                    day = day + parseInt(shift);
	                    minDate.setDate(day);
	                }
	                value = new Date(valueParts[fmt["yyyy"]], valueParts[fmt["mm"]]-1, valueParts[fmt["dd"]],0,0,0,0);
	                check = value <= minDate;
                }
                return this.optional(element) || check;
            }, function(options, element) {
            //validation message
             if (options.fromField) {
                    var minDate = "";
                    var fieldId = options.value;
                    var field = document.getElementById(fieldId);
                    if (field) {
                        minDate = field.value;
                    }
                } else {
                    minDate = options.value;
                }
                var format, minDateFormat, j = 0, minDateFmt = {}, day, month, year, valDate;
                minDateFormat = (minDate.indexOf(".") > -1) ? "dd.mm.yyyy" : ((minDate.indexOf("/") > -1) ? "mm/dd/yyyy" : "yyyy-mm-dd");
                minDateFormat.replace(/(yyyy|dd|mm)/g, function(part) { minDateFmt[part] = j++; });
                var minDateParts = minDate.match(/(\d+)/g);
                minDate = new Date(minDateParts[minDateFmt["yyyy"]], minDateParts[minDateFmt["mm"]]-1, minDateParts[minDateFmt["dd"]],0,0,0,0);
                if (options.shift) {
                    var shift = options.shift;
                    day = minDate.getDate();
                    day = day + parseInt(shift);
                    minDate.setDate(day);
                }
                format = options.format;
                valDate = "";
                day = minDate.getDate();
                if (day < 10) {
                    day = "0" + day;
                }
                month = 1 + minDate.getMonth();
                if (month < 10) {
                    month = "0" + month;
                }
                year = minDate.getFullYear();
                switch (format) {
                    case "%Y-%m-%d" :
                        valDate = year + "-" + month + "-" + day;
                        break;
                    case "%m/%d/%Y" :
                        valDate = month + "/" + day  + "/" + year;
                        break;
                    default :
                        valDate = day + "." + month + "." + year;
                        break;
                }
                return jQuery.validator.format("' . addslashes(JText::_('COM_VISFORMS_MAXDATE_VALIDATION_FAILED_JS')) . '", valDate);
            });
        });';
        JFactory::getDocument()->addScriptDeclaration($script);
        static::$loaded[__METHOD__] = true;
    }

    public static function replacePlaceholder($form, &$text = '') {
        if ($text != '') {
        	$matches = VisformsPlaceholder::getAllPlaceholderFullString($text);
	        if (!empty($matches)) {
                foreach ($matches as $match) {
                	$replace = '';
                	$pName = VisformsPlaceholder::getPlaceholderName($match);
                	if (empty($pName)) {
                		// should never happen, remove placeholder from text
		                $newText = preg_replace('\'' . preg_quote($match) . '\'', $replace, $text);
		                $text = $newText;
		                continue;
	                }
	                // overhead placeholder
                	if (array_key_exists($pName, VisformsPlaceholder::$nonFieldPlaceholder)) {
                		switch ($pName) {
			                case 'id' :
			                	$replace = (!empty($form->dataRecordId)) ? $form->dataRecordId : '';
			                	break;
			                case 'formtitle' :
				                $replace = (!empty($form->title)) ? $form->title : '';
				                break;
			                default :
				                $placeholder = VisformsPlaceholder::getInstance('', null, $pName);
				                $replace = $placeholder->getReplaceValue();
				                break;
		                }
	                }
	                else if (is_array($form->fields)) {
                        foreach ($form->fields as $ffield) {
                            $fieldName = (!empty($form->context)) ? str_replace($form->context, '', $ffield->name) : $ffield->name;
                            if ($pName === $fieldName) {
                                $pParams = VisformsPlaceholder::getPlaceholderParam($match);
                                $placeholder = VisformsPlaceholder::getInstance($pParams, $ffield->dbValue, $ffield->typefield, $ffield);
                                $replace = $placeholder->getReplaceValue();
                                break;
	                        }
                        }
                    }

                    // replace the match
                    $newText = preg_replace('\'' . preg_quote($match) . '\'', $replace, $text);
                    $text = $newText;
                    unset($replace);
                }
            }
        }
        return $text;
    }

    public static function fixLinksInMail(&$text) {
        $urlPattern = '/^(http|https|ftp|mailto)\:.*$/i';
        $aPattern = '/<[ ]*a[^>]+href=[("\')]([^("\')]*)/';
        $imgPattern = '/<[ ]*img[^>]+src=[("\')]([^("\')]*)/';
        if (preg_match_all($aPattern, $text, $hrefs)) {
            foreach ($hrefs[1] as $href) {
                if (!(preg_match($urlPattern, $href) == 1)) {
                    //we deal with an intern Url without Root path
                    $link = JURI::base() . $href;
                    $newText = preg_replace('\'' . preg_quote($href) . '\'', $link, $text);
                    $text = $newText;
                }
            }
        }
        if (preg_match_all($imgPattern, $text, $srcs)) {
            jimport('joomla.filesystem.file');
            jimport('joomla.filesystem.folder');
            foreach ($srcs[1] as $src) {
                if (JFile::exists($src)) {
                    //we deal with a local img
                    if (!(preg_match('\'' . preg_quote(Juri::base()) . '\'', $src) == 1)) {
                        //we deal with an intern Url without base Uri
                        $link = Juri::base() . $src;
                        $newText = preg_replace('\'' . preg_quote($src) . '\'', $link, $text);
                        $text = $newText;
                    }
                }
            }
        }
        return $text;
    }

    //this is a copy of JHtml::_(gird.sort...
    //necessary to use our own function because we cannot change the icon prefix in Joomla! grid.sort function into visicon
    //and to allow multiple sort forms on one page (since Visforms 3.7.1)
    public static function sort($title, $order, $direction = 'asc', $selected = '', $task = null, $new_direction = 'asc', $tip = '', $form = 'adminForm', $jsFunction = "Joomla.tableOrdering", $unSortable = false) {
    	if (!empty($unSortable)) {
    		return JText::_($title);
	    }
        JHtml::_('behavior.core');
        JHtml::_('bootstrap.tooltip');
        $direction = strtolower($direction);
        $icon = array('arrow-up-3', 'arrow-down-3');
        $index = (int)($direction == 'desc');
        if ($order != $selected) {
            $direction = $new_direction;
        } else {
            $direction = ($direction == 'desc') ? 'asc' : 'desc';
        }
        $html = array();
        $html[] = '<a href="#" onclick="' . $jsFunction . '(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\',document.getElementById(\'' . $form . '\'));return false;"'
            . ' class="hasTooltip" title="' . JHtml::tooltipText(($tip ? $tip : $title), 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN') . '">';
        if (isset($title['0']) && $title['0'] == '<') {
            $html[] = $title;
        } else {
            $html[] = JText::_($title);
        }
        if ($order == $selected) {
            $html[] = ' <span class="visicon-' . $icon[$index] . '"></span>';
        }
        $html[] = '</a>';
        return implode('', $html);
    }

    private static function getCustomCssFileNameList() {
        jimport('joomla.filesystem.folder');
        $path = JPath::clean(JPATH_ROOT . '/media/com_visforms/css/');
        $result = array();
        $dirFiles = scandir($path);
        $regex = '@^(.*custom.*)(\.css)$@';

        foreach ($dirFiles as $key => $value) {
            if (is_file(JPath::clean($path . $value))) {
                if (preg_match($regex, $value, $match)) {
                    if ($match) {
                        $match = preg_replace($regex, '$1', $value);
                        $result[$match] = true;
                    }
                }
            }
        }
        return $result;
    }

    public static function base64_url_encode($val) {
        return strtr(base64_encode($val), '+/=', '-_,');
    }

    static function base64_url_decode($val) {
        return base64_decode(strtr($val, '-_,', '+/='));
    }

    public static function createSelectFromDb($table, $name, $selected, $attribs = '', $params = true, $id = false, $textfieldname = "a.title", $where='', $order = '', $textprefix = '', $valueprefix = '', $requiresSub = true) {
        $hasSub = VisformsAEF::checkAEF(VisformsAEF::$subscription);
        if (empty($requiresSub) || (!empty($hasSub))) {
            $db = JFactory::getDbo();;
            $query = $db->getQuery(true);
            $query->select($db->quoteName('a.id', 'value') . ', ' . $db->quoteName($textfieldname, 'text'))
                ->from($db->quoteName($table, 'a'));
            if (!empty($where)) {
                $query->where($where);
            }
            if (!empty($order)) {
                $query->order($db->quoteName($order) . ' DESC');
            }
            // Get the options.
            $db->setQuery($query);
            try {
                $options = $db->loadObjectList();
            } catch (RuntimeException $e) {
                $options = array();
            }
            if (!empty($options)) {
                $count = count($options);
                for ($i = 0; $i < $count; $i++) {
                    if (!empty($valueprefix)) {
                        $options[$i]->value = JText::_($valueprefix) . $options[$i]->value;
                    }
                    if (!empty($textprefix)) {
                        $options[$i]->text = JText::_($textprefix) . ': ' . $options[$i]->text;
                    }
                }
            }
        } else {
            $options = array();
        }

        // If params is an array, push these options to the array
        if (is_array($params)) {
            $options = array_merge($params, $options);
        }

        return JHtml::_(
            'select.genericlist',
            $options,
            $name,
            array(
                'list.attr' => $attribs,
                'list.select' => $selected,
                'id' => $id
            )
        );
    }

    public static function getCustomUserFieldValue($id) {
        if (empty($id)) {
            return false;
        }
        $user = JFactory::getUser();
        $userId = $user->get('id');
        if (empty($userId)) {
            return false;
        }
        $db = JFactory::getDbo();;
        $query = $db->getQuery(true);
        $query->select($db->qn('value'))
            ->from($db->qn('#__fields_values'))
            ->where($db->qn('field_id') . ' = ' . $id)
            ->where($db->qn('item_id') . ' = ' . $userId);
        $db->setQuery($query);
        try {
            $value = $db->loadResult();
        } catch (RuntimeException $e) {
            $value = false;
        }
        return $value;
    }
}