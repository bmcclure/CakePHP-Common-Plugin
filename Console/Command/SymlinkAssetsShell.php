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
		$paths = $this->getPaths();
		foreach ($paths as $plugin => $config) {
			$this->out('<info>Processing</info> ' . $plugin);
			if (file_exists($config['public'])) {
				$this->out('--> <warning>Already exists</warning>');
				if (is_link($config['public'])) {
					$symlinkTarget = readlink($config['public']);
					$this->out('----> Current target is ' . $symlinkTarget);
					if ($config['private'] === $symlinkTarget) {
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

			if (symlink($config['private'], $config['public'])) {
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
			if ($config['private'] !== $symlinkTarget) {
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
				'public' => WWW_ROOT . $this->convertPlugin($plugin),
				'private' => $pluginWebrootPath
			);
		}

		return $items;
	}
}