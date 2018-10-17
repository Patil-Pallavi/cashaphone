<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.beez3
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$templateparams = $app->getTemplate(true)->params;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);
$user = JFactory::getUser();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('behavior.caption');

// Create shortcut to parameters.
$params = $this->item->params;
?>
<?php if ($params->get('access-view')): ?>
    <?php echo $this->item->text; ?>		
<?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
    <?php echo JLayoutHelper::render('joomla.content.intro_image', $this->item); ?>
    <?php echo JHtml::_('content.prepare', $this->item->introtext); ?>
    <?php // Optional link to let them register to see the whole article.        ?>
    <?php if ($this->item->fulltext != null && $params->get('show_readmore')) : ?>
        <?php $menu = JFactory::getApplication()->getMenu(); ?>
        <?php $active = $menu->getActive(); ?>
        <?php $itemId = $active->id; ?>
        <?php $link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false)); ?>
        <?php $link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language))); ?>
        <p class="readmore">
            <a href="<?php echo $link; ?>" class="register">
                <?php $attribs = json_decode($this->item->attribs); ?>
                <?php if ($attribs->alternative_readmore == null) : ?>
                    <?php echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?>
                <?php elseif ($readmore = $attribs->alternative_readmore) : ?>
                    <?php echo $readmore; ?>
                    <?php if ($params->get('show_readmore_title', 0) != 0) : ?>
                        <?php echo JHtml::_('string.truncate', $this->item->title, $params->get('readmore_limit')); ?>
                    <?php endif; ?>
                <?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
                    <?php echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE'); ?>
                <?php else : ?>
                    <?php echo JText::_('COM_CONTENT_READ_MORE'); ?>
                    <?php echo JHtml::_('string.truncate', $this->item->title, $params->get('readmore_limit')); ?>
                <?php endif; ?>
            </a>
        </p>
    <?php endif; ?>
<?php endif; ?>