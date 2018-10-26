<?php
/**
 * vistools model for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

defined('_JEXEC') or die;

/**
 * vistools Model
 *
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @since        Joomla 1.6 
 */
class VisformsModelVistools extends JModelForm
{
	protected $element = null;

	/**
	 * Internal method to get file properties.
	 *
	 * @param   string  $path  The base path.
	 * @param   string  $name  The file name.
	 *
	 * @return  object
	 *
	 * @since   1.6
	 */
	protected function getFile($path, $name)
	{
		$temp = new stdClass;

        $temp->name = $name;
        $temp->id = urlencode(base64_encode($path . $name));

        return $temp;
	}

	/**
	 * Method to get a list of all the files to edit in a the media/css folder.
	 *
	 * @return  array  A nested array of relevant files.
	 *
	 * @since   1.6
	 */
	public function getFiles()
	{
		$result	= array();

        jimport('joomla.filesystem.folder');
        $app    = JFactory::getApplication();
        $path   = $csspath = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_visforms' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR);
        $this->element = $path;

        if (!is_writable($path))
        {
            $app->enqueueMessage(JText::_('COM_VISFORMS_DIRECTORY_NOT_WRITABLE'), 'error');
        }

        if (is_dir($path))
        {
            $result = $this->getDirectoryTree($path);
        }
        else
        {
            $app->enqueueMessage(JText::_('COM_VISFORMS_ERROR_CSS_FOLDER_NOT_FOUND'), 'error');

            return false;
        }

		return $result;
	}

