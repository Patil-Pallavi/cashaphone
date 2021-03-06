<?php
/**
 * @package   OSMap
 * @copyright 2007-2014 XMap - Joomla! Vargas - Guillermo Vargas. All rights reserved.
 * @copyright 2016-2017 Open Source Training, LLC. All rights reserved.
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Alledia\OSMap\Sitemap\ItemAdapter;

defined('_JEXEC') or die();

interface AdapterInterface
{
    /**
     * Gets the visible state for robots. Each adapter will check specific params. Returns
     * true if the item is visible.
     *
     * @return bool
     */
    public function checkVisibilityForRobots();
}
