<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  plg_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2017 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class pkg_cjblogInstallerScript 
{
	public function preflight( $type, $parent )
	{
		if(version_compare(PHP_VERSION, '5.5', '<'))
		{
			return false;
		}
		
		return true;
	}
	
	public function postflight($type, $parent)
	{
		$installCjLib = false;
		if(!file_exists(JPATH_ROOT.'/components/com_cjlib/framework.php'))
		{
			$installCjLib = true;
		}
		
		if(!$installCjLib)
		{
			require_once JPATH_ROOT . '/components/com_cjblog/helpers/constants.php';
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('manifest_cache')
				->from($db->quoteName('#__extensions'))
				->where('element = ' . $db->q('pkg_cjlib'));
			$db->setQuery($query);
			
			$manifest = json_decode($db->loadResult(), true);
			$installedCjLibVersion = $manifest['version'];
			if(!$installedCjLibVersion || version_compare(CQ_CJLIB_VER, $installedCjLibVersion, '>'))
			{
				$installCjLib = true;
			}
		}
		
		if($installCjLib)
		{
			$cjLibVer	= version_compare(PHP_VERSION, '5.6', '<') ? '2.6.6' : '2.7.1'; 
			$url 		= 'https://www.corejoomla.com/media/autoupdates/files/pkg_cjlib_v'.$cjLibVer.'.zip';
			$package 	= $this->downloadPackage($url);
			$return		= $this->installPackage($package);
		}
		
		echo '<p>CjBlog Package:</p> 
		<table class="table table-hover table-striped"> 
		<tr><td>CjBlog Component</td><td>Successfully installed</td></tr>
		<tr><td>CjBlog CjBlog Bloggers Module</td><td>Successfully installed</td></tr>
		<tr><td>CjBlog CjBlog Categories Module</td><td>Successfully installed</td></tr>
		<tr><td>CjBlog CjBlog Archive Module</td><td>Successfully installed</td></tr>
		<tr><td>CjBlog CjBlog Apps Plugin</td><td>Successfully installed</td></tr>
		<tr><td>CjBlog Content Plugin</td><td>Successfully installed</td></tr>
		<tr><td>CjBlog User Plugin</td><td>Successfully installed</td></tr>
		</table>
		<p><strong style="color: red;">Please install CjLib component if not yet installed. Please enable the plugins from plugin manager.</strong></p>
		<p>Thank you for using corejoomla&reg; software. Please add a rating and review at Joomla&reg; Extension Directory.</p>';
	}
	
	private function installPackage($package)
	{
		// Get an installer instance.
		$app = JFactory::getApplication();
		$installer = JInstaller::getInstance();
		
		if (is_array($package) && isset($package['dir']) && is_dir($package['dir']))
		{
			$installer->setPath('source', $package['dir']);
			
			if (!$installer->findManifest())
			{
				// If a manifest isn't found at the source, this may be a Joomla package; check the package directory for the Joomla manifest
				if (file_exists($package['dir'] . '/administrator/manifests/files/joomla.xml'))
				{
					// We have a Joomla package
					JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
					
					$app->enqueueMessage(
							JText::sprintf('COM_INSTALLER_UNABLE_TO_INSTALL_JOOMLA_PACKAGE', JRoute::_('index.php?option=com_joomlaupdate')),
							'warning'
							);
					
					return false;
				}
			}
		}
		
		// Was the package unpacked?
		if (!$package || !$package['type'])
		{
			JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
			$app->enqueueMessage(JText::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');
			
			return false;
		}
		
		// Install the package.
		if (!$installer->install($package['dir']))
		{
			// There was an error installing the package.
			$app->enqueueMessage(JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($package['type']))));
			
			return false;
		}
		
		return true;
	}
	
	private function downloadPackage($url)
	{
		// Download the package at the URL given.
		$p_file = JInstallerHelper::downloadPackage($url);
		
		// Was the package downloaded?
		if (!$p_file)
		{
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
			
			return false;
		}
		
		$config   = JFactory::getConfig();
		$tmp_dest = $config->get('tmp_path');
		
		// Unpack the downloaded package file.
		$package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file, true);
		return $package;
	}
}