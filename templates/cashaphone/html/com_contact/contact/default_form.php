<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
$app = JFactory::getApplication();
$messageQueue = $app->getMessageQueue();
?>
<div class="col- col-sm-12 col-md-8 col-lg-5 col-xl-5 ">
<?php if(isset($messageQueue[0]['message']) && $messageQueue[0]['message'] != ''){ ?>
	<div class="alert alert-success alert-dismissible">
	  	<strong><?php echo $messageQueue[0]['message']; ?></strong>
	</div>
<?php } ?>
<h1>Contact Us</h1>
	<div class="contact_us_cont">
		<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate form-horizontal well">

			<div class="form-group">
			 	<input id="jform_contact_name" class="required form-control" type="text" aria-required="true" required="required" size="30" value="" name="jform[contact_name]" placeholder="Name">
			</div>
			<div class="form-group">
				<input id="jform_contact_email" class="validate-email required form-control" type="email" aria-required="true" required="required" autocomplete="email" size="30" value="" name="jform[contact_email]" aria-invalid="true" placeholder="Email">
			</div>
			<div class="form-group">
				<input id="jform_contact_emailmsg" class="required form-control" type="text" aria-required="true" required="required" size="60" value="" name="jform[contact_subject]" placeholder="Subject">		  
			</div>
			<div class="form-group">
				<textarea id="jform_contact_message" class="required form-control" aria-required="true" required="required" rows="10" cols="50" name="jform[contact_message]" placeholder="Message"></textarea>		
			</div>			
			<button class="contact_send" type="submit">SEND</button>
			<input id="jform_contact_email_copy" type="checkbox" value="1" name="jform[contact_email_copy]" hidden="true" checked="true">
			<input type="hidden" name="option" value="com_contact" />
			<input type="hidden" name="task" value="contact.submit" />
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
	<div class="help_desk">
		<h2>We are here to help :</h2>
		<span>Tel : 1300 771 330</span>
	</div>
</div>