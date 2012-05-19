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

	public function main() {
		$this->out($this->OptionParser->help());
	}

	protected function _welcome() {
		// Empty
	}

/**
 * Create symlinks from each plugin to app/webroot
 *
 * @return void
 */
	public function create() {
		chdir(WWW_ROOT);

		$paths = $this->_getPaths();
		foreach ($paths as $plugin => $config) {
			$this->out('Processing plugin: <info>' . $plugin . '</info>');
			if (file_exists($config['public'])) {
				$this->out(sprintf('--> <warning>Path "%s" already exists</warning>', \Nodes\Common::stripRealPaths($config['public'])));
				if (is_link($config['public'])) {
					$symlinkTarget = readlink($config['public']);
					$this->out('----> Path is already symlink. (' . $symlinkTarget . ')');
					if ($config['relative_private'] === $symlinkTarget) {
						$this->out('----> <info>Skipping</info>, Already configured correctly');
						continue;
					}
					$this->out('--> <error>Target is already symlink, but does not have same source</error>');
					continue;
				} elseif (is_file($config['public'])) {
					$this->out('----> Skipping, target is a file');
					continue;
				} else {
					$this->out('----> <error>Skipping, don\'t know how to process file</error>');
					continue;
				}
			}

			if (symlink($config['relative_private'], $config['relative_public'])) {
				$this->out(sprintf('--> <info>OK</info>, symlink created (%s => %s)', \Nodes\Common::stripRealPaths($config['private']), \Nodes\Common::stripRealPaths($config['public'])));
			} else {
				$this->out('--> <error>Error</error> Could not create symlink');
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

		$paths = $this->_getPaths();
		foreach ($paths as $plugin => $config) {
			$this->out('Processing plugin: <info>' . $plugin . '</info>');
			if (!file_exists($config['public'])) {
				$this->out('--> <error>Skipping</error>, symlink does not exists (' . \Nodes\Common::stripRealPaths($config['public']) . ')');
				continue;
			}

			if (!is_link($config['public'])) {
				$this->out('--> <error>Skipping, target is not a symlink</error>');
				continue;
			}

			$symlinkTarget = readlink($config['public']);
			$this->out('----> Current target is ' . $symlinkTarget);
			if ($config['relative_private'] !== $symlinkTarget) {
				$this->out('--> <error>Skipping, symlink source does not match ours</error>');
				continue;
			}

			if (unlink($config['public'])) {
				$this->out(sprintf('--> <info>OK</info>, symlink removed (%s => %s)', \Nodes\Common::stripRealPaths($config['private']), \Nodes\Common::stripRealPaths($config['public'])));
			} else {
				$this->out('--> <error>Error</error>');
			}
		}
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser
			->addSubcommand('create', array('help' => 'Create symlinks for all plugin assets.'))
			->addSubcommand('remove', array('help' => 'Remove all plugin asset symlinks.'))
			->description('Manage plugin asset symlinks so they can be served directly by apache.');
		//configure parser
		return $parser;
	}

/**
 * Convert the plugin name to what ever format you use
 *
 * By default it converts PluginName to plugin_name
 *
 * @param string $plugin
 * @return string
 */
	protected function _convertPlugin($plugin) {
		return Inflector::underscore($plugin);
	}

/**
 * Get the relative path between two directories
 *
 * @param string $from
 * @param string $to
 * @return string
 */
	protected function _relativePath($from, $to) {
		$arFrom	= explode(DS, rtrim($from, DS));
		$arTo	= explode(DS, rtrim($to, DS));

		$size = count($arTo);
		while (count($arFrom) && $size && ($arFrom[0] == $arTo[0])) {
			array_shift($arFrom);
			array_shift($arTo);
		}

		return str_pad("", (count($arFrom) - 1) * 3, '..' . DS) . implode(DS, $arTo);
	}

/**
 * Build a list of valid plugin webroots
 *
 * @return array
 */
	protected function _getPaths() {
		$items = array();
		foreach (CakePlugin::loaded() as $plugin) {
			$pluginWebrootPath = CakePlugin::path($plugin) . 'webroot';
			if (!is_dir($pluginWebrootPath)) {
				continue;
			}

			$items[$plugin] = array(
				'public'	=> WWW_ROOT . $this->_convertPlugin($plugin),
				'private'	=> $pluginWebrootPath
			);

			$items[$plugin]['relative_private']	= $this->_relativePath($items[$plugin]['public'], $items[$plugin]['private']);
			$items[$plugin]['relative_public']	= $this->_convertPlugin($plugin);
		}
		return $items;
	}
}