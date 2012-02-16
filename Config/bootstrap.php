<?php
App::import('Lib', 'Common.Nodes/Autoload');
Nodes\Autoload::register();
Nodes\Autoload::addPath(App::path('Lib'));
Nodes\Autoload::addPath(App::pluginPath('Common') . 'Lib' . DS);
