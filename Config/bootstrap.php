<?php
App::import('Lib', 'Common.Nodes/Autoload');
nodes\Autoload::register();
nodes\Autoload::addPath(App::pluginPath('Common') . 'Lib' . DS);