	/**
	 * Get the directory tree.
	 *
	 * @param   string  $dir  The path of the directory to scan
	 *
	 * @return  array
	 *
	 * @since   3.2
	 */
	public function getDirectoryTree($dir)
	{
		$result = array();

		$dirFiles = scandir($dir);

		foreach ($dirFiles as $key => $value)
		{
			if (!in_array($value, array(".", "..")))
			{
				if (is_dir($dir . $value))
				{
					$relativePath = str_replace($this->element, '', $dir . $value);
					$result['/' . $relativePath] = $this->getDirectoryTree($dir . $value . '/');
				}
				else
				{
					$ext          = pathinfo($dir . $value, PATHINFO_EXTENSION);
					$types = array('css');

					if (in_array($ext, $types))
					{
						$relativePath = str_replace($this->element, '', $dir);
						//$info = $this->getFile('/' . $relativePath, $value);
                        $info = $this->getFile($relativePath, $value);
						$result[] = $info;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		jimport('joomla.filesystem.file');
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		// Codemirror or Editor None should be enabled
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__extensions as a')
			->where(
				'(a.name =' . $db->quote('plg_editors_codemirror') .
				' AND a.enabled = 1) OR (a.name =' .
				$db->quote('plg_editors_none') .
				' AND a.enabled = 1)'
			);
		$db->setQuery($query);
		$state = $db->loadResult();

		if ((int) $state < 1)
		{
			$app->enqueueMessage(JText::_('COM_VISFORMS_ERROR_EDITOR_DISABLED'), 'warning');
		}

		// Get the form.
		$form = $this->loadForm('com_visforms.source', 'source', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		$data = $this->getSource();

		$this->preprocessData('com_visforms.source', $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function &getSource()
	{
		$app = JFactory::getApplication();
		$item = new stdClass;

        $input    = JFactory::getApplication()->input;
        $fileName = base64_decode($input->get('file'));
        $filePath = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_visforms' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $fileName);

        if (file_exists($filePath))
        {
            //$item->extension_id = $this->getState('extension.id');
            $item->filename = $fileName;
            $item->source = file_get_contents($filePath);
        }
        else
        {
            $app->enqueueMessage(JText::_('COM_VISFORMS_FILE_NOT_FOUND'), 'error');
        }

		return $item;
	}

	/**
	 * Method to store the source file contents.
	 *
	 * @param   array  $data  The source data to save.
	 *
	 * @return  boolean  True on success, false otherwise and internal error set.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		jimport('joomla.filesystem.file');

		$app = JFactory::getApplication();
		$fileName = base64_decode($app->input->get('file'));
		$filePath = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_visforms' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $fileName);

		// Include the extension plugins for the save events.
		JPluginHelper::importPlugin('extension');

		$user = get_current_user();
		chown($filePath, $user);
		JPath::setPermissions($filePath, '0644');

		// Try to make the css file writable.
		if (!is_writable($filePath))
		{
			$app->enqueueMessage(JText::_('COM_VISFORMS_ERROR_SOURCE_FILE_NOT_WRITABLE'), 'warning');
			$app->enqueueMessage(JText::_('COM_VISFORMS_FILE_PERMISSIONS' . JPath::getPermissions($filePath)), 'warning');

			if (!JPath::isOwner($filePath))
			{
				$app->enqueueMessage(JText::_('COM_VISFORMS_CHECK_FILE_OWNERSHIP'), 'warning');
			}

			return false;
		}

		$return = JFile::write($filePath, $data['source']);

		// Try to make the css file unwritable.
		if (JPath::isOwner($filePath) && !JPath::setPermissions($filePath, '0444'))
		{
			$app->enqueueMessage(JText::_('COM_VISFORMS_ERROR_SOURCE_FILE_NOT_UNWRITABLE'), 'error');

			return false;
		}
		elseif (!$return)
		{
			$app->enqueueMessage(JText::sprintf('COM_VISFORMS_ERROR_FAILED_TO_SAVE_FILENAME', $fileName), 'error');

			return false;
		}

		$explodeArray = explode('.', $fileName);
		$ext = end($explodeArray);

		return true;
	}
    
    /**
	 * Upload new file.
	 *
	 * @param   string  $file      The name of the file.
	 * @param   string  $location  Location for the new file.
	 *
	 * @return   boolean  True if file uploaded successfully, false otherwise
	 *
	 * @since   3.2
	 */
	public function uploadFile($file)
	{
		jimport('joomla.filesystem.folder');
        $app      = JFactory::getApplication();
        $path     = JPath::clean(JPATH_ROOT . '/media/com_visforms/css/');
        $fileName = JFile::makeSafe($file['name']);

        $allowedExtensions = 'css';
        JLoader::register('VisformsHelper', JPATH_ADMINISTRATOR . '/components/com_visforms/helpers/visforms.php');

        if (!VisformsHelper::canUpload($file, $allowedExtensions))
        {
            // Can't upload the file
            return false;
        }

        if (file_exists(JPath::clean($path . '/' . $file['name'])))
        {
            $app->enqueueMessage(JText::_('COM_TEMPLATES_FILE_EXISTS'), 'error');

            return false;
        }

        if (!JFile::upload($file['tmp_name'], JPath::clean($path . '/' . $fileName)))
        {
            $app->enqueueMessage(JText::_('COM_VISFORMS_ERROR_FILE_UPLOAD'), 'error');

            return false;
        }

        $url = JPath::clean($fileName);

        return $url;
    }
    
    /**
	 * Create new file.
	 *
	 * @param   string  $name      The name of file.
	 * @param   string  $type      The extension of the file.
	 * @param   string  $location  Location for the new file.
	 *
	 * @return  boolean  true if file created successfully, false otherwise
	 *
	 * @since   3.2
	 */
	public function createFile($name, $type)
	{

        $app    = JFactory::getApplication();
        $path     = JPath::clean(JPATH_ROOT . '/media/com_visforms/css/');

        if (file_exists(JPath::clean($path . '/' . $name . '.' . $type)))
        {
            $app->enqueueMessage(JText::_('COM_VISFORMS_FILE_EXISTS'), 'error');

            return false;
        }

        if (!fopen(JPath::clean($path . '/' . $name . '.' . $type), 'x'))
        {
            $app->enqueueMessage(JText::_('COM_VISFORMS_FILE_CREATE_ERROR'), 'error');

            return false;
        }

        return true;
    }
    
    /**
	 * Delete a particular file.
	 *
	 * @param   string  $file  The relative location of the file.
	 *
	 * @return   boolean  True if file deletion is successful, false otherwise
	 *
	 * @since   3.2
	 */
	public function deleteFile($file)
	{

        $app      = JFactory::getApplication();
        $path     = JPath::clean(JPATH_ROOT . '/media/com_visforms/css/');
        $filePath = $path . urldecode(base64_decode($file));

        $return = JFile::delete($filePath);

        if (!$return)
        {
            $app->enqueueMessage(JText::_('COM_VISFORMS_FILE_DELETE_FAIL'), 'error');

            return false;
        }

        return true;
    }
    
    /**
	 * Rename a file.
	 *
	 * @param   string  $file  The name and location of the old file
	 * @param   string  $name  The new name of the file.
	 *
	 * @return  string  Encoded string containing the new file location.
	 *
	 * @since   3.2
	 */
	public function renameFile($file, $name)
	{		
			$app          = JFactory::getApplication();
			$path     = JPath::clean(JPATH_ROOT . '/media/com_visforms/css/');
			$fileName     = base64_decode($file);
			$explodeArray = explode('.', $fileName);
			$type         = end($explodeArray);
			$explodeArray = explode('/', $fileName);
			$newName      = str_replace(end($explodeArray), $name . '.' . $type, $fileName);

			if (file_exists($path . $newName))
			{
				$app->enqueueMessage(JText::_('COM_VISFORMS_FILE_EXISTS'), 'error');

				return false;
			}

			if (!rename($path . $fileName, $path . $newName))
			{
				$app->enqueueMessage(JText::_('COM_VISFORMS_ERROR_FILE_RENAME'), 'error');

				return false;
			}

			return base64_encode($newName);
	}
}
