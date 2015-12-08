<?php
use Cake\Routing\Router;

Router::plugin('Monitor', function ($routes) {
    $routes->connect('/', ['plugin' => 'Monitor', 'controller' => 'Check', 'action' => 'check']);
    $routes->fallbacks('DashedRoute');
});
