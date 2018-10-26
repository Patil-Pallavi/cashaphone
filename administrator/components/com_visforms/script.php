<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class com_visformsInstallerScript
{
	private $release;
	private $oldRelease;
	private $minimum_joomla_release;
	private $maximum_joomla_release = 3;
	private $status;
	private $versionsWithPostflightFunction;

	public function __construct(JAdapterInstance $adapter) {
		$this->status = new stdClass();
		$this->status->fixTableVisforms = array();
		$this->status->modules = array();
		$this->status->plugins = array();
		$this->status->tables = array();
		$this->status->folders = array();
		$this->status->component = array();
		$this->status->messages = array();
		$this->release = $adapter->get("manifest")->version;
		$this->minimum_joomla_release = $adapter->get("manifest")->attributes()->version;
		$this->oldRelease = "";
		//list all no rollback versions here
		$this->versionsWithPostflightFunction = array('3.1.0', '3.2.0', '3.3.0', '3.4.0', '3.4.1', '3.5.0', '3.5.1', '3.5.3', '3.6.0', '3.6.3', '3.6.5', '3.7.0', '3.7.1', '3.8.8', '3.8.10', '3.8.12', '3.8.17', '3.8.19', '3.8.20', '3.9.1', '3.10.0', '3.10.1', '3.10.2','3.11.0', '3.11.1', '3.11.2');
	}

	public function preflight($route, JAdapterInstance $adapter) {
		$jversion = new JVersion();
		$msg = "";
		$date = new JDate('now');

		// abort if system requirements are not met
		if ($route != 'uninstall') {
			if (version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt')) {
				Jerror::raiseWarning(null, JText::_('COM_VISFORMS_WRONG_JOOMLA_VERSION') . $this->minimum_joomla_release);
				return false;
			}
			if (property_exists( 'JVersion', 'MAJOR_VERSION') && $jversion::MAJOR_VERSION > 3) {
				Jerror::raiseWarning(null, JText::sprintf('COM_VISFORMS_WRONG_MAX_JOOMLA_VERSION', $this->maximum_joomla_release));
				return false;
			}

			// abort if the component being installed is not newer than the currently installed version
			if ($route == 'update') {
				JLog::add("*** Start Update: " . $date . " ***", JLog::INFO);
				//set permissions for css files (which might be edited through backend and set to readonly) so they can be updated
				$files = array('visforms.css', 'visforms.min.css', 'bootstrapform.css');
				foreach ($files as $cssfile) {
					@chmod(JPath::clean(JPATH_ROOT . '/media/com_visforms/css/' . $cssfile), 0755);
				}
				$this->oldRelease = $this->getExtensionParam('version');
				$rel = $this->oldRelease . JText::_('COM_VISFORMS_TO') . $this->release;
				JLog::add("Installed version is: " . $this->oldRelease . " Update version is : " . $this->release, JLog::INFO);
				if (version_compare($this->release, $this->oldRelease, 'le')) {
					JLog::add("Update aborted due to wrong version sequence: " . $rel, JLog::ERROR);
					Jerror::raiseWarning(null, JText::_('COM_VISFORMS_WRONG_VERSION') . $rel);

					return false;
				} else {
					//process preflight for specific versions
					if (version_compare($this->oldRelease, '2.0.0', 'lt')) {
						JLog::add("Update aborted due to incompatible version with sequence: " . $rel, JLog::ERROR);
						Jerror::raiseWarning(null, JText::_('COM_VISFORMS_INCOMPATIBLE_VERSION') . $rel);

						return false;
					}
				}
			} else {
				JLog::add("*** Start Install: " . $date . " ***", JLog::INFO);
				JLog::add("Version is: " . $this->release, JLog::INFO);
				$rel = $this->release;
			}
			//create installation success message (only display if complete installation is executed successfully)
			if ($route == 'update') {
				$msg = JText::_('COM_VISFORMS_UPDATE_VERSION') . $rel . JText::_('COM_VISFORMS_SUCESSFULL');
				if (version_compare($this->oldRelease, '3.11.1', 'lt')) {
					$msg .= '<br /><strong style="color: red;">' . JText::_('COM_VISORMS_DELETE_TEMPLATE_OVERRIDES') . '</strong>';
				}
			} else {
				if ($route == 'install') {
					$msg = JText::_('COM_VISFORMS_INSTALL_VERSION') . $rel . JText::_('COM_VISFORMS_SUCESSFULL');
				}
			}

			$this->status->component = array('name' => 'visForms', 'type' => $route, 'msg' => $msg);
		}
	}

	public function postflight($route, JAdapterInstance $adapter) {
		if ($route == 'update') {
			//run specific component adaptation for specific update versions
			if ((!empty($this->oldRelease)) && ((version_compare($this->oldRelease, '3.0.0', 'ge')) || (version_compare($this->oldRelease, '2.2.0', 'lt')))) {
				foreach ($this->versionsWithPostflightFunction as $versionWithDatabaseChanges) {
					if (version_compare($this->oldRelease, '2.1.0', 'ge') && version_compare($this->oldRelease, '2.2.0', 'lt') && $versionWithDatabaseChanges == "3.1.0") {
						continue;
					}
					if (version_compare($this->oldRelease, $versionWithDatabaseChanges, 'lt')) {
						$postFlightFunctionPostfix = str_replace('.', '_', $versionWithDatabaseChanges);
						$postFlightFunctionName = 'postFlightForVersion' . $postFlightFunctionPostfix;
						if (method_exists($this, $postFlightFunctionName)) {
							$this->$postFlightFunctionName();
						}
					}
				}
				if (version_compare($this->oldRelease, '3.8.0', 'le')) {
					//check if plugin form view is installed and needs update
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select($db->qn('manifest_cache'))
						->from($db->qn('#__extensions'))
						->where($db->qn('element') . ' = ' . $db->q('vfformview'))
						->where($db->qn('type') . ' = ' . $db->q('plugin'))
						->where($db->qn('folder') . ' = ' . $db->q('content'));
					$db->setQuery($query);
					try {
						$manifest = json_decode($db->loadResult(), true);
						$version = $manifest['version'];
					} catch (Exception $e) {

					}
					if ((!empty($version)) && (version_compare($version, '1.5.0', 'lt'))) {
						JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_VISORMS_UPDATE_PAYED_EXTENSION', 'Content Plugin Visforms Formview', '1.5.0', $version), 'warning');
						$msg = '<br /><strong style="color: red;">' . JText::sprintf('COM_VISORMS_UPDATE_PAYED_EXTENSION', 'Content Plugin Visforms Formview', '1.5.0', $version) . '</strong>';
						$this->status->component = array('name' => 'visForms', 'type' => $route, 'msg' => $msg);
					}
				}
			}
		}

		if ($route == 'install') {
			$this->createFolder(array('images', 'visforms'));
		}

		//Install or update all extensions that come with component visForms
		$this->installExtensions($route, $adapter);
		$this->installationResults($route);
	}

	public function uninstall(JAdapterInstance $adapter) {

		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$tablesAllowed = $db->getTableList();
		if (!empty($tablesAllowed)) {
			$tablesAllowed = array_map('strtolower', $tablesAllowed);
		}

		$date = new JDate('now');
		JLog::add("*** Start Uninstall: " . $date . "***", JLog::INFO);
		JLog::add("Version is: " . $this->release, JLog::INFO);

		if ($db) {
			JLog::add("*** Try to delete tables ***", JLog::INFO);
			//delete all visforms related tables in database
			$db->setQuery("SELECT * FROM #__visforms");
			try {
				$forms = $db->loadObjectList();
			} catch (RuntimeException $e) {
				JLog::add('Unable to load form list from database: ' . $e->getMessage(), JLog::ERROR);
			}

			$n = count($forms);
			for ($i = 0; $i < $n; $i++) {
				$row = $forms[$i];
				$tnfulls = array(strtolower($db->getPrefix() . "visforms_" . $row->id), strtolower($db->getPrefix() . "visforms_" . $row->id . "_save"));
				foreach ($tnfulls as $tnfull) {
					if (in_array($tnfull, $tablesAllowed)) {
						$tn = str_replace(strtolower($db->getPrefix()), "#__", $tnfull);
						$db->setQuery("drop table if exists " . $tn);
						try {
							$db->execute();
							$message = JText::sprintf('COM_VISFORMS_SAVE_DATA_TABLE_DROPPED', $row->id);
							$this->status->tables[] = array('message' => $message);
							JLog::add('Table dropped: ' . $tn, JLog::INFO);
						} catch (RuntimeException $e) {
							$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
							$this->status->tables[] = array('message' => $message);
							JLog::add('Unable to drop table: ' . $tn . ', ' . $e->getMessage(), JLog::ERROR);
						}
					}
				}
			}

			$db->setQuery("drop table if exists #__visfields");
			try {
				$db->execute();
				$message = JText::_('COM_VISFORMS_FIELD_TABLE_DROPPED');
				$this->status->tables[] = array('message' => $message);
				JLog::add('Table dropped: #__visfields', JLog::INFO);
			} catch (RuntimeException $e) {
				$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
				$this->status->tables[] = array('message' => $message);
				JLog::add('Unable to drop table: #__visfields, ' . $e->getMessage(), JLog::ERROR);
			}

			$db->setQuery("drop table if exists #__visforms");
			try {
				$db->execute();
				$message = JText::_('COM_VISFORMS_FORMS_TABLE_DROPPED');
				$this->status->tables[] = array('message' => $message);
				JLog::add('Table dropped: #__visforms', JLog::INFO);
			} catch (RuntimeException $e) {
				$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
				$this->status->tables[] = array('message' => $message);
				JLog::add('Unable to drop table: #__visforms, ' . $e->getMessage(), JLog::ERROR);
			}

			$db->setQuery("drop table if exists #__visverificationcodes");
			try {
				$db->execute();
				$message = JText::_('COM_VISFORMS_FORMS_TABLE_DROPPED');
				$this->status->tables[] = array('message' => $message);
				JLog::add('Table dropped: #__visverificationcodes', JLog::INFO);
			} catch (RuntimeException $e) {
				$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
				$this->status->tables[] = array('message' => $message);
				JLog::add('Unable to drop table: #__visverificationcodes, ' . $e->getMessage(), JLog::ERROR);
			}
		}

		//uninstall plugins
		JLog::add("*** Try to uninstall extensions ***", JLog::INFO);
		$manifest = $adapter->getParent()->manifest;
		$plugins = $manifest->xpath('plugins/plugin');
		foreach ($plugins as $plugin) {
			$name = (string)$plugin->attributes()->plugin;
			$group = (string)$plugin->attributes()->group;
			$plgWhere = $db->quoteName('type') . ' = ' . $db->quote('plugin') . ' AND ' . $db->quoteName('element') . ' = ' . $db->quote($name) . ' AND ' . $db->quoteName('folder') . ' = ' . $db->quote($group);
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('extension_id'))
				->from($db->quoteName('#__extensions'))
				->where($plgWhere);
			$db->setQuery($query);
			try {
				$extensions = $db->loadColumn();
			} catch (RuntimeException $e) {
				JLog::add('Unable to get extension_id: ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
				continue;
			}
			if (count($extensions)) {
				foreach ($extensions as $id) {
					$installer = new JInstaller;
					try {
						$result = $installer->uninstall('plugin', $id);
						$this->status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
						if ($result) {
							JLog::add('Plugin sucessfully removed: ' . $name, JLog::INFO);
						} else {
							JLog::add('Removal of plugin failed: ' . $name, JLog::ERROR);
						}
					} catch (RuntimeException $e) {
						JLog::add('Removal of plugin failed: ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
					}
				}
			}
		}
		//uninstall modules
		$modules = $manifest->xpath('modules/module');
		foreach ($modules as $module) {
			$name = (string)$module->attributes()->module;
			$client = (string)$module->attributes()->client;
			if (is_null($client)) {
				$client = 'site';
			}
			if ($client == 'site') {
				$client_id = 0;
			} else {
				$client_id = 1;
			}
			$db = JFactory::getDbo();
			$modWhere = $db->quoteName('type') . ' = ' . $db->quote('module') . ' AND ' . $db->quoteName('element') . ' = ' . $db->quote($name) . ' AND ' . $db->quoteName('client_id') . ' = ' . $db->quote($client_id);
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('extension_id'))
				->from($db->quoteName('#__extensions'))
				->where($modWhere);
			$db->setQuery($query);
			try {
				$extensions = $db->loadColumn();
			} catch (RuntimeException $e) {
				JLog::add('Unable to get extension_id: ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
				continue;
			}
			if (count($extensions)) {
				foreach ($extensions as $id) {
					$installer = new JInstaller;
					try {
						$result = $installer->uninstall('module', $id);
						$this->status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
						if ($result) {
							JLog::add('Module sucessfully removed: ' . $name, JLog::INFO);
						} else {
							JLog::add('Removal of module failed: ' . $name, JLog::ERROR);
						}
					} catch (RuntimeException $e) {
						JLog::add('Removal of module failed: ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
					}
				}
			}
		}

		//delete folders in image folder
		JLog::add("*** Try to delete custom files and folders ***", JLog::INFO);
		jimport('joomla.filesystem.file');
		$folder = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'visforms';
		if (JFolder::exists($folder)) {
			$result = array();
			try {
				$result[] = JFolder::delete($folder);
				$this->status->folders[] = array('folder' => $folder, 'result' => $result[0]);
				if ($result[0]) {
					JLog::add("Folder successfully removed: " . $folder, JLog::INFO);
				} else {
					JLog::add('Problems removing folder: ' . $folder, JLog::ERROR);
				}
			} catch (RuntimeException $e) {
				JLog::add('Problems removing folder: ' . $folder . ', ' . $e->getMessage(), JLog::ERROR);
			}

		}

		//delete visuploads folder
		$folder = JPATH_ROOT . DIRECTORY_SEPARATOR . 'visuploads';
		if (JFolder::exists($folder)) {
			$result = array();
			try {
				$result[] = JFolder::delete($folder);
				$this->status->folders[] = array('folder' => $folder, 'result' => $result[0]);
				if ($result[0]) {
					JLog::add("Folder successfully removed: " . $folder, JLog::INFO);
				} else {
					JLog::add('Problems removing folder: ' . $folder, JLog::ERROR);
				}
			} catch (RuntimeException $e) {
				JLog::add('Problems removing folder: ' . $folder . ', ' . $e->getMessage(), JLog::ERROR);
			}
		}

		$this->uninstallationResults();
	}

	private function installationResults($route) {
		$language = JFactory::getLanguage();
		$language->load('com_visforms');
		$rows = 0;
		$image = ($route == 'update') ? 'logo-banner-u.png' : 'logo-banner.png';
		$src = "http://www.vi-solutions.de/images/f/$this->release/$image";
		$extension_message = array();
		$extension_message[] = ($route == 'update') ? '' : '<h2 style="text-align: center;">' . JText::_('COM_VISFORMS_INSTALL_MESSAGE') . '</h2>';
		$extension_message[] = '<img src="'.$src.'" alt="visForms" align="right" />';
		$extension_message[] = '<h2>' . (($route == 'update') ? JText::_('COM_VISFORMS_UPDATE_STATE') : JText::_('COM_VISFORMS_INSTALLATION_STATUS')) . '</h2>';
		$extension_message[] = '<table class="adminlist table table-striped">';
		$extension_message[] = '<thead>';
		$extension_message[] = '<tr>';
		$extension_message[] = '<th class="title" colspan="2" style="text-align: left;">' . JText::_('COM_VISFORMS_EXTENSION') . '</th>';
		$extension_message[] = '<th width="30%" style="text-align: left;">' . JText::_('COM_VISFORMS_STATUS') . '</th>';
		$extension_message[] = '</tr>';
		$extension_message[] = '</thead>';
		$extension_message[] = '<tfoot>';
		$extension_message[] = '<tr>';
		$extension_message[] = '<td colspan="3"></td>';
		$extension_message[] = '</tr>';
		$extension_message[] = '</tfoot>';
		$extension_message[] = '<tbody>';
		$extension_message[] = '<tr class="row0">';
		$extension_message[] = '<td class="key" colspan="2">' . JText::_('COM_VISFORMS_COMPONENT') . '</td>';
		$extension_message[] = '<td><strong>' . $this->status->component['msg'] . '</strong></td>';
		$extension_message[] = '</tr>';
		if (count($this->status->modules)):
			$extension_message[] = '<tr>';
			$extension_message[] = '<th>' . JText::_('COM_VISFORMS_MODULE') . '</th>';
			$extension_message[] = '<th>' . JText::_('COM_VISFORMS_CLIENT') . '</th>';
			$extension_message[] = '<th></th>';
			$extension_message[] = '</tr>';
			foreach ($this->status->modules as $module):
				$module_message = "";
				if (!isset($module['type'])) {
					$plugin_message = ($module['result']) ? '<strong>' . JText::_('COM_VISFORMS_REMOVED') : '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_REMOVED');
				} else {
					$module_message = ($module['result']) ? (($module['type'] == 'install') ? '<strong>' . JText::_('COM_VISFORMS_INSTALLED') : '<strong>' . JText::_('COM_VISFORMS_UPDATED')) : (($module['type'] == 'install') ? '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_INSTALLED') : '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_UPDATED'));
				}
				$extension_message[] = '<tr class="row' . (++$rows % 2) . '">';
				$extension_message[] = '<td class="key">' . $module['name'] . '</td>';
				$extension_message[] = '<td class="key">' . ucfirst($module['client']) . '</td>';
				$extension_message[] = '<td>' . $module_message . '</strong></td>';
				$extension_message[] = '</tr>';
			endforeach;
		endif;
		if (count($this->status->plugins)):
			$extension_message[] = '<tr>';
			$extension_message[] = '<th>' . JText::_('COM_VISFORMS_PLUGIN') . '</th>';
			$extension_message[] = '<th>' . JText::_('COM_VISFORMS_GROUP') . '</th>';
			$extension_message[] = '<th></th>';
			$extension_message[] = '</tr>';
			foreach ($this->status->plugins as $plugin):
				$plugin_message = '';
				if (!isset($plugin['type'])) {
					$plugin_message = ($plugin['result']) ? '<strong>' . JText::_('COM_VISFORMS_REMOVED') : '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_REMOVED');
				} else {
					$plugin_message = ($plugin['result']) ? (($plugin['type'] == 'install') ? '<strong>' . JText::_('COM_VISFORMS_INSTALLED') : '<strong>' . JText::_('COM_VISFORMS_UPDATED')) : (($plugin['type'] == 'install') ? '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_INSTALLED') : '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_UPDATED'));
				}
				$extension_message[] = '<tr class="row' . (++$rows % 2) . '">';
				$extension_message[] = '<td class="key">' . ucfirst($plugin['name']) . '</td>';
				$extension_message[] = '<td class="key">' . ucfirst($plugin['group']) . '</td>';
				$extension_message[] = '<td>' . $plugin_message . '</strong></td>';
				$extension_message[] = '</tr>';
			endforeach;
		endif;
		if (count($this->status->folders)):
			$extension_message[] = '<tr>';
			$extension_message[] = '<th colspan="2">' . JText::_('COM_VISFORMS_FILESYSTEM') . '</th>';
			$extension_message[] = '<th></th>';
			$extension_message[] = '</tr>';
			foreach ($this->status->folders as $folder):
				$folder_message = '';
				$folder_message = ($folder['result']) ? '<strong>' . JText::_('COM_VISFORMS_CREATED') : '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_CREATED');
				$extension_message[] = '<tr class="row' . (++$rows % 2) . '">';
				$extension_message[] = '<td class="key" colspan="2">' . ucfirst($folder['folder']) . '</td>';
				$extension_message[] = '<td>' . $folder_message . '</strong></td>';
				$extension_message[] = '</tr>';
			endforeach;
		endif;
		if (count($this->status->fixTableVisforms)):
			$extension_message[] = '<tr>';
			$extension_message[] = '<th colspan="2">' . JText::_('COM_VISFORMS_UPDATE_FIX_FOR_FORM_DATA') . '</th>';
			$extension_message[] = '<th></th>';
			$extension_message[] = '</tr>';
			foreach ($this->status->fixTableVisforms as $recordset):
				$table_message = '';
				$table_message = ($recordset['result']) ? '<strong>' . $recordset['resulttext'] : '<strong style="color: red">' . $recordset['resulttext'];
				$extension_message[] = '<tr class="row' . (++$rows % 2) . '">';
				$extension_message[] = '<td class="key" colspan="2">' . JText::_('COM_VISFORMS_FORM_WITH_ID') . $recordset['form'] . '</td>';
				$extension_message[] = '<td>' . $table_message . '</strong></td>';
				$extension_message[] = '</tr>';
			endforeach;
		endif;
		if (count($this->status->messages)) :
			$extension_message[] = '<tr>';
			$extension_message[] = '<th colspan="2">' . JText::_('COM_VISFORMS_MESSAGES') . '</th>';
			$extension_message[] = '<th></th>';
			$extension_message[] = '</tr>';
			foreach ($this->status->messages as $message) {
				$extension_message[] = '<tr class="row' . (++$rows % 2) . '">';
				$extension_message[] = '<td class="key" colspan="2"></td>';
				$extension_message[] = '<td><strong style="color: red">' . $message['message'] . '</strong></td>';
				$extension_message[] = '</tr>';
			}
		endif;
		$extension_message[] = '</tbody>';
		$extension_message[] = '</table>';
		$msg_string = implode(' ', $extension_message);
		$jversion = new JVersion();
		if (($route == 'update') && version_compare($jversion->getShortVersion(), '3.4.0', 'ge') && version_compare($jversion->getShortVersion(), '3.8.0', 'lt')) {
			$app = JFactory::getApplication();
			$app->setUserState('com_installer.redirect_url', 'index.php?option=com_visforms');
			$app->setUserState('com_visforms.update_message', $msg_string);
		} else {
			echo $msg_string;
		}
	}

	private function uninstallationResults() {
		$language = JFactory::getLanguage();
		$language->load('com_visforms');
		$rows = 0;
		$src = "http://www.vi-solutions.de/images/f/$this->release/logo-banner-d.png";
		?>
        <img src="<?php echo $src; ?>" alt="visForms" align="right" />
        <h2><?php echo JText::_('COM_VISFORMS_REMOVAL_STATUS'); ?></h2>
        <table class="adminlist table table-striped">
            <thead>
            <tr>
                <th class="title" colspan="2"
                    style="text-align: left;"><?php echo JText::_('COM_VISFORMS_EXTENSION'); ?></th>
                <th width="30%" style="text-align: left;"><?php echo JText::_('COM_VISFORMS_STATUS'); ?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="3"></td>
            </tr>
            </tfoot>
            <tbody>
            <tr class="row0">
                <td class="key" colspan="2"><?php echo JText::_('COM_VISFORMS_COMPONENT'); ?></td>
                <td><strong><?php echo JText::_('COM_VISFORMS_REMOVED'); ?></strong></td>
            </tr>
			<?php if (count($this->status->modules)): ?>
                <tr>
                    <th><?php echo JText::_('COM_VISFORMS_MODULE'); ?></th>
                    <th><?php echo JText::_('COM_VISFORMS_CLIENT'); ?></th>
                    <th></th>
                </tr>
				<?php foreach ($this->status->modules as $module): ?>
                    <tr class="row<?php echo(++$rows % 2); ?>">
                        <td class="key"><?php echo $module['name']; ?></td>
                        <td class="key"><?php echo ucfirst($module['client']); ?></td>
                        <td><?php echo ($module['result']) ? '<strong>' . JText::_('COM_VISFORMS_REMOVED') : '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_REMOVED'); ?></strong></td>
                    </tr>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if (count($this->status->plugins)): ?>
                <tr>
                    <th><?php echo JText::_('COM_VISFORMS_PLUGIN'); ?></th>
                    <th><?php echo JText::_('COM_VISFORMS_GROUP'); ?></th>
                    <th></th>
                </tr>
				<?php foreach ($this->status->plugins as $plugin): ?>
                    <tr class="row<?php echo(++$rows % 2); ?>">
                        <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                        <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                        <td><?php echo ($plugin['result']) ? '<strong>' . JText::_('COM_VISFORMS_REMOVED') : '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_REMOVED'); ?></strong></td>
                    </tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if (count($this->status->tables)) { ?>
                <tr>
                    <th><?php echo JText::_('COM_VISFORMS_TABLES'); ?></th>
                    <th></th>
                    <th></th>
                </tr>
				<?php foreach ($this->status->tables as $table) { ?>
                    <tr class="row<?php echo(++$rows % 2); ?>">
                        <td class="key" colspan="3"><?php echo ucfirst($table['message']); ?></td>
                    </tr>
				<?php } ?>
			<?php } ?>
			<?php if (count($this->status->folders)): ?>
                <tr>
                    <th colspan="2"><?php echo JText::_('COM_VISFORMS_FILESYSTEM'); ?></th>
                    <th></th>
                </tr>
				<?php foreach ($this->status->folders as $folder): ?>
                    <tr class="row<?php echo(++$rows % 2); ?>">
                        <td class="key" colspan="2"><?php echo ucfirst($folder['folder']); ?></td>
                        <td><?php echo ($folder['result']) ? '<strong>' . JText::_('COM_VISFORMS_DELETED') : '<strong style="color: red">' . JText::_('COM_VISFORMS_NOT_DELETED'); ?></strong></td>
                    </tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if (count($this->status->messages)) : ?>
                <tr>
                    <th colspan="2"><?php echo JText::_('COM_VISFORMS_MESSAGES'); ?></th>
                    <th></th>
                </tr>
				<?php foreach ($this->status->messages as $message) {
					?>
                    <tr class="row<?php echo(++$rows % 2); ?>">
                        <td class="key" colspan="2"></td>
                        <td><?php echo '<strong style="color: red">' . $message['message'] . '</strong>'; ?></td>
                    </tr>
				<?php } ?>
			<?php endif; ?>
            </tbody>
        </table>
		<?php
	}

	private function createFolder($folders = array()) {

		JLog::add("*** Try to create folders ***", JLog::INFO);
		//create visforms folder in image directory and copy an index.html into it
		jimport('joomla.filesystem.file');
		$folder = JPATH_ROOT;
		foreach ($folders as $name) {
			$folder .= DIRECTORY_SEPARATOR . $name;
		}

		if (($folder != JPATH_ROOT) && !(JFolder::exists($folder))) {
			$result = array();
			try {
				$result[] = JFolder::create($folder);
				$this->status->folders[] = array('folder' => $folder, 'result' => $result[0]);
				if ($result[0]) {
					JLog::add("Folder successfully created: " . $folder, JLog::INFO);
				} else {
					JLog::add("Problems creating folder: " . $folder, JLog::ERROR);
				}
			} catch (RuntimeException $e) {
				JLog::add("Problems creating folders, " . $e->getMessage(), JLog::ERROR);
			}

			$src = JPATH_ROOT . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_visforms' . DIRECTORY_SEPARATOR . 'index.html';
			$dest = JPath::clean($folder . DIRECTORY_SEPARATOR . 'index.html');

			try {
				$result[] = JFile::copy($src, $dest);
				$this->status->folders[] = array('folder' => $folder . DIRECTORY_SEPARATOR . 'index.html', 'result' => $result[1]);
				if ($result[1]) {
					JLog::add("File successfully copied: " . $dest, JLog::INFO);
				} else {
					JLog::add("Problems copying file: " . $dest, JLog::ERROR);
				}
			} catch (RuntimeException $e) {
				JLog::add("Problems copying files, " . $e->getMessage(), JLog::ERROR);
			}
		}
	}

	public function installExtensions($route, JAdapterInstance $adapter) {
		JLog::add("*** Try to install extensions ***", JLog::INFO);
		$db = JFactory::getDbo();
		$src = $adapter->getParent()->getPath('source');
		$manifest = $adapter->getParent()->manifest;
		$types = array(array('libraries', 'library'), array('plugins', 'plugin'), array('modules', 'module'));
		foreach ($types as $type) {
			$etype = $type[0];
			$ename = $type[1];
			$xmldefs = $manifest->xpath($etype . '/' . $ename);
			foreach ($xmldefs as $xmldef) {
				$name = (string)$xmldef->attributes()->$ename;
				$newVersion = (string)$xmldef->attributes()->version;
				$version = "";
				$extWhere = $db->quoteName('type') . ' = ' . $db->quote($ename) . ' AND ' . $db->quoteName('element') . ' = ' . $db->quote($name);
				if ($ename == 'plugin') {
					$group = (string)$xmldef->attributes()->group;
					$path = $src . '/' . $etype . '/' . $group;
					if (JFolder::exists($src . '/' . $etype . '/' . $group . '/' . $name)) {
						$path = $src . '/' . $etype . '/' . $group . '/' . $name;
					}
					$extWhere .= ' AND ' . $db->quoteName('folder') . ' = ' . $db->quote($group);
				}
				if ($ename == 'module') {
					$client = (string)$xmldef->attributes()->client;
					if (is_null($client)) {
						$client = 'site';
					}
					if ($client == 'site') {
						$client_id = 0;
					} else {
						$client_id = 1;
					}
					($client == 'administrator') ? $path = $src . '/administrator/' . $etype . '/' . $name : $path = $src . '/' . $etype . '/' . $name;
					$extWhere .= ' AND ' . $db->quoteName('client_id') . ' = ' . $db->quote($client_id);
				}
				if ($ename == 'library') {
					$path = $src . '/' . $etype . '/' . $name;
				}
				$query = $db->getQuery(true);
				$query
					->select($db->quoteName('extension_id'))
					->from($db->quoteName('#__extensions'))
					->where($extWhere);
				$db->setQuery($query);
				$extension = array();
				try {
					$extension = $db->loadColumn();
				} catch (RuntimeException $e) {
					$message = JText::sprintf('COM_VISFORMS_UNABLE_TO_GET_EXTENSION_ID', $name) . " " . JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
					$this->status->messages[] = array('message' => $message);
					JLog::add('Unable to get extension_id: ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
					continue;
				}
				$installer = new JInstaller;
				if (count($extension)) {
					//make sure we have got only on id, if not use the first
					if (is_array($extension)) {
						$extension = $extension[0];
					}
					//check if we need to update
					try {
						$version = $this->getExtensionParam('version', (int)$extension);
					} catch (RuntimeException $e) {
						$message = JText::sprintf('COM_VISFORMS_UNABLE_TO_GET_EXTENSION_PARAMS', $name) . " " . JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
						$this->status->messages[] = array('message' => $message);
						JLog::add('Unable to get ' . $ename . ' params: ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
						continue;
					}
					if (version_compare($newVersion, $version, 'gt')) {
						$installationType = "update";
					}
				} else {
					$installationType = "install";
				}
				if (isset($installationType)) {
					try {
						$result = $installer->$installationType($path);
						$resultArray = array('name' => $name, 'result' => $result, 'type' => $installationType);
						if ($ename == "plugin") {
							$resultArray['group'] = $group;
							$this->status->plugins[] = $resultArray;
							//we have to enable the content plugin visforms
							if ($name == 'visforms') {
								JLog::add("Try to enable " . $ename . " " . $name, JLog::INFO);
								$this->enableExtension($extWhere);
							}
							//enable plugin visform spambotcheck
							if ($name == 'spambotcheck') {
								JLog::add("Try to enable " . $ename . " " . $name, JLog::INFO);
								$this->enableExtension($extWhere);
							}
							//enable plugin editor-xtd visformfields
							if ($name == 'visformfields') {
								JLog::add("Try to enable " . $ename . " " . $name, JLog::INFO);
								$this->enableExtension($extWhere);
							}

						}
						if ($ename == "module") {
							$resultArray['client'] = $client;
							$this->status->modules[] = $resultArray;
						}
						if ($result) {
							JLog::add($installationType . " of " . $ename . ' sucessfully: ' . $name, JLog::INFO);
						} else {
							JLog::add($installationType . " of " . $ename . ' failed: ' . $name, JLog::ERROR);
						}
					} catch (RuntimeException $e) {
						JLog::add($installationType . " of " . $ename . ' failed: ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
					}
					unset($installationType);
				}
			}
		}
	}

	private function getExtensionParam($name, $eid = 0) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		//check if a extenstion id is given. If yes we want a parameter from this extension
		if ($eid != 0) {
			$query->where($db->quoteName('extension_id') . ' = ' . $db->quote($eid));
		} else {
			//we want a parameter from component visForms
			$query->where($this->getComponentWhereStatement());
		}

		$db->setQuery($query);
		try {
			$manifest = json_decode($db->loadResult(), true);
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_UNABLE_TO_GET_VALUE_OF_PARAM', $name) . " " . JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add('Unable to get value of param ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
		}

		return $manifest[$name];
	}

	private function setExtensionParams($param_array) {
		if (count($param_array) > 0) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('params'))
				->from($db->quoteName('#__extensions'))
				->where($this->getComponentWhereStatement());
			$db->setQuery($query);
			$params = json_decode($db->loadResult(), true);
			foreach ($param_array as $name => $value) {
				$params[(string)$name] = (string)$value;
			}
			$paramsString = json_encode($params);
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote($paramsString) . ' WHERE ' . $this->getComponentWhereStatement());
			$db->execute();
		}
	}

	private function setParams($param_array, $table, $fieldName, $where = "") {

		if (count($param_array) > 0) {
			JLog::add("*** Try to add params to table: #__" . $table . " ***", JLog::INFO);
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName(array('id', $fieldName)))
				->from($db->quoteName('#__' . $table));
			if ($where != "") {
				$query->where($where);
			}

			$db->setQuery($query);
			$results = new stdClass();
			try {
				$results = $db->loadObjectList();
				JLog::add(count($results) . ' recordsets to process', JLog::INFO);
			} catch (RuntimeException $e) {
				JLog::add('Unable to load param fields, ' . $e->getMessage(), JLog::ERROR);
			}
			if ($results) {
				foreach ($results as $result) {
					$params = json_decode($result->$fieldName, true);
					// add the new variable(s) to the existing one(s)
					foreach ($param_array as $name => $value) {
						$params[(string)$name] = (string)$value;
					}
					// store the combined new and existing values back as a JSON string
					$paramsString = json_encode($params);
					$db->setQuery('UPDATE #__' . $table . ' SET ' . $fieldName . ' = ' .
						$db->quote($paramsString) . ' WHERE id=' . $result->id);
					try {
						$db->execute();
						JLog::add("Params successfully added", JLog::INFO);
					} catch (RuntimeException $e) {
						JLog::add('Problems with adding params ' . $e->getMessage(), JLog::ERROR);
					}
				}
			}
		}
	}

	//create where statement to select visforms component record in #__extensions table
	private function getComponentWhereStatement() {
		$db = JFactory::getDbo();
		$where = $db->quoteName('type') . ' = ' . $db->quote('component') . ' AND ' . $db->quoteName('element') . ' = ' . $db->quote('com_visforms') . ' AND ' . $db->quoteName('name') . ' = ' . $db->quote('visforms');

		return $where;
	}

	private function deleteOldFiles($filesToDelete = array(), $foldersToDelete = array()) {
		JLog::add('*** Try to delete old files ***', JLog::INFO);
		jimport('joomla.filesystem.file');
		foreach ($filesToDelete as $fileToDelete) {
			$oldfile = JPath::clean(JPATH_ROOT . $fileToDelete);
			if (JFile::exists($oldfile)) {
				try {
					JFile::delete($oldfile);
					JLog::add($oldfile . " deleted", JLog::INFO);
				} catch (RuntimeException $e) {
					JLog::add('Unable to delete ' . $oldfile . ': ' . $e->getMessage(), JLog::INFO);
					throw $e;
				}
			} else {
				JLog::add($oldfile . " does not exist.", JLog::INFO);
			}

		}
		foreach ($foldersToDelete as $folderToDelete) {
			$folder = JPath::clean(JPATH_ROOT . $folderToDelete);
			if (JFolder::exists($folder)) {
				try {
					JFolder::delete($folder);
					JLog::add($folder . "deleted", JLog::INFO);
				} catch (RuntimeException $e) {
					JLog::add('Unable to delete ' . $folder . ': ' . $e->getMessage(), JLog::INFO);
					throw $e;
				}
			} else {
				JLog::add($folder . " does not exist.", JLog::INFO);
			}

		}

	}

	private function postFlightForVersion3_1_0() {
		JLog::add('*** Perform postflight for Version 3.1.0 ***', JLog::INFO);
		JLog::add('*** Try to add columns to table: #__visforms ***', JLog::INFO);
		//Add new fields to table visforms
		$columnsToAdd = array('emailreceiptsettings', 'frontendsettings');
		$db = JFactory::getDbo();
		foreach ($columnsToAdd as $columnToAdd) {
			$queryStr = $db->getQuery(true);
			$queryStr = ("ALTER TABLE " . $db->quoteName('#__visforms') . "ADD COLUMN " . $db->quoteName($columnToAdd) . " text");
			$db->setQuery($queryStr);
			try {
				$db->execute();
				JLog::add('Column added: ' . $columnToAdd, JLog::INFO);
			} catch (RuntimeException $e) {
				$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
				$this->status->messages[] = array('message' => $message);
				JLog::add('Unable to add column: ' . $columnToAdd . ': ' . $e->getMessage(), JLog::ERROR);
			}

		}
		JLog::add('*** Try to delete old files no longer used ***', JLog::INFO);
		$filesToDelete = array(
			'/administrator/components/com_visforms/images/icon-16-visforms.png',
			'/adminstrator/components/com_visforms/views/vistools/tmpl/css.php',
			'/components/com_visforms/captcha/images/audio_icon.gif'
		);
		JLog::add(count($filesToDelete) . " files to delete", JLog::INFO);
		try {
			$this->deleteOldFiles($filesToDelete);
		} catch (Exception $e) {
			JLog::add('Problems deleting old files: ' . $e->getMessage(), JLog::WARNING);
		}
		JLog::add('*** Try to run fixTableVisforms3_1_0 ***', JLog::INFO);
		try {
			$this->fixTableVisforms3_1_0();
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_PROBLEM_UPDATE_DATABASE', 'fixTableVisforms3_1_0') . " " . JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add('Problems with update of tables: #__visforms', JLog::ERROR);
		}
		//add new menu params
		JLog::add('*** Try to add new menu params ***', JLog::INFO);
		$menu_params = array('sortorder' => 'id', 'display_num' => '20');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName(array('id', 'link', 'params')))
			->from($db->quoteName('#__menu'))
			->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$db->setQuery($query);
		$menus = new stdClass();
		try {
			$menus = $db->loadObjectList();
		} catch (RuntimeException $e) {
			$message = JText::_('COM_VISFORMS_UNABLE_TO_UPDATE_MENU_PARAMS') . " " . JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add('Unable to load menu params: ' . $e->getMessage(), JLog::WARNING);
		}
		if ($menus) {

			foreach ($menus as $menu) {
				if ((isset($menu->link)) && ($menu->link != "") && (strpos($menu->link, "view=visformsdata") !== false)) {
					$params = json_decode($menu->params, true);
					// add the new variable(s) to the existing one(s)
					foreach ($menu_params as $name => $value) {
						$params[(string)$name] = (string)$value;
						// store the combined new and existing values back as a JSON string
						$paramsString = json_encode($params);
						$db->setQuery('UPDATE #__menu SET params = ' .
							$db->quote($paramsString) . ' WHERE ' . $db->quoteName('id') . ' = ' . $db->quote($menu->id));
						try {
							$db->execute();
							JLog::add('Param added: ' . $name . 'to menu with id: ' . $menu->id, JLog::INFO);
						} catch (RuntimeException $e) {
							JLog::add('Unable to add param :' . $name . 'to menu with id: ' . $menu->id . " " . $e->getMessage(), JLog::ERROR);
						}
					}
				}
			}
		}
	}

	private function fixTableVisforms3_1_0() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		JLog::add('*** Try to move emailreceipt params into new param field emailreceiptsettings ***', JLog::INFO);
		$query
			->select($db->quoteName(array('id', 'emailreceiptincfield', 'emailreceiptincfile', 'emailrecipientincfilepath')))
			->from($db->quoteName('#__visforms'));
		$db->setQuery($query);
		try {
			$forms = $db->loadObjectList();
			JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($forms) > 0) {
			foreach ($forms as $form) {
				$emailreceiptsettings = array();
				if (isset($form->emailreceiptincfield)) {
					$emailreceiptsettings['emailreceiptincfield'] = $form->emailreceiptincfield;
				} else {
					$emailreceiptsettings['emailreceiptincfield'] = 0;
				}
				if (isset($form->emailreceiptincfile)) {
					$emailreceiptsettings['emailreceiptincfile'] = $form->emailreceiptincfile;
				} else {
					$emailreceiptsettings['emailreceiptincfile'] = 0;
				}
				if (isset($form->emailrecipientincfilepath)) {
					$emailreceiptsettings['emailrecipientincfilepath'] = $form->emailrecipientincfilepath;
				} else {
					$emailreceiptsettings['emailrecipientincfilepath'] = 0;
				}
				$emailreceiptsettings['emailreceiptinccreated'] = 1;
				$emailreceiptsettings['emailreceiptincformtitle'] = 1;
				if (is_array($emailreceiptsettings)) {
					$registry = new JRegistry;
					$registry->loadArray($emailreceiptsettings);
					$emailreceiptsettings = (string)$registry;
					$query = $db->getQuery(true);
					$query->update($db->quoteName('#__visforms'))
						->set($db->quoteName('emailreceiptsettings') . " = " . $db->quote($emailreceiptsettings))
						->where($db->quoteName('id') . " = " . $db->quote($form->id));
					$db->setQuery($query);
					try {
						$result = $db->execute();
						JLog::add('Update successfull for form with id: ' . $form->id, JLog::INFO);
					} catch (RuntimeException $e) {
						JLog::add('Problems with update for form with id: ' . $form->id . ', ' . $e->getMessage(), JLog::ERROR);
					}
				} else {
					JLog::add('Frontendsettings have invalid type. Cannot update form with id: ' . $form->id, JLog::ERROR);
				}
			}
			JLog::add("*** Try to drop fields from table #__visforms ***", JLog::INFO);
			$columnsToDelete = array('emailreceiptincfield', 'emailreceiptincfile', 'emailrecipientincfilepath');
			JLog::add(count($columnsToDelete) . " fields to drop", JLog::INFO);
			foreach ($columnsToDelete as $columnToDelete) {
				$queryStr = ("ALTER TABLE " . $db->quoteName('#__visforms') . "DROP COLUMN " . $db->quoteName($columnToDelete));
				$db->setQuery($queryStr);
				try {
					$db->execute();
					JLog::add("Field successfully dropped: " . $columnToDelete, JLog::INFO);
				} catch (RuntimeException $e) {
					JLog::add("Problems dropping field: " . $columnToDelete . ', ' . $e->getMessage(), JLog::ERROR);
				}
			}
		}
		JLog::add('*** Try to move params for frontend display into new param field frontendsettings ***', JLog::INFO);
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName(array('id', 'displayip', 'displaydetail', 'autopublish')))
			->from($db->quoteName('#__visforms'));
		$db->setQuery($query);
		try {
			$forms = $db->loadObjectList();
			JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($forms) > 0) {
			foreach ($forms as $form) {
				$frontendsettings = array();
				if (isset($form->displayip)) {
					$frontendsettings['displayip'] = $form->displayip;
				} else {
					$frontendsettings['displayip'] = 0;
				}
				if (isset($form->displaydetail)) {
					$frontendsettings['displaydetail'] = $form->displaydetail;
				} else {
					$frontendsettings['displaydetail'] = 0;
				}
				if (isset($form->autopublish)) {
					$frontendsettings['autopublish'] = $form->autopublish;
				} else {
					$frontendsettings['autopublish'] = 1;
				}
				$frontendsettings['displayid'] = 0;
				if (is_array($frontendsettings)) {
					$registry = new JRegistry;
					$registry->loadArray($frontendsettings);
					$frontendsettings = (string)$registry;
					$query = $db->getQuery(true);
					$query->update($db->quoteName('#__visforms'))
						->set($db->quoteName('frontendsettings') . " = " . $db->quote($frontendsettings))
						->where($db->quoteName('id') . " = " . $db->quote($form->id));
					$db->setQuery($query);
					try {
						$result = $db->execute();
						JLog::add('Update successfull for form with id: ' . $form->id, JLog::INFO);
					} catch (RuntimeException $e) {
						JLog::add('Problems with update for form with id: ' . $form->id, JLog::ERROR);
					}
				} else {
					JLog::add('Frontendsettings have invalid type. Cannot update form with id: ' . $form->id, JLog::ERROR);
				}
			}
			JLog::add("*** Try to drop fields from table #__visforms ***", JLog::INFO);
			$columnsToDelete = array('displayip', 'displaydetail', 'autopublish');
			JLog::add(count($columnsToDelete) . " fields to drop", JLog::INFO);
			foreach ($columnsToDelete as $columnToDelete) {
				$queryStr = ("ALTER TABLE " . $db->quoteName('#__visforms') . "DROP COLUMN " . $db->quoteName($columnToDelete));
				$db->setQuery($queryStr);
				try {
					$db->execute();
					JLog::add("Field successfully dropped: " . $columnToDelete, JLog::INFO);
				} catch (RuntimeException $e) {
					JLog::add("Problems dropping field: " . $columnToDelete . ', ' . $e->getMessage(), JLog::ERROR);
				}
			}
		}
	}

	private function postFlightForVersion3_2_0() {
		JLog::add('*** Perform postflight for Version 3.2.0 ***', JLog::INFO);
		$db = JFactory::getDbo();
		try {
			$this->addColumns(array('allowurlparam' => array('name' => 'allowurlparam', 'type' => 'TINYINT', 'length' => '4', 'notNull' => true, 'default' => '0'),
				'customtextposition' => array('name' => 'customtextposition', 'type' => 'TINYINT', 'length' => '4', 'notNull' => true, 'default' => '0'),
				'uniquevaluesonly' => array('name' => 'uniquevaluesonly', 'type' => 'TINYINT', 'length' => '4', 'notNull' => true, 'default' => '0'),
				'restrictions' => array('name' => 'restrictions', 'type' => 'TEXT')
			),
				'visfields');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visfields, " . $e->getMessage(), JLog::ERROR);
		}
		try {
			$this->addColumns(array('layoutsettings' => array('name' => 'layoutsettings', 'type' => 'TEXT'),
					'emailreceiptfrom' => array('name' => 'emailreceiptfrom', 'type' => 'TEXT'),
					'emailreceiptfromname' => array('name' => 'emailreceiptfromname', 'type' => 'TEXT')
				)
			);
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}

		try {
			$this->convertParamsToJsonField('layoutsettings',
				array('formCSSclass' => "", 'required' => 'top'),
				array('formlayout' => 'visforms', 'usebootstrapcss' => '0', 'requiredasterix' => '1')
			);
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems converting params in table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
		try {
			$this->dropColumns(array('formCSSclass', 'required'));
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems with dropping fields from table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}

		try {
			$this->setParams(array('f_submit_attribute_class' => 'btn ', 'f_reset_attribute_class' => 'btn '), 'visfields', 'defaultvalue', $db->quoteName('typefield') . " in ( " . $db->quote('submit') . ", " . $db->quote('reset') . ")");
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Unable to set params in table #__visfields, " . $e->getMessage(), JLog::WARNING);
		}
		try {
			$this->setParams(array('emailreceiptincip' => '1'), 'visforms', 'emailreceiptsettings');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Unable to set params in table #__visforms, " . $e->getMessage(), JLog::WARNING);
		}
		JLog::add("*** Try to set values in field emailreceiptfrom and emailreceiptfromname in table: #__visforms ***", JLog::INFO);
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'emailfrom', 'emailfromname')))
			->from('#__visforms');
		$db->setQuery($query);
		try {
			$forms = $db->loadObjectList();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($forms) > 0) {
			JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
			foreach ($forms as $form) {
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__visforms'))
					->set($db->quoteName('emailreceiptfrom') . ' = ' . $db->quote($form->emailfrom) . ', ' . $db->quoteName('emailreceiptfromname') . ' = ' . $db->quote($form->emailfromname))
					->where($db->quoteName('id') . " = " . $db->quote($form->id));
				$db->setQuery($query);
				try {
					$result = $db->execute();
					JLog::add("Value successfully set for form with id: " . $form->id, JLog::INFO);
				} catch (RuntimeException $e) {
					$this->status->fixTableVisforms[] = array('form' => $form->id, 'result' => false, 'resulttext' => JText::sprintf('COM_VISFORMS_EMAIL_ADDRESS_FIELD_UPDATE_FAILED', JText::_('COM_VISFORMS_EMAIL_RECEIPT_FROM')));
					JLog::add("Problems setting value for form with id: " . $form->id . ', ' . $e->getMessage(), JLog::ERROR);
				}
			}
		} else {
			JLog::add("No form recordsets to process", JLog::INFO);
		}
		//enforce creation of _save datatable
		try {
			$this->createDataTableSave3_2_0();
		} catch (RuntimeException $e) {
			JLog::add("Problems creating _save tables, " . $e->getMessage(), JLog::ERROR);
		}
		//Add column ismfd to data tables
		try {
			$this->updateDataTable3_2_0();
		} catch (RuntimeException $e) {
			JLog::add("Problems updateing data tables, " . $e->getMessage(), JLog::ERROR);
		}
		//convert option list of radio buttons and selects from former custom format string to json in table visfields
		try {
			$this->convertSelectRadioOptionList();
		} catch (RuntimeException $e) {
			JLog::add("Problems converting option list string, " . $e->getMessage(), JLog::ERROR);
		}
	}

	private function postFlightForVersion3_3_0() {
		JLog::add('*** Perform postflight for Version 3.3.0 ***', JLog::INFO);
		//create visforms table field spamprotection
		$this->addColumns(array('spamprotection' => array('name' => 'spamprotection', 'type' => 'TEXT')));
		//copy params from plg_visforms_spambotcheck into forms or set default values in form
		JLog::add("*** Try to copy params from Plugin Visforms Spambotcheck to forms ***", JLog::INFO);
		$plgParamsForm = array("spbot_check_ip" => "1",
			"spbot_check_email" => "1",
			"allow_generic_email_check" => "0",
			"spbot_whitelist_email" => "",
			"spbot_whitelist_ip" => "",
			"spbot_log_to_db" => "0",
			"spbot_stopforumspam" => "1",
			"spbot_stopforumspam_max_allowed_frequency" => "0",
			"spbot_projecthoneypot" => "0",
			"spbot_projecthoneypot_api_key" => "",
			"spbot_projecthoneypot_max_allowed_threat_rating" => "0",
			"spbot_sorbs" => "1",
			"spbot_spamcop" => "1",
			"spbot_blacklist_email" => "");

		$newPlgParamsForm = $this->getPlgvscParmas($plgParamsForm);

		if (is_array($newPlgParamsForm)) {
			$plgParamsForm = $newPlgParamsForm;
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update($plgParamsForm);
		$registry = new JRegistry;
		$registry->loadArray($plgParamsForm);
		$plgParamsForm = (string)$registry;
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__visforms'))
			->set($db->quoteName('spamprotection') . " = " . $db->quote($plgParamsForm));
		$db->setQuery($query);
		try {
			$db->execute();
			JLog::add("Plugin Visforms Spambotcheck params added to forms", JLog::INFO);
		} catch (RuntimeException $e) {
			JLog::add("Unable to add plugin Visforms Spambotcheck params to forms: " . $e->getMessage(), JLog::ERROR);
		}

	}

	private function postFlightForVersion3_4_0() {
		JLog::add('*** Perform postflight for Version 3.4.0 ***', JLog::INFO);
		try {
			$this->setParams(array('allowfedv' => '1', 'displaycreated' => '0', 'displaycreatedtime' => '0'), 'visforms', 'frontendsettings');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Unable to set params in table #__visforms, " . $e->getMessage(), JLog::WARNING);
		}
		JLog::add("*** Try to set frontendaccess in table: #__visforms ***", JLog::INFO);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'access')))
			->from('#__visforms');
		$db->setQuery($query);
		try {
			$forms = $db->loadObjectList();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if ($forms) {
			JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
			foreach ($forms as $form) {
				try {
					$this->setParams(array('frontendaccess' => $form->access), 'visforms', 'frontendsettings', $db->quoteName('id') . " = " . $db->quote($form->id));
					JLog::add("Value successfully set for form with id: " . $form->id, JLog::INFO);
				} catch (RuntimeException $e) {
					$this->status->fixTableVisforms[] = array('form' => $form->id, 'result' => false, 'resulttext' => JText::sprintf('COM_VISFORMS_EMAIL_ADDRESS_FIELD_UPDATE_FAILED', 'frontendaccess'));
					JLog::add("Problems setting value for form with id: " . $form->id . ', ' . $e->getMessage(), JLog::ERROR);
				}
			}
		} else {
			JLog::add("No form recordsets to process", JLog::INFO);
		}
	}

	private function postFlightForVersion3_4_1() {
		JLog::add('*** Perform postflight for Version 3.4.1 ***', JLog::INFO);
		$this->addColumns(array('captchaoptions' => array('name' => 'captchaoptions', 'type' => 'TEXT')));
		try {
			$this->convertParamsToJsonField('captchaoptions',
				array('captchacustominfo' => '', 'captchacustomerror' => ''),
				array('captchalabel' => 'Captcha', 'showcaptchalabel' => '0')
			);
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems converting params in table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
		try {
			$this->dropColumns(array('captchacustominfo', 'captchacustomerror'));
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems with dropping fields from table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
	}

	private function postFlightForVersion3_5_0() {
		JLog::add('*** Perform postflight for Version 3.5.0 ***', JLog::INFO);
		try {
			$this->addColumns(array('bootstrap_size' => array('name' => 'bootstrap_size', 'type' => 'TINYINT', 'length' => '3', 'notNull' => true, 'attribute' => 'unsigned', 'default' => 0)), 'visfields');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
	}

	private function postFlightForVersion3_5_1() {
		JLog::add('*** Perform postflight for Version 3.5.1 ***', JLog::INFO);
		try {
			$this->addColumns(array('viscaptchaoptions' => array('name' => 'viscaptchaoptions', 'type' => 'text'),
					'emailresultsettings' => array('name' => 'emailresultsettings', 'type' => 'TEXT'),
					'emailresulttext' => array('name' => 'emailresulttext', 'type' => 'LONGTEXT')
				)
			);
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}

		try {
			$this->convertParamsToJsonField('viscaptchaoptions',
				array(),
				array('image_width' => '215', 'image_height' => '80', 'image_bg_color' => '#ffffff', 'text_color' => '#616161', 'line_color' => '#616161',
					'noise_color' => '#616161', 'text_transparency_percentage' => '50', 'use_transparent_text' => '0', 'code_length' => '6', 'case_sensitive' => '0',
					'perturbation' => '0.75', 'num_lines' => '8', 'captcha_type' => 'self::SI_CAPTCHA_STRING'
				)
			);
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems converting params in table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}

		try {
			$this->convertParamsToJsonField('emailresultsettings',
				array('emailresultincfile' => "0"),
				array('emailresultincfield' => '1', 'emailresultinccreated' => '1', 'emailresultincformtitle' => '1', 'emailresultincip' => '1')
			);
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems converting params in table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
		try {
			$this->dropColumns(array('emailresultincfile'));
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems with dropping fields from table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
	}

	private function postFlightForVersion3_5_3() {
		JLog::add('*** Perform postflight for Version 3.5.3 ***', JLog::INFO);
		try {
			$this->addColumns(array('frontaccess' => array('name' => 'frontaccess', 'type' => 'INT', 'length' => '11', 'notNull' => true, 'default' => 0)), 'visfields');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
		JLog::add("Set value in new field frontaccess", JLog::INFO);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__visfields'))
			->set($db->quoteName('frontaccess') . " = " . 1);
		$db->setQuery($query);
		try {
			$db->execute();
			JLog::add("Value in new field frontaccess set to 1", JLog::INFO);
		} catch (RuntimeException $e) {
			JLog::add("Unable to set value in new field frontaccess", JLog::ERROR);
		}
	}

	private function postFlightForVersion3_6_0() {
		JLog::add('*** Perform postflight for Version 3.6.0 ***', JLog::INFO);
		$this->updateDataTable3_6_0();
		$filesToDelete = array(
			'/components/com_visforms/views/visforms/tmpl/message.php',
			'/components/com_visforms/views/visforms/tmpl/message.xml'
		);
		$this->deleteOldFiles($filesToDelete);
	}

	private function updateDataTable3_6_0() {
		JLog::add("*** Try to update data tables ***", JLog::INFO);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName(array('id', 'saveresult')))
			->from($db->quoteName('#__visforms'));
		$db->setQuery($query);
		try {
			$forms = $db->loadAssocList();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($forms) > 0) {
			JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
			foreach ($forms as $form) {
				try {
					$this->addColumns(array('created_by' => array('name' => 'created_by', 'type' => 'INT', 'length' => '11', 'notNull' => true, 'default' => '0')
					),
						'visforms_' . $form['id']);
				} catch (RuntimeException $e) {
					$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
					$this->status->messages[] = array('message' => $message);
					JLog::add("Problems adding fields to table: #__visforms, " . $form['id'] . " " . $e->getMessage(), JLog::ERROR);
				}
				try {
					$this->addColumns(array('created_by' => array('name' => 'created_by', 'type' => 'INT', 'length' => '11', 'notNull' => true, 'default' => '0')
					),
						'visforms_' . $form['id'] . '_save');
				} catch (RuntimeException $e) {
					$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
					$this->status->messages[] = array('message' => $message);
					JLog::add("Problems adding fields to table: #__visforms, " . $form['id'] . "_save " . $e->getMessage(), JLog::ERROR);
				}
			}
		}
	}

	private function getPlgvscParmas($plgParamsForm = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('params'))
			->from('#__extensions')
			->where($db->quoteName('name') . " = " . $db->quote("plg_visforms_spambotcheck") . " AND " . $db->quoteName('folder') . " = " . $db->quote("visforms"));
		$db->setQuery($query);
		try {
			$params = json_decode($db->loadResult(), true);
		} catch (RuntimeException $e) {
			JLog::add("Cannot retrieve Plugin params, " . $e->getMessage(), JLog::ERROR);

			return false;
		}
		if (!isset($params) || !is_array($params) || !(count($params) > 0)) {
			JLog::add("Cannot retrieve Plugin params", JLog::ERROR);

			return false;
		}
		if ($params['spbot_projecthoneypot_api_key'] != "") {
			$plgParamsForm['spbot_projecthoneypot'] = "1";
		}

		return $newPlgParamsForm = array_merge($plgParamsForm, $params);
	}

	private function addColumns($columnsToAdd = array(), $table = "visforms") {
		if (count($columnsToAdd) > 0) {
			JLog::add("*** Try to add new fields to table: #__" . $table . " ***", JLog::INFO);
			JLog::add(count($columnsToAdd) . " fields to add", JLog::INFO);
			$db = JFactory::getDbo();
			foreach ($columnsToAdd as $columnToAdd) {
				//we need at least a column name
				if (!(isset($columnToAdd['name'])) || ($columnToAdd['name'] == "")) {
					continue;
				}
				$queryStr = $db->getQuery(true);
				$queryStr = ("ALTER TABLE " . $db->quoteName('#__' . $table) . "ADD COLUMN " . $db->quoteName($columnToAdd['name']) .
					((isset($columnToAdd['type']) && ($columnToAdd['type'] != "")) ? " " . $columnToAdd['type'] : " text") .
					((isset($columnToAdd['length']) && ($columnToAdd['length'] != "")) ? "(" . $columnToAdd['length'] . ")" : "") .
					((isset($columnToAdd['attribute']) && ($columnToAdd['attribute'] != "")) ? " " . $columnToAdd['attribute'] : "") .
					((isset($columnToAdd['notNull']) && ($columnToAdd['notNull'] == true)) ? " not NULL" : "") .
					((isset($columnToAdd['default']) && ($columnToAdd['default'] !== "")) ? " DEFAULT " . $db->quote($columnToAdd['default']) : " DEFAULT ''"));
				$db->setQuery($queryStr);
				try {
					$db->execute();
					JLog::add("Field added: " . $columnToAdd['name'], JLog::INFO);
				} catch (RuntimeException $e) {
					JLog::add("Unable to add field: " . $columnToAdd['name'] . ', ' . $e->getMessage(), JLog::ERROR);
				}
			}
		}
	}

	private function dropColumns($columnsToDrop = array(), $table = "visforms") {
		JLog::add("*** Try to drop fields from table #__" . $table . " ***", JLog::INFO);
		if (count($columnsToDrop) > 0) {
			JLog::add(count($columnsToDrop) . " fields to drop", JLog::INFO);
			$db = JFactory::getDbo();
			foreach ($columnsToDrop as $columnToDrop) {
				$queryStr = ("ALTER TABLE " . $db->quoteName('#__' . $table) . "DROP COLUMN " . $db->quoteName($columnToDrop));
				$db->setQuery($queryStr);
				try {
					$db->execute();
					JLog::add("Field successfully dropped: " . $columnToDrop, JLog::INFO);
				} catch (RuntimeException $e) {
					JLog::add("Problems dropping field: " . $columnToDrop . ', ' . $e->getMessage(), JLog::ERROR);
				}
			}
		} else {
			JLog::add('No fields to drop', JLog::INFO);
		}
	}

	/**
	 *
	 * @param string $paramFieldName Name of database field that contains the params (as JSON Object)
	 * @param array $oldFields array of database field names and default values that should be converted into the new param database field
	 * @param array $newFields array of field names and defaultvalues of fields that should be newly created inte the new param database field
	 * @param string $table table name
	 */
	private function convertParamsToJsonField($paramFieldName, $oldFields = array(), $newFields = array(), $table = 'visforms') {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		JLog::add("*** Try to convert params in table: #__" . $table . "***", JLog::INFO);
		if (count($oldFields) > 0) {
			$fields = array_merge(array('id'), array_keys($oldFields));
		} else {
			$fields = array('id');
		}
		$query
			->select($db->quoteName($fields))
			->from($db->quoteName('#__' . $table));
		$db->setQuery($query);
		try {
			$forms = $db->loadObjectList();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($forms) > 0) {

			JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
			foreach ($forms as $form) {
				$paramArray = array();
				if (count($oldFields) > 0) {
					foreach ($oldFields as $oldFieldName => $oldFieldDefault) {
						if (isset($form->$oldFieldName)) {
							$paramArray[$oldFieldName] = $form->$oldFieldName;
						} else {
							$paramArray[$oldFieldName] = $oldFieldDefault;
						}
					}
				}
				if (count($newFields) > 0) {
					foreach ($newFields as $newFieldName => $newFieldDefault) {
						$paramArray[$newFieldName] = $newFieldDefault;
					}
				}
				if (is_array($paramArray)) {
					$registry = new JRegistry;
					$registry->loadArray($paramArray);
					$paramArray = (string)$registry;
					$query = $db->getQuery(true);
					$query->update($db->quoteName('#__' . $table))
						->set($db->quoteName($paramFieldName) . " = " . $db->quote($paramArray))
						->where($db->quoteName('id') . " = " . $db->quote($form->id));
					$db->setQuery($query);
					try {
						$db->execute();
						JLog::add("Modified params saved in form with id: " . $form->id, JLog::INFO);
					} catch (RuntimeException $e) {
						$this->status->fixTableVisforms[] = array('form' => $form->id, 'result' => false, 'resulttext' => JText::_('COM_VISFORMS_PARAMS_LOST'));
						JLog::add("Unable to save modified params in form with id: " . $form->id . ', ' . $e->getMessage(), JLog::ERROR);
					}
				} else {
					JLog::add('Params have invalid type. Cannot update form with id: ' . $form->id, JLog::ERROR);
				}
			}
		} else {
			JLog::add('No form recordsets to process', JLog::INFO);
		}
	}

	private function createDataTableSave3_2_0() {
		JLog::add("*** Try to create _save tables ***", JLog::INFO);
		//get all form records from database
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName(array('id', 'saveresult')))
			->from($db->quoteName('#__visforms'));
		$db->setQuery($query);
		try {
			$forms = $db->loadAssocList();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($forms) > 0) {
			JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
			foreach ($forms as $form) {
				//create __save datatable if saveresult is true and it doesn't exists
				try {
					$tn = "#__visforms_" . $form['id'] . "_save";
					$dataTableName = $db->getPrefix() . 'visforms_' . $form['id'];
					$tableList = $db->getTableList();

					// Create the _save data table if data table exists
					if (in_array($dataTableName, $tableList)) {
						// Create _save table
						$query = "create table " . $tn .
							" (id int(11) not null AUTO_INCREMENT," .
							"published tinyint, " .
							"created datetime, " .
							"checked_out int(10) NOT NULL default '0', " .
							"checked_out_time datetime NOT NULL default '0000-00-00 00:00:00', " .
							"ipaddress TEXT NULL, " .
							"articleid TEXT NULL, ";
						$query .= "mfd_id int(11) NOT NULL default 0, ";
						$query .= "primary key (id) " .
							") ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";

						$db->SetQuery($query);
						$db->execute();

						// Add existing Fields
						$query = ' SELECT * from #__visfields c where c.fid=' . $form['id'] . ' ';
						$db->setQuery($query);
						$fields = $db->loadObjectList();

						$tableFields = $db->getTableColumns($tn, false);
						$n = count($fields);
						for ($i = 0; $i < $n; $i++) {
							$rowField = $fields[$i];
							$fieldname = "F" . $rowField->id;

							if (!isset($tableFields[$fieldname])) {
								$query = "ALTER TABLE " . $tn . " ADD " . $fieldname . " TEXT NULL";
								$db->SetQuery($query);
								$db->execute();
							}
						}
						$this->status->fixTableVisforms[] = array('form' => $form['id'], 'result' => true, 'resulttext' => JText::_('COM_VISFORMS_CREATION_OF_DATATABLE_SAVE_SUCCESSFUL'));
						JLog::add("_save table successfully create for form with id: " . $form['id'], JLog::INFO);
					}
				} catch (RuntimeException $e) {
					$this->status->fixTableVisforms[] = array('form' => $form['id'], 'result' => false, 'resulttext' => JText::_('COM_VISFORMS_CREATION_OF_DATATABLE_SAVE_FAILED') . ': '. $e->getMessage());
					JLog::add("Unable to create _save table for form with id: " . $form['id'] . ', ' . $e->getMessage(), JLog::ERROR);
				}
			}
		}
	}

	private function updateDataTable3_2_0() {
		JLog::add("*** Try to update data tables ***", JLog::INFO);

		//get all form records from database
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName(array('id', 'saveresult')))
			->from($db->quoteName('#__visforms'));
		$db->setQuery($query);
		try {
			$forms = $db->loadAssocList();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($forms) > 0) {
			JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
			foreach ($forms as $form) {
				try {
					$this->addColumns(array('ismfd' => array('name' => 'ismfd', 'type' => 'TINYINT', 'length' => '4', 'notNull' => true, 'default' => '0'),
						array('name' => 'checked_out', 'type' => 'int', 'length' => '10', 'notNull' => true, 'default' => '0'),
						array('name' => 'checked_out_time', 'type' => 'datetime', 'notNull' => true, 'default' => '0000-00-00 00:00:00')
					),
						'visforms_' . $form['id']);
				} catch (RuntimeException $e) {
					$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
					$this->status->messages[] = array('message' => $message);
					JLog::add("Problems adding fields to table: #__visforms, " . $form['id'] . " " . $e->getMessage(), JLog::ERROR);
				}
			}
		}
	}

	// Convert option list of radio buttons and selects from former custom format string to json in table visfields
	private function convertSelectRadioOptionList() {
		JLog::add("*** Try to convert option list string of radio buttons and selects to json in table: #__visfields ***", JLog::INFO);
		//get all field records from database
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName(array('id', 'typefield', 'defaultvalue')))
			->from($db->quoteName('#__visfields'))
			->where($db->quoteName('typefield') . " IN (" . $db->quote('select') . ", " . $db->quote('radio') . ")");
		$db->setQuery($query);
		try {
			$fields = $db->loadObjectList();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get fields: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($fields) > 0) {
			JLog::add(count($fields) . " field recordsets to process", JLog::INFO);
			foreach ($fields as $field) {
				//convert defaultvalue to array
				$registry = new JRegistry;
				$registry->loadString($field->defaultvalue);
				$field->defaultvalue = $registry->toArray();
				$optionFieldName = "f_" . $field->typefield . "_list_hidden";
				//get old option string
				$oldOptions = $field->defaultvalue[$optionFieldName];
				JLog::add("Old option list value in field with id: " . $field->id . " is " . $oldOptions, JLog::INFO);
				$newOptsString = '';
				//extract old options
				if ($oldOptions != "") {
					//index of newOptions has to start with 1 not with 0
					$i = 1;
					$newOptsString .= '{';
					$options = explode("[-]", $oldOptions);
					foreach ($options as $option) {
						$val = explode("==", $option);
						$key = explode("||", $val[1]);
						$ipos = strpos($key[1], ' [default]');
						//remove the [default]
						if ($ipos != false) {
							$key[1] = substr($key[1], 0, $ipos);
							$ipos = "1";
						}

						$newOptsString .= '"' . $i . '":{"listitemid":' . $i . ',"listitemvalue":"' . $key[0] . '","listitemlabel":"' . $key[1] . '"';

						//add listitemischecked if the option is set as default
						if ($ipos == "1") {
							$newOptsString .= ',"listitemischecked":"' . $ipos . '"';
						}
						$newOptsString .= "},";
						$i++;
					}
					$newOptsString = rtrim($newOptsString, ",") . '}';
				}
				if ($newOptsString != "") {
					JLog::add("New option list value in field with id: " . $field->id . " is " . $newOptsString, JLog::INFO);
					$field->defaultvalue[$optionFieldName] = $newOptsString;
					$registry = new JRegistry();
					$registry->loadArray($field->defaultvalue);
					$newDefaultvalue = (string)$registry;
					$query = $db->getQuery(true);
					$query->update($db->quoteName('#__visfields'))
						->set($db->quoteName('defaultvalue') . " = " . $db->quote($newDefaultvalue))
						->where($db->quoteName('id') . " = " . $db->quote($field->id));
					$db->setQuery($query);
					try {
						$db->execute();
						JLog::add("Modified option list saved in field with id: " . $field->id, JLog::INFO);
					} catch (RuntimeException $e) {
						JLog::add("Unable to save modified option list in field with id: " . $field->id . ', ' . $e->getMessage(), JLog::ERROR);
					}
				}
			}
		}
	}

	private function enableExtension($extWhere) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . " = 1")
			->where($extWhere);
		$db->setQuery($query);
		try {
			$db->execute();
			JLog::add("Extension successfully enabled", JLog::INFO);
		} catch (RuntimeException $e) {
			JLog::add("Unable to enable extension " . $e->getMessage(), JLog::ERROR);
		}
	}

	private function postFlightForVersion3_6_3() {
		JLog::add('*** Perform postflight for Version 3.6.3 ***', JLog::INFO);
		// Content plugin visforms was replaced with the visforms plugin visforms
		JLog::add('Try to uninstall content plugin visforms', JLog::INFO);
		$name = (string)'visforms';
		$group = (string)'content';
		$db = JFactory::getDbo();
		$plgWhere = $db->quoteName('type') . ' = ' . $db->quote('plugin') . ' AND ' . $db->quoteName('element') . ' = ' . $db->quote($name) . ' AND ' . $db->quoteName('folder') . ' = ' . $db->quote($group);
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($plgWhere);
		$db->setQuery($query);
		try {
			$extensions = $db->loadColumn();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get extension_id: ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($extensions)) {
			foreach ($extensions as $id) {
				$installer = new JInstaller;
				try {
					$result = $installer->uninstall('plugin', $id);
					$this->status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
					if ($result) {
						JLog::add('Plugin sucessfully removed: ' . $name, JLog::INFO);
					} else {
						JLog::add('Removal of plugin failed: ' . $name, JLog::ERROR);
					}
				} catch (RuntimeException $e) {
					JLog::add('Removal of plugin failed: ' . $name . ', ' . $e->getMessage(), JLog::ERROR);
				}
			}
		}
	}

	private function postFlightForVersion3_6_5() {
		JLog::add('*** Perform postflight for Version 3.6.5 ***', JLog::INFO);
		$filesToDelete = array(
			'/administrator/components/com_visforms/images/icon-16-visforms.png',
			'/adminstrator/components/com_visforms/views/vistools/tmpl/css.php',
			'/administrator/components/com_visforms/models/fields/aeffrontenddataedit.php'
		);
		JLog::add(count($filesToDelete) . " files to delete", JLog::INFO);
		try {
			$this->deleteOldFiles($filesToDelete);
		} catch (Exception $e) {
			JLog::add('Problems deleting old files: ' . $e->getMessage(), JLog::WARNING);
		}
		try {
			$this->setParams(array('includeheadline' => '1'), 'visforms', 'exportsettings');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Unable to set params in table #__visforms, " . $e->getMessage(), JLog::WARNING);
		}
	}

	private function postFlightForVersion3_7_0() {
		JLog::add('*** Perform postflight for Version 3.7.0 ***', JLog::INFO);
		$this->fixSepInStoredUserInputsFromMultiSelect();
		$this->convertTableEngine();
	}

	private function postFlightForVersion3_7_1() {
		JLog::add('*** Perform postflight for Version 3.7.1 ***', JLog::INFO);
		JLog::add('*** Try to delete old files no longer used ***', JLog::INFO);
		$filesToDelete = array(
			'/administrator/components/com_visforms/models/fields/spaceraefhidden.php'
		);
		JLog::add(count($filesToDelete) . " files to delete", JLog::INFO);
		try {
			$this->deleteOldFiles($filesToDelete);
		} catch (Exception $e) {
			JLog::add('Problems deleting old files: ' . $e->getMessage(), JLog::WARNING);
		}
		try {
			$this->addColumns(array('editonlyfield' => array('name' => 'editonlyfield', 'type' => 'TINYINT', 'length' => '1', 'notNull' => true, 'default' => 0),
				'addtoredirecturl' => array('name' => 'addtoredirecturl', 'type' => 'TINYINT', 'length' => '1', 'notNull' => true, 'default' => 0),
				'includeinresultmail' => array('name' => 'includeinresultmail', 'type' => 'TINYINT', 'length' => '1', 'notNull' => true, 'default' => 1),
				'includeinreceiptmail' => array('name' => 'includeinreceiptmail', 'type' => 'TINYINT', 'length' => '1', 'notNull' => true, 'default' => 1)
			),
				'visfields');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visfields, " . $e->getMessage(), JLog::ERROR);
		}
	}

	private function postFlightForVersion3_8_8() {
		JLog::add('*** Perform postflight for Version 3.8.8 ***', JLog::INFO);
		try {
			$this->addColumns(array('redirecttoeditview' => array('name' => 'redirecttoeditview', 'type' => 'TINYINT', 'length' => '1', 'notNull' => true, 'default' => 0)
			));
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
		$this->runQuery("ALTER TABLE `#__visfields` MODIFY `defaultvalue` LONGTEXT");
		$this->runQuery("ALTER TABLE `#__visforms` MODIFY `layoutsettings` LONGTEXT");

	}

	private function fixSepInStoredUserInputsFromMultiSelect() {
		JLog::add('Try to change separator in stored user inputs in fields of type select and multicheckbox with mulitselect', JLog::INFO);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn(array('fid', 'id', 'typefield', 'defaultvalue')))
			->from($db->qn('#__visfields'))
			->where($db->qn('typefield') . ' = ' . $db->q('select'), 'OR')
			->where($db->qn('typefield') . ' = ' . $db->q('multicheckbox'));
		$db->setQuery($query);
		try {
			$fields = $db->loadObjectList();
		} catch (RuntimeException $e) {
			JLog::add("Unable to get fields definition: " . $e->getMessage(), JLog::ERROR);

			return false;
		}
		if (empty($fields)) {
			JLog::add("No fields to process", JLog::INFO);

			return true;
		}
		$count = count($fields);
		JLog::add($count . " fields to process", JLog::INFO);
		$tablelist = $db->getTableList();
		if (!empty($tablelist)) {
			$tablelist = array_map('strtolower', $tablelist);
		}
		foreach ($fields as $field) {
			$test = strtolower($db->getPrefix() . 'visforms_' . $field->fid);
			//only try to process fields if data table exists.
			if ((empty($tablelist)) || (!in_array($test, $tablelist))) {
				JLog::add("User inputs of form with id " . $field->fid . " are not stored in database. Nothing to do.", JLog::INFO);
				continue;
			}
			if (!empty($field->defaultvalue)) {
				$registry = new JRegistry;
				$registry->loadString($field->defaultvalue);
				$field->defaultvalue = $registry->toArray();
				$key = 'f_' . $field->typefield;
				//only process fields which actually have a multi select option enabled
				//if an admins has changed this options results of conversion may be incorrect
				switch ($field->typefield) {
					case 'select':
						if (empty($field->defaultvalue[$key . '_attribute_multiple'])) {
							//skip this field
							JLog::add("Option multi select is not enabled in field with id " . $field->id . " Nothing to do.", JLog::INFO);
							continue 2;
						}
						break;
					case 'multicheckbox':
						if ((empty($field->defaultvalue[$key . '_attribute_maxlength'])) || ($field->defaultvalue[$key . '_attribute_maxlength'] < 2)) {
							//skip this field
							JLog::add("Multi selection is not enabled multi checkbox field with id " . $field->id . " Nothing to do.", JLog::INFO);
							continue 2;
						}
						break;
					default:
						//should never happen but skip this field
						continue 2;
				}
				//extract array of allowed options from field definition
				$options = json_decode($field->defaultvalue[$key . '_list_hidden']);
				$returnopts = array();
				$hasOptionWithComma = false;
				if ((!empty($options)) && (is_object($options))) {
					foreach ($options as $option) {
						if ((!empty($option)) && (!empty($option->listitemvalue)) && (substr_count($option->listitemvalue, ','))) {
							$hasOptionWithComma = true;
						}
						$returnopts[] = $option->listitemvalue;
					}
				}
				//get stored user inputs from data table
				$datatablefieldkey = 'f' . $field->id;
				$query = $db->getQuery(true);
				$query->select($db->qn(array('id', $datatablefieldkey)))
					->from($db->qn('#__visforms_' . $field->fid));
				$db->setQuery($query);
				try {
					$storedValues = $db->loadObjectList();
				} catch (RuntimeException $e) {
					JLog::add("Unable to get stored user input for field with id " . $field->id . ":" . $e->getMessage(), JLog::ERROR);
				}
				//no user inputs stored
				if (!empty($storedValues)) {
					foreach ($storedValues as $storedValue) {
						if (empty($storedValue->$datatablefieldkey)) {
							JLog::add("User inputs for recordset " . $storedValue->id . " for field with id " . $field->id . " are empty. Nothing to do.", JLog::INFO);
							continue;
						}
						$fixedStoreValue = $this->replaceSeparator($storedValue->$datatablefieldkey, $returnopts, $storedValue->id, $field->id, $hasOptionWithComma);
						$fixedData = new stdClass();
						$fixedData->id = $storedValue->id;
						$fixedData->$datatablefieldkey = $fixedStoreValue;
						try {
							$db->updateObject('#__visforms_' . $field->fid, $fixedData, 'id');
							JLog::add("Fixed user inputs for recordset " . $storedValue->id . " for field with id " . $field->id . " stored. Original value was: " . $storedValue->$datatablefieldkey . " fixed value is: " . $fixedStoreValue, JLog::INFO);
						} catch (RuntimeException $e) {
							JLog::add("Unable to store fixed value for recordset " . $storedValue->id . " for field with id " . $field->id . " in database. Original value was: " . $storedValue->$datatablefieldkey . " fixed value is: " . $fixedStoreValue . ": " . $e->getMessage(), JLog::ERROR);
						}
					}
				}
				unset($storedValue);
				unset($storedValues);
				unset($fixedData);
				unset($fixedStoreValue);
				//fix data stored in "save" table
				if ((empty($tablelist)) || (!in_array(strtolower($db->getPrefix() . 'visforms_' . $field->fid . '_save'), $tablelist))) {
					continue;
				}
				$query = $db->getQuery(true);
				$query->select($db->qn(array('id', $datatablefieldkey)))
					->from($db->qn('#__visforms_' . $field->fid . '_save'));
				$db->setQuery($query);
				try {
					$storedValues = $db->loadObjectList();
				} catch (RuntimeException $e) {
					JLog::add("Unable to get stored user input for field with id " . $field->id . " from save table:" . $e->getMessage(), JLog::ERROR);
				}
				if (!empty($storedValues)) {
					foreach ($storedValues as $storedValue) {
						if (empty($storedValue->$datatablefieldkey)) {
							JLog::add("User inputs for recordset " . $storedValue->id . " for field with id " . $field->id . " in save table are empty. Nothing to do.", JLog::INFO);
							continue;
						}
						$fixedStoreValue = $this->replaceSeparator($storedValue->$datatablefieldkey, $returnopts, $storedValue->id, $field->id, $hasOptionWithComma);
						$fixedData = new stdClass();
						$fixedData->id = $storedValue->id;
						$fixedData->$datatablefieldkey = $fixedStoreValue;
						try {
							$db->updateObject('#__visforms_' . $field->fid . '_save', $fixedData, 'id');
							JLog::add("Fixed user inputs for recordset " . $storedValue->id . " for field with id " . $field->id . " stored in save table. Original value was: " . $storedValue->$datatablefieldkey . " fixed value is: " . $fixedStoreValue, JLog::INFO);
						} catch (RuntimeException $e) {
							JLog::add("Unable to store fixed value in save database. Original value was: " . $storedValue->$datatablefieldkey . " fixed value is: " . $fixedStoreValue . ": " . $e->getMessage(), JLog::ERROR);
						}
					}
				}
				unset($storedValue);
				unset($storedValues);
				unset($datatablefieldkey);
				unset($returnopts);
				unset($fixedData);
				unset($fixedStoreValue);
				unset($options);
				unset($key);
				unset($hasOptionWithComma);
			}
			unset($field);
		}
	}

	private function replaceSeparator($storedValue, $validOptions, $fieldid, $recordid, $hasOptionWithComma = false) {
		if (empty($hasOptionWithComma)) {
			$tmp = explode(",", $storedValue);
			foreach ($tmp as $index => $word) {
				$tmp[$index] = (string) trim($word);
			}
		} else {
			//start with the longest option value
			usort($validOptions, function ($a, $b) {
				if (strlen($a) == strlen($b)) {
					return 0;
				}

				return (strlen($a) > strlen($b)) ? -1 : 1;
			});
			//array with used valid options
			$tmp = array();
			foreach ($validOptions as $validOption) {
				//check if this value is part of the stored string, add it to the new array and remove it from stored string
				if ((!empty($storedValue)) && (strpos($storedValue . ',', $validOption . ',') !== false)) {
					$tmp[] = $validOption;
					$storedValue = str_replace($validOption . ',', '', $storedValue . ',');
				}
			}
			//stored user input contains parts which are no valid option, add these to fixed stored Value
			if (!empty($storedValue)) {
				$trimmed = rtrim($storedValue, ',');
				if (!empty($trimmed)) {
				    $trimmed = (string) trim($trimmed);
				}
				if (!empty($trimmed)) {
					$tmp[] = $trimmed;
				}
			}
			$addLogEntry = true;
		}
		$fixedStoreValue = implode("\0, ", $tmp);
		if (!empty($addLogEntry)) {
			JLog::add("Potential problem: Selected vaules in recordset " . $recordid . " for field with id " . $fieldid . " contains options with comma. Converting options with commas can cause invalid data. Old value:  " . $storedValue . ". Stored new values:" . $fixedStoreValue, JLog::INFO);
		}

		return $fixedStoreValue;
	}

	private function convertTableEngine() {
		JLog::add('Try to change storage engine', JLog::INFO);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$tablelist = $db->getTableList();
		foreach ($tablelist as $table) {
			if ((strpos($table, '_visforms') !== false) || (strpos($table, '_visfields') !== false)) {
				$query = 'ALTER TABLE ' . $table . ' ENGINE=InnoDB';
				try {
					$db->setQuery($query);
					$db->execute();

				} catch (Exception $e) {
					JLog::add("Unable to change storage engine to InnoDB for " . $table . ": " . $e->getMessage(), JLog::ERROR);
				}
			}
		}
	}

	private function runQuery($sql) {
		JLog::add('Try to run sql query', JLog::INFO);
		$db = JFactory::getDbo();
		$query = $sql;
		try {
			$db->setQuery($query);
			$result = $db->execute();

		} catch (Exception $e) {
			JLog::add("Unable to sql query: " . $e->getMessage(), JLog::ERROR);
		}
	}

	function cmp($a, $b) {
		if (strlen($a) == strlen($b)) {
			return 0;
		}

		return (strlen($a) > strlen($b)) ? 1 : -1;
	}

	private function postFlightForVersion3_8_10() {
		JLog::add('*** Try to delete old files no longer used ***', JLog::INFO);
		$filesToDelete = array(
			'/administrator/components/com_visforms/models/fields/btsize.php'
		);
		JLog::add(count($filesToDelete) . " files to delete", JLog::INFO);
		try {
			$this->deleteOldFiles($filesToDelete);
		} catch (Exception $e) {
			JLog::add('Problems deleting old files: ' . $e->getMessage(), JLog::WARNING);
		}
	}

	private function postFlightForVersion3_8_12() {
		JLog::add('*** Try to delete old files no longer used ***', JLog::INFO);
		$filesToDelete = array(
			'/administrator/components/com_visforms/controllers/vishelp.php',
			'/administrator/components/com_visforms/models/vishelp.php',
			'/administrator/components/com_visforms/models/fields/donate.php'

		);

		$foldersToDelete = array(
			'/administrator/components/com_visforms/views/vishelp'
		);
		JLog::add(count($filesToDelete) . " files to delete", JLog::INFO);
		try {
			$this->deleteOldFiles($filesToDelete, $foldersToDelete);
		} catch (Exception $e) {
			JLog::add('Problems deleting old files: ' . $e->getMessage(), JLog::WARNING);
		}
	}

	private function postFlightForVersion3_8_17() {
		JLog::add('*** Perform postflight for Version 3.8.17 ***', JLog::INFO);
		try {
			$this->addColumns(array('editemailresultsettings' => array('name' => 'editemailresultsettings', 'type' => 'longtext'),
				'editemailreceiptsettings' => array('name' => 'editemailreceiptsettings', 'type' => 'longtext')
			));
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
		$this->convertEditMailOptions();
	}

	private function postFlightForVersion3_8_19() {
		JLog::add('*** Perform postflight for Version 3.8.19 ***', JLog::INFO);
		try {
			$this->addColumns(array('savemode' => array('name' => 'savemode', 'type' => 'tinyint', 'length' => '1', 'notNull' => true, 'default' => 0)
			));
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
	}

	private function postFlightForVersion3_8_20() {
		JLog::add('*** Perform postflight for Version 3.8.20 ***', JLog::INFO);
		try {
			$this->addColumns(array('rdtparamname' => array('name' => 'rdtparamname', 'type' => 'text', 'notNull' => true, 'default' => '')
			), 'visfields');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visfields, " . $e->getMessage(), JLog::ERROR);
		}
	}

	private function postFlightForVersion3_9_1() {
		JLog::add('*** Perform postflight for Version 3.9.1 ***', JLog::INFO);
		$filesToDelete = array(
			'/administrator/components/com_visforms/js/jquery-ui.js',
			'/administrator/components/com_visforms/js/jquery-ui.min.js'
		);
		try {
			$this->deleteOldFiles($filesToDelete);
		} catch (Exception $e) {
			JLog::add('Problems deleting old files: ' . $e->getMessage(), JLog::WARNING);
		}
	}


	private function postFlightForVersion3_10_0() {
		JLog::add('*** Perform postflight for Version 3.10.0 ***', JLog::INFO);
		$filesToDelete = array(
			'/administrator/components/com_visforms/css/visforms_min.css'
		);
		try {
			$this->deleteOldFiles($filesToDelete);
		} catch (Exception $e) {
			JLog::add('Problems deleting old files: ' . $e->getMessage(), JLog::WARNING);
		}
		try {
			$this->addColumns(array(
					'useoptionvalueinplaceholder' => array('name' => 'useoptionvalueinplaceholder', 'type' => 'tinyint', 'length' => '1', 'notNull' => true, 'default' => 0),
					'customlabelformail' => array('name' => 'customlabelformail', 'type' => 'text'),
					'customlabelforcsv' => array('name' => 'customlabelforcsv', 'type' => 'text'),
					'fileexportformat' => array('name' => 'fileexportformat', 'type' => 'tinyint', 'length' => '1', 'notNull' => true, 'default' => 0),
                    'displayAsMapInList' => array('name' => 'displayAsMapInList', 'type' => 'tinyint', 'length' => '1', 'notNull' => true, 'default' => 0),
					'displayAsMapInDetail' => array('name' => 'displayAsMapInDetail', 'type' => 'tinyint', 'length' => '1', 'notNull' => true, 'default' => 0),
                    'listMapHeight' =>array ('name' => 'listMapHeight', 'type' => 'char', 'length' => '10', 'notNull' => true, 'default' => ''),
					'detailMapHeight' =>array ('name' => 'detailMapHeight', 'type' => 'char', 'length' => '10', 'notNull' => true, 'default' => ''),
					'listMapZoom' => array('name' => 'listMapZoom', 'type' => 'int', 'notNull' => true, 'default' => 8),
					'detailMapZoom' => array('name' => 'detailMapZoom', 'type' => 'int', 'notNull' => true, 'default' => 13)
				)
				, 'visfields');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visfields, " . $e->getMessage(), JLog::ERROR);
		}
		try {
			$this->addColumns(array('subredirectsettings' => array('name' => 'subredirectsettings', 'type' => 'text')));
        } catch (RuntimeException $e) {
			$message                  = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
		JLog::add("*** Try to update data tables ***", JLog::INFO);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('id'))
			->from($db->quoteName('#__visforms'));
		$db->setQuery($query);
		try {
			$forms = $db->loadColumn();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($forms) > 0) {
			JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
			foreach ($forms as $form) {
				try {
					$this->addColumns(array('modified' => array('name' => 'modified', 'type' => 'datetime', 'notNull' => true, 'default' => '0000-00-00 00:00:00'),
						array('name' => 'modified_by', 'type' => 'int', 'length' => '11', 'notNull' => true, 'default' => 0)
					),
						'visforms_' . $form);
				} catch (RuntimeException $e) {
					$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
					$this->status->messages[] = array('message' => $message);
					JLog::add("Problems adding fields to table: #__visforms, " . $form . " " . $e->getMessage(), JLog::ERROR);
				}
			}
		}
	}
	private function postFlightForVersion3_10_1() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName(array('id')))
			->from($db->quoteName('#__visforms'));
		$db->setQuery($query);
		try {
			$forms = $db->loadAssocList();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get forms: ' . $e->getMessage(), JLog::ERROR);
		}
		if (count($forms) === 0) {
			JLog::add("No recordsets to process", JLog::INFO);
		    return true;
        }
        JLog::add(count($forms) . " form recordsets to process", JLog::INFO);
        try {
            $tableList = $db->getTableList();
        } catch (RuntimeException $e) {
            JLog::add('Unable to get tablelist: ' . $e->getMessage(), JLog::ERROR);
        }
        if (empty($tableList))
        {
            return;
        }
		$tableList = array_map('strtolower', $tableList);
			foreach ($forms as $form) {
				//create __save datatable if saveresult is true and it doesn't exists
				try {
					$tn = "#__visforms_" . $form['id'];
					$dataTableName = strtolower( $db->getPrefix() . 'visforms_' . $form['id']);
					if (in_array($dataTableName, $tableList)) {
						//get column list
						$columns = $db->getTableColumns($tn, false);
						$keys = array('id');
						if (isset($columns['created'])) {$keys[] = 'created';}
						if (count($keys) > 1) {
							//fix timezoneoffset in record sets
                            $query = $db->getQuery(true);
                            $query->select($db->quoteName($keys))
	                            ->from($db->quoteName($tn));
							$db->setQuery($query);
							$datas = $db->loadObjectList();
							if (!empty($datas)) {
							    foreach ($datas as $data) {
							        $changed = false;
                                   if (isset($data->created) && $data->created !== "0000-00-00 00:00:00" ) {
                                       $date = JFactory::getDate($data->created, JFactory::getConfig()->get('offset'));
                                       $date->setTimezone(new DateTimeZone('UTC'));
                                       $data->created = $date->toSql();
                                       $changed = true;
                                   }
								    if ($changed) {
									    $db->updateObject($tn, $data, 'id');
                                    }
                                }
                            }
						}
					}

					$tn = "#__visforms_" . $form['id'] . "_save";
					$dataTableName = strtolower( $db->getPrefix() . 'visforms_' . $form['id'] . "_save");
					if (in_array($dataTableName, $tableList)) {
						$columns = $db->getTableColumns($tn, false);
						$keys = array('id');
						if (isset($columns['created'])) {$keys[] = 'created';}
						if (count($keys) > 1) {
							//fix timezoneoffset in record sets
							$query = $db->getQuery(true);
							$query->select($db->quoteName($keys))
								->from($db->quoteName($tn));
							$db->setQuery($query);
							$datas = $db->loadObjectList();
							if (!empty($datas)) {
								foreach ($datas as $data) {
									$changed = false;
									if (isset($data->created) && $data->created !== "0000-00-00 00:00:00" ) {
										$date = JFactory::getDate($data->created, JFactory::getConfig()->get('offset'));
										$date->setTimezone(new DateTimeZone('UTC'));
										$data->created = $date->toSql();
										$changed = true;
									}
									if ($changed) {
										$db->updateObject($tn, $data, 'id');
									}
								}
							}
						}
                    }
				} catch (RuntimeException $e) {
					$this->status->fixTableVisforms[] = array('form' => $form['id'], 'result' => false, 'resulttext' => JText::_('COM_VISFORMS_CREATION_OF_DATATABLE_SAVE_FAILED') . ': '. $e->getMessage());
					JLog::add("Unable to fix dates for form with id: " . $form['id'] . ', ' . $e->getMessage(), JLog::ERROR);
				}
			}

    }

	private function postFlightForVersion3_10_2() {
		JLog::add('*** Perform postflight for Version 3.10.2 ***', JLog::INFO);
		try {
			$this->addColumns(array(
					'allowferadiussearch' => array('name' => 'allowferadiussearch', 'type' => 'tinyint', 'length' => '1', 'notNull' => true, 'default' => 0),
					'distanceunit' =>array ('name' => 'distanceunit', 'type' => 'char', 'length' => '10', 'notNull' => true, 'default' => 'km'),
					'useassearchfieldonly' => array('name' => 'useassearchfieldonly', 'type' => 'tinyint', 'length' => '1', 'notNull' => true, 'default' => 0)
				)
				, 'visfields');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visfields, " . $e->getMessage(), JLog::ERROR);
		}
		$this->deleteUpdateSiteLinks();
    }
	
	private function postFlightForVersion3_11_0() {
		JLog::add('*** Perform postflight for Version 3.11.0 ***', JLog::INFO);
		$filesToDelete = array(
			'/media/com_visforms/js/visforms.min.js'
		);
		try {
			$this->deleteOldFiles($filesToDelete);
		} catch (Exception $e) {
			JLog::add('Problems deleting old files: ' . $e->getMessage(), JLog::WARNING);
		}
	}

	private function postFlightForVersion3_11_1() {
		JLog::add('*** Perform postflight for Version 3.11.0 ***', JLog::INFO);
		try {
			$this->addColumns(array(
					'displayImgAsImgInList' => array('name' => 'displayImgAsImgInList', 'type' => 'tinyint', 'length' => '1', 'notNull' => true, 'default' => 0),
					'displayImgAsImgInDetail' => array('name' => 'displayImgAsImgInDetail', 'type' => 'tinyint', 'length' => '1', 'notNull' => true, 'default' => 0),
					'dataordering' => array('name' => 'dataordering', 'type' => 'int', 'length' => '11', 'notNull' => true, 'default' => 0)
				)
				, 'visfields');
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visfields, " . $e->getMessage(), JLog::ERROR);
		}
		$this->setDataOrderFieldValues();
    }
	
	private function postFlightForVersion3_11_2() {
		JLog::add('*** Perform postflight for Version 3.11.2 ***', JLog::INFO);
		try {
			$this->addColumns(array('savesettings' => array('name' => 'savesettings', 'type' => 'text')));
		} catch (RuntimeException $e) {
			$message = JText::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			JLog::add("Problems adding fields to table: #__visforms, " . $e->getMessage(), JLog::ERROR);
		}
	}

    private function setDataOrderFieldValues() {
	    JLog::add('*** Try to set values in field dataordering ***', JLog::INFO);
	    $db = JFactory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select($db->qn(array('id', 'ordering')))
		    ->from($db->qn('#__visfields'));
	    $db->setQuery($query);
	    try {
		    $orderings = $db->loadObjectList();
	    } catch (RuntimeException $e) {
		    JLog::add("Unable to get order: " . $e->getMessage(), JLog::ERROR);
	    }
	    if (empty($orderings)) {
	        return;
        }
        foreach ($orderings as $ordering) {
	        $data = new stdClass();
	        $data->id = $ordering->id;
	        $data->dataordering = $ordering->ordering;
	        try {
	           $db->updateObject('#__visfields', $data, 'id');
	           unset($data);
            } catch (RuntimeException $e) {
		        JLog::add("Unable to set dataordering: " . $e->getMessage(), JLog::ERROR);
            }
        }
    }

	private function convertEditMailOptions() {
		JLog::add('*** Try to convert edit mail options ***', JLog::INFO);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn(array(
			'id', 'emailreceipt', 'emailreceiptsubject', 'emailreceiptfrom', 'emailreceiptfromname', 'emailreceipttext', 'emailreceiptsettings',
			'emailresult', 'emailfrom', 'emailfromname', 'emailto', 'emailcc', 'emailbcc', 'subject', 'emailresulttext', 'emailresultsettings'
		)))
			->from($db->qn('#__visforms'));
		$db->setQuery($query);
		try {
			$forms = $db->loadObjectList();
		} catch (RuntimeException $e) {
			JLog::add("Unable to get stored options: " . $e->getMessage(), JLog::ERROR);
		}
		if (empty($forms)) {
			return;
		}
		foreach ($forms as $form) {
			$newform = new stdClass();
			$newform->id = $form->id;
			//Result Mail
			$registry = new JRegistry;
			$registry->loadString($form->emailresultsettings);
			$emailresultsettings = $registry->toArray();
			$editemailresultsettings = array(
				'editemailresult' => '0',
				'editemailfrom' => '',
				'editemailfromname' => '',
				'editemailto' => '',
				'editemailcc' => '',
				'editemailbcc' => '',
				'editsubject' => '',
				'editemailresulttext' => '',
				'editemailresultincfield' => '1',
				'editemailresulthideemptyfields' => '0',
				'editemailresultincdatarecordid' => '1',
				'editemailresultinccreated' => '1',
				'editemailresultincformtitle' => '1',
				'editemailresultincip' => '1',
				'editreceiptmailaslink' => '0',
				'editemailresultincfile' => '0',
				'editemailresultmodifiedonly' => '0'
			);
			if (!empty($emailresultsettings)) {
				if (isset($emailresultsettings['editemailresult'])) {
					$editemailresultsettings['editemailresult'] = $emailresultsettings['editemailresult'];
					//if edit mail is enabled copy values from emailreceiptsettings into new edit mail parameters else keep default settings
					if (!empty($editemailresultsettings['editemailresult'])) {
						$editemailresultsettings['editemailfrom'] = $form->emailfrom;
						$editemailresultsettings['editemailfromname'] = $form->emailfromname;
						$editemailresultsettings['editemailto'] = $form->emailto;
						$editemailresultsettings['editemailcc'] = $form->emailcc;
						$editemailresultsettings['editemailbcc'] = $form->emailbcc;
						$editemailresultsettings['editsubject'] = $form->subject;
						$editemailresultsettings['editemailresulttext'] = $form->emailresulttext;
						foreach ($emailresultsettings as $pname => $pvalue) {
							$key = 'edit' . $pname;
							if (array_key_exists($key, $editemailresultsettings)) {
								$editemailresultsettings[$key] = $pvalue;
							}
						}
					}
					unset($emailresultsettings['editemailresult']);
				}
				if (isset($emailresultsettings['editemailresultmodifiedonly'])) {
					$editemailresultsettings['editemailresultmodifiedonly'] = $emailresultsettings['editemailresultmodifiedonly'];
					unset($emailresultsettings['editemailresultmodifiedonly']);
				}
				$registry = new JRegistry;
				$registry->loadArray($emailresultsettings);
				$newform->emailresultsettings = (string)$registry;
			}
			$registry = new JRegistry;
			$registry->loadArray($editemailresultsettings);
			$newform->editemailresultsettings = (string)$registry;

			//Receipt Mail
			$registry = new JRegistry;
			$registry->loadString($form->emailreceiptsettings);
			$emailreceiptsettings = $registry->toArray();
			$editemailreceiptsettings = array(
				'editemailreceipt' => '0',
				'editemailreceiptsubject' => '',
				'editemailreceiptfrom' => '',
				'editemailreceiptfromname' => '',
				'editemailreceipttext' => '',
				'editemailreceiptincfield' => '0',
				'editemailreceipthideemptyfields' => '0',
				'editemailreceiptincdatarecordid' => '1',
				'editemailrecipientincfilepath' => '0',
				'editemailreceiptinccreated' => '1',
				'editemailreceiptincformtitle' => '1',
				'editemailreceiptincip' => '1',
				'editemailreceiptincfile' => '0',
				'editemailreceiptmodifiedonly' => '0'
			);
			if (!empty($emailreceiptsettings)) {
				if (isset($emailreceiptsettings['editemailreceipt'])) {
					$editemailreceiptsettings['editemailreceipt'] = $emailreceiptsettings['editemailreceipt'];
					//if edit mail is enabled copy values from emailreceiptsettings into new edit mail parameters else keep default settings
					if (!empty($emailreceiptsettings['editemailreceipt'])) {
						$editemailreceiptsettings['editemailreceiptsubject'] = $form->emailreceiptsubject;
						$editemailreceiptsettings['editemailreceiptfrom'] = $form->emailreceiptfrom;
						$editemailreceiptsettings['editemailreceiptfromname'] = $form->emailreceiptfromname;
						$editemailreceiptsettings['editemailreceipttext'] = $form->emailreceipttext;
						foreach ($emailreceiptsettings as $pname => $pvalue) {
							$key = 'edit' . $pname;
							if (array_key_exists($key, $editemailreceiptsettings)) {
								$editemailreceiptsettings[$key] = $pvalue;
							}
						}
					}
					unset($emailreceiptsettings['editemailreceipt']);
				}
				if (isset($emailreceiptsettings['editemailreceiptmodifiedonly'])) {
					$editemailreceiptsettings['editemailreceiptmodifiedonly'] = $emailreceiptsettings['editemailreceiptmodifiedonly'];
					unset($emailreceiptsettings['editemailreceiptmodifiedonly']);
				}
				$registry = new JRegistry;
				$registry->loadArray($emailreceiptsettings);
				$newform->emailreceiptsettings = (string)$registry;
			}
			$registry = new JRegistry;
			$registry->loadArray($editemailreceiptsettings);
			$newform->editemailreceiptsettings = (string)$registry;

			$db->updateObject('#__visforms', $newform, 'id');
		}
	}

	private function deleteUpdateSiteLinks() {
		JLog::add('*** Try to reduce number of update site links ***', JLog::INFO);
		$success = true;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($this->getComponentWhereStatement());
		$db->setQuery($query);
		try {
			$extension = $db->loadResult();
		} catch (RuntimeException $e) {
			JLog::add('Unable to get Visforms extension_id: ' . $e->getMessage(), JLog::ERROR);
			return;
		}
		if (empty($extension)) {
		    return;
        }
        $updateSiteIds = $this->getUpdateSites($extension);
		if (empty($updateSiteIds)) {
		    return;
        }
		$in = implode(", ", $updateSiteIds);
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__update_sites_extensions'));
		$query->where($db->quoteName('update_site_id') . ' IN (' . $in . ')');
		$db->setQuery($query);
		try {
			$db->execute();
		} catch (RuntimeException $e) {
			JLog::add('Problems deleting record sets in #__update_sites_extensions : ' . $e->getMessage(), JLog::ERROR);
			return;
		}
        foreach ($updateSiteIds as $updateSiteId) {
	        $query = $db->getQuery(true);
	        $query->delete($db->quoteName('#__update_sites'));
	        $query->where($db->quoteName('update_site_id') . ' = ' . $updateSiteId);
	        $db->setQuery($query);
	        try {
		        $db->execute();
	        } catch (RuntimeException $e) {
		        JLog::add('Problems deleting record sets in #__update_sites : ' . $e->getMessage(), JLog::ERROR);
	        }
        }
    }

	private function getUpdateSites($extension)
	{

		$db = JFactory::getDbo();
		$extendWheres = array(
			$db->quoteName('#__update_sites.location') . ' LIKE ' . $db->q('%visforms_3_2%'),
            $db->quoteName('#__update_sites.location') . ' LIKE ' . $db->q('%visforms_3_5%'),
			$db->quoteName('#__update_sites.location') . ' LIKE ' . $db->q('%visforms_3_6%'),
			$db->quoteName('#__update_sites.location') . ' LIKE ' . $db->q('%visforms_3_7%')
		);
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('#__update_sites.update_site_id'))
			->from($db->quoteName('#__update_sites_extensions'))
            ->leftJoin('#__update_sites ON #__update_sites.update_site_id = #__update_sites_extensions.update_site_id')
			->where($db->quoteName('extension_id') . ' = ' . $db->quote($extension))
            ->extendWhere('AND', $extendWheres, 'OR');
		$db->setQuery($query);
		try {
			$update_site_ids = $db->loadColumn();
		} catch (RuntimeException $e) {
			return false;
		}
		return $update_site_ids;
	}
}

?>