<?php
/**
 * viscpanel default view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

//no direct access
 defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.framework');
$issub = VisformsAEF::checkAEF(VisformsAEF::$subscription);
$hasAef = VisformsAEF::checkForOneAef();
$isOldSub = VisformsAEF::checkForAllAef();
$component = JComponentHelper::getComponent('com_visforms');
$dlid = $component->params->get('downloadid', '');
$gotSubUpdateInfo = $component->params->get('gotSubUpdateInfo', '');
$extensiontypetag = ($issub || $isOldSub) ? 'COM_VISFORMS_SUBSCRIPTION' : 'COM_VISFORMS_PAYED_EXTENSION';
?>

    <?php
 if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <?php else : ?>
<div id="j-main-container"><?php endif; ?>
    <div id="vfcpanel">
        <?php  if (isset($this->update_message)) {echo $this->update_message;} ?>
        <?php if ($gotSubUpdateInfo == '') { ?>
        <?php if ($isOldSub && !$issub) { ?>
            <div class="alert alert-success">
                <form class="pull-right" action="<?php echo JRoute::_($this->gotSubUpdateInfoLink); ?>" method="post">
                    <input class="btn" type="submit" value="<?php echo JText::_("COM_VISFORMS_CPANEL_OLD_SUB_HIDE_UPDATE_MESSAGE"); ?>" />
                    <?php echo JHtml::_('form.token'); ?>
                </form>
                <h3><?php echo JText::sprintf("COM_VISFORMS_CPANEL_OLD_SUB_HEADER", JText::_($extensiontypetag)); ?></h3>
                <p><?php echo JText::_("COM_VISFORMS_CPANEL_OLD_SUB_DESCR"); ?></p>
                <div class="row-fluid"><a href="<?php echo JRoute::_($this->subUpdateMoreInfoLink); ?>" class="btn btn-info" target="_blank"><?php echo JText::_("COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_COMPARE_VERSIONS"); ?></a> <a href="<?php echo JRoute::_($this->subUpdateInstructionLink); ?>" class="btn btn-warning" target="_blank"><?php echo JText::_("COM_VISFORMS_CPANEL_OLD_SUB_UPDATE_INSTRUCTIONS"); ?></a>
                </div>
            </div>
        <?php } else if ($hasAef && !$issub) { ?>
            <div class="alert alert-success">
                <form class="pull-right" action="<?php echo JRoute::_($this->gotSubUpdateInfoLink); ?>" method="post">
                    <input class="btn" type="submit" value="<?php echo JText::_("COM_VISFORMS_CPANEL_OLD_SUB_HIDE_UPDATE_MESSAGE"); ?>" />
                    <?php echo JHtml::_('form.token'); ?>
                </form>
                <h3><?php echo JText::sprintf("COM_VISFORMS_CPANEL_OLD_SUB_HEADER", JText::_($extensiontypetag)); ?></h3>
                <h3><?php echo JText::_("COM_VISFORMS_CPANEL_HAVE_SINGLE_EXT"); ?></h3>
                <p><?php echo JText::_("COM_VISFORMS_CPANEL_OLD_EXT_DESCR"); ?> <a href="<?php echo JRoute::_($this->extUpdateMoreInfoLink); ?>" class="btn btn-warning" target="_blank"><?php echo JText::_("COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_COMPARE_VERSIONS"); ?></a></p>
                <h3><?php echo JText::_("COM_VISFORMS_CPANEL_HAVE_OLD_SUB"); ?></h3>
                <p><?php echo JText::_("COM_VISFORMS_CPANEL_OLD_SUB_DESCR"); ?> <a href="<?php echo JRoute::_($this->subUpdateMoreInfoLink); ?>" class="btn btn-warning" target="_blank"><?php echo JText::_("COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_COMPARE_VERSIONS"); ?></a></p>
            </div>
        <?php } ?>
        <?php } ?>
        <h1><?php echo JText::_('COM_VISFORMS_SUBMENU_CPANEL_LABEL'); ?></h1>
        <div class="row-fluid">
            <div class="span6">
                <h3><?php echo JText::_('COM_VISFORMS_CPANEL_OPERATIIONS_HEADER'); ?></h3>
                <div class="clearfix">
                    <div class="cpanel">
                        <a href="index.php?option=com_visforms&amp;view=visforms"><i class="icon-stack"></i><span><?php echo JText::_('COM_VISFORMS_SUBMENU_FORMS'); ?></span></a>
                    </div>
                    <?php if ($this->canDo->get('core.create')) : ?>
                    <div class="cpanel">
                        <a href="index.php?option=com_visforms&amp;task=visform.add" ><i class="icon-file-plus"></i><span><?php echo JText::_('COM_VISFORMS_FORM_NEW'); ?></span></a>
                    </div>
                    <?php endif; ?>
                    <?php if (JFactory::getUser()->authorise('core.admin', 'com_visforms')) : ?>
                    <div class="cpanel">
                        <a href="<?php echo $this->preferencesLink; ?>" ><i class="icon-options"></i><span><?php echo JText::_('JTOOLBAR_OPTIONS'); ?></span></a>
                    </div>
                    <?php endif; ?>
                    <?php if ($this->canDo->get('core.edit.css')) : ?>
                    <div class="cpanel">
                        <a href="index.php?option=com_visforms&amp;task=viscpanel.edit_css" ><i class="icon-editcss"></i><span><?php echo JText::_('COM_VISFORMS_EDIT_CSS'); ?></span></a>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
            <div class="span5">
                <h3><?php echo JText::_('COM_VISFORMS_CPANEL_INFO_SUPPORT_HEADER'); ?></h3>
                <div class="clearfix">
                    <div class="cpanel">
                        <a href="<?php echo $this->documentationLink; ?>" target="_blank"><i class="icon-info-circle"></i><span><?php echo JText::_('COM_VISFORMS_CPANEL_DOCUMENTATION_BUTTON_LABEL');?></span></a>
                    </div>
                    <div class="cpanel">
                        <a href="<?php echo $this->forumLink; ?>" target="_blank"><i class="icon-question-circle"></i><span><?php echo JText::_('COM_VISFORMS_CPANEL_FORUM_BUTTON_LABEL');?></span></a>
                    </div>
                </div>

            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <?php if ((empty($issub)) && (empty($hasAef))) : ?>
                <h3><?php echo JText::_('COM_VISFORMS_CPANEL_ADDITIONAL_FEATURE_HEADER'); ?></h3>
                <div id="subscribe" class="alert alert-block alert-info">
                    <p class="text-center"><?php echo JText::_('COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_TEXT'); ?></p>
                    <p class="text-center visible-desktop"><?php echo JText::_('COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_LIST'); ?></p>
                    <p class="text-center" style="margin-top: 20px"><a href="<?php echo $this->versionCompareLink; ?>" target="_blank" class="btn btn-small"><?php echo JText::_('COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_COMPARE_VERSIONS'); ?></a>
                    <a href="<?php echo $this->buySubsLink; ?>" target="_blank" class="btn btn-small"><?php echo JText::_('COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_BUY_SUBSCRIPTION'); ?></a></p>
                </div>
                <?php endif; ?>
                <?php if ((!empty($issub)) || (!empty($hasAef))) : ?>
                    <h3><?php echo JText::sprintf('COM_VISFORMS_CPANEL_MANAGE_SUBSCRIPTION_HEADER', JText::_($extensiontypetag)); ?></h3>
                    <div class="clearfix">
                        <div class="cpanel">
                            <a href="#downloadid" data-toggle="modal"><i class="icon-unlock "></i><span><?php echo JText::_('COM_VISFORMS_CPANEL_UPDATE_BUTTON_LABEL'); ?></span></a>
                        </div>
                        <div class="cpanel">
                            <a href=<?php echo $this->dlidInfoLink; ?>" target="_blank"><i class="icon-eye-open "></i><span><?php echo JText::_('COM_VISFORMS_CPANEL_MANAGE_BUTTON_LABEL'); ?></span></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="span5">

                <h3><?php echo JText::_('COM_VISFORMS_CPANEL_CONTRIBUTE_HEADER'); ?></h3>
                <div class="clearfix">
                    <div class="cpanel">
                        <a href="http://extensions.joomla.org/extensions/contacts-and-feedback/forms/23899" target="_blank"><i class="icon-star"></i><span><?php echo JText::_('COM_VISFORMS_CPANEL_REVIEW_BUTTON_LABEL');?></span></a>
                    </div>
                    <?php if (empty($issub) && (empty($hasAef))) : ?>
                    <div class="cpanel">
                        <a href="<?php echo $this->donateLink; ?>" target="_blank"><i class="icon-credit"></i><span><?php echo JText::_('COM_VISFORMS_CPANEL_DONATE_BUTTON_LABEL');?></span></a>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <div class="row-fluid">
            <div class="span11">
                <?php
                if (empty($this->items) || empty($this->submitfieldcount)) : ?>
                    <h3><?php echo JText::_('COM_VISFORMS_HELP_GETTING_STARTED_HEADER'); ?></h3>
                    <div class="accordion" id="first-steps">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#first-steps" href="#createform">
                                    <?php echo JText::_('COM_VISFORMS_CREATE_FORM'); ?>
                                </a>
                            </div>
                            <div id="createform" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <ul>
                                        <li><?php echo JText::_('COM_VISFORMS_CREATE_FORM_STEP1'); ?></li>
                                        <li><?php echo JText::_('COM_VISFORMS_CREATE_FORM_STEP2'); ?></li>
                                        <li><?php echo JText::_('COM_VISFORMS_CREATE_FORM_STEP3'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#first-steps" href="#addfields">
                                    <?php echo JText::_('COM_VISFORMS_ADD_FIELDS'); ?>
                                </a>
                            </div>
                            <div id="addfields" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <ul>
                                        <li><?php echo JText::_('COM_VISFORMS_ADD_FIELDS_STEP1'); ?></li>
                                        <li><?php echo JText::_('COM_VISFORMS_ADD_FIELDS_STEP2'); ?></li>
                                        <li><?php echo JText::_('COM_VISFORMS_ADD_FIELDS_STEP3'); ?></li>
                                        <li><?php echo JText::_('COM_VISFORMS_ADD_FIELDS_STEP4'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#first-steps" href="#addsubmit">
                                    <?php echo JText::_('COM_VISFORMS_ADD_SUBMIT_BUTTON'); ?>
                                </a>
                            </div>
                            <div id="addsubmit" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <ul>
                                        <li><?php echo JText::_('COM_VISFORMS_ADD_SUBMIT_BUTTON_STEP1'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#first-steps" href="#createmenu">
                                    <?php echo JText::_('COM_VISFORMS_FIRST_STEPS_ADD_MENU_ITEM'); ?>
                                </a>
                            </div>
                            <div id="createmenu" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <ul>
                                        <li><?php echo JText::_('COM_VISFORMS_FIRST_STEPS_ADD_MENU_ITEM_STEP1'); ?></li>
                                        <li><?php echo JText::_('COM_VISFORMS_FIRST_STEPS_ADD_MENU_ITEM_STEP2'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span11">
                <h3><?php echo JText::_('COM_VISFORMS_CPANEL_TRANSLATIONS'); ?></h3>
                <p>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/cs_cz.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/el_gr.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/fr_fr.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/he_il.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/nl_nl.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/pl_pl.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/pt_br.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/ru_ru.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/sk_sk.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/tr_tr.gif"/></a>
                    <a href="<?php echo $this->translationsLink; ?>" target="_blank"><img class="img-bordered" src="<?php echo JUri::root(); ?>/media/com_visforms/img/sr_yu.gif"/></a>
                </p>
            </div>
        </div>
        <?php echo JHtml::_('visforms.creditsBackend'); ?>
    </div>
</div>


<div id="downloadid" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="downloadid" aria-hidden="true">
    <form class="form-horizontal" action="<?php echo JRoute::_($this->dlidFormLink); ?>" method="post" style="padding-bottom: 0; margin-bottom:0">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo JText::sprintf('COM_VISFORMS_CPANEL_MODAL_UPDATE_HEADER', JText::_($extensiontypetag));?></h3>
    </div>
    <div class="modal-body">
        <div class="control-group">

                <label class="control-label" style="width: 160px; text-align: right;"><?php echo JText::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_LABEL');?></label>

            <div class="controls">
                <input name="downloadid" type="text" value="<?php echo $dlid; ?>" /><span class="help-inline"><?php echo JText::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_DESC'); ?></span>
            </div>
        </div>
        <div class="accordion" id="dlid">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#dlid" href="#dlid-info">
                        <?php echo JText::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_HEADER'); ?>
                    </a>
                </div>
                <div id="dlid-info" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <p><?php echo JText::sprintf('COM_VISFORMS_DOWNLOAD_ID_DESC', JText::_($extensiontypetag), JText::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_LINK_TEXT'), JText::_($extensiontypetag));?></p>
                        <p><a href="<?php echo $this->dlidInfoLink; ?>" target="_blank"><?php echo JText::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_LINK_TEXT'); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer" style="text-align: left;">
        <input type="submit" class="btn btn-success" value="Submit" />
        <button type="button" class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_VISFORMS_CLOSE'); ?></button>
    </div>
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>

