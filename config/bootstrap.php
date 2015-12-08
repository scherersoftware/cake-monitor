<?php
use Cake\Core\Configure;
use Cake\Utility\Hash;

// Load and merge default with app config
$config = include 'monitor.default.php';
$config = $config['CakeMonitor'];
if ($appMonitorConfig = Configure::read('CakeMonitor')) {
    $config = Hash::merge($config, $appMonitorConfig);
}
Configure::write('CakeMonitor', $config);