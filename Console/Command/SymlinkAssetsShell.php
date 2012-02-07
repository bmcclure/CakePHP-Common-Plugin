<?php
/**
* Symlink Assets Shell
*
* Manages symlinks between your plugins/webroot and app/webroot/plugin
*
* @platform
* @package Common.Console.Command
* @copyright Nodes ApS 2010-2012 <tech@nodes.dk>
*/
class SymlinkAssetsShell extends AppShell {

	/**
	* Create symlinks from each plugin to app/webroot
	*
	* @return void
	*/
	public function create() {
		chdir(WWW_ROOT);

		$paths = $this->getPaths();
		foreach ($paths as $plugin => $config) {
			$this->out('<info>Processing</info> ' . $plugin);
			if (file_exists($config['public'])) {
				$this->out('--> <warning>Already exists</warning>');
				if (is_link($config['public'])) {
					$symlinkTarget = readlink($config['public']);
					$this->out('----> Current target is ' . $symlinkTarget);
					if ($config['relative_private'] === $symlinkTarget) {
						$this->out('------> <info>Skipping</info>, Already configured correctly');
						continue;
					}
					$this->out('--> <error>Target is already symlink, but does not have same source</error>');
					continue;
				} elseif (is_file($config['public'])) {
					$this->out('----> <error>Skipping, target is a file</error>');
					continue;
				}
			}

			if (symlink($config['relative_private'], $config['relative_public'])) {
				$this->out('--> OK');
			} else {
				$this->out('--> Error');
			}
		}
	}

	/**
	* Remove symlinks from each plugin to app/webroot
	*
	* @return void
	*/
	public function remove() {
		chdir(WWW_ROOT);

		$paths = $this->getPaths();
		foreach ($paths as $plugin => $config) {
			$this->out('<info>Processing</info> ' . $plugin);
			if (!file_exists($config['public'])) {
				$this->out('--> <info>Skipping</info>, does not exists');
				continue;
			}

			if (!is_link($config['public'])) {
				$this->out('--> <error>Skipping, target is not a symlink</error>');
				ctoninue;
			}

			$symlinkTarget = readlink($config['public']);
			$this->out('----> Current target is ' . $symlinkTarget);
			if ($config['relative_private'] !== $symlinkTarget) {
				$this->out('--> <error>Skipping, symlink source does not match ours</error>');
				continue;
			}

			if (unlink($config['public'])) {
				$this->out('--> OK');
			} else {
				$this->out('--> Error');
			}
		}
	}

	/**
	* Convert the plugin name to what ever format you use
	*
	* By default it converts PluginName to plugin_name
	*
	* @param string $plugin
	* @return string
	*/
	protected function convertPlugin($plugin) {
		return Inflector::underscore($plugin);
	}

	/**
	* Get the relative path between two directories
	*
	* @param string $from
	* @param string $to
	* @return string
	*/
	protected function relativePath($from, $to) {
		$arFrom	= explode(DS, rtrim($from, DS));
		$arTo	= explode(DS, rtrim($to, DS));

		while (count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
			array_shift($arFrom);
			array_shift($arTo);
		}

		return str_pad("", (count($arFrom)-1) * 3, '..' . DS) . implode(DS, $arTo);
	}

	/**
	* Build a list of valid plugin webroots
	*
	* @return array
	*/
	protected function getPaths() {
		$items = array();
		foreach(CakePlugin::loaded() as $plugin) {
			$pluginWebrootPath = CakePlugin::path($plugin) . 'webroot';
			if (!is_dir($pluginWebrootPath)) {
				continue;
			}

			$items[$plugin] = array(
				'public'	=> WWW_ROOT . $this->convertPlugin($plugin),
				'private'	=> $pluginWebrootPath
			);

			$items[$plugin]['relative_private']	= $this->relativePath($items[$plugin]['public'], $items[$plugin]['private']);
			$items[$plugin]['relative_public']	= $this->convertPlugin($plugin);
		}
		return $items;
	}
}