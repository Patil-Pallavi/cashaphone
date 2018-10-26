<?php
/**
 * @package         Regular Labs Library
 * @version         18.10.19424
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
{
	return;
}

require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';

class JFormFieldRL_Showon extends \RegularLabs\Library\Field
{
	public $type = 'Showon';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$value       = (string) $this->get('value');
		$class       = $this->get('class', '');
		$formControl = $this->get('form', $this->formControl);
		$formControl = $formControl == 'root' ? '' : $formControl;

		if ( ! $value)
		{
			return '</div>';
		}

		JHtml::_('script', 'jui/cms.js', ['version' => 'auto', 'relative' => true]);
		$json = json_encode(JFormHelper::parseShowOnConditions($value, $formControl, $this->group));

		$attributes = ['data-showon=\'' . $json . '\''];
		if ($class)
		{
			$attributes[] = 'class="' . $class . '"';
		}

		$html = [
			'</div>',
			'</div>',
			'<div ' . implode(' ', $attributes) . '>',
			'<div>',
			'<div>',
		];

		return implode('', $html);
	}
}
