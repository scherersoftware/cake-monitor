<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Monitor', static function (RouteBuilder $builder): void {
    $builder->connect('/', ['plugin' => 'Monitor', 'controller' => 'Check', 'action' => 'check']);
    $builder->fallbacks('DashedRoute');
});
