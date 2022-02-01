<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

return static function (RouteBuilder $routes): void
{
    $routes->plugin('Monitor', static function (RouteBuilder $builder): void {
        $builder->connect('/', ['plugin' => 'Monitor', 'controller' => 'Check', 'action' => 'check']);
        $builder->fallbacks('DashedRoute');
    });
};
