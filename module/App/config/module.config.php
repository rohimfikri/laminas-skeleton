<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace App;

use Laminas\Stdlib\ArrayUtils;

$routes = [];
foreach (glob(__DIR__.'/route/*.route.php') as $filename){
    $routes = ArrayUtils::merge($routes, include $filename);
}
// var_dump($routes);die();

return [
    'service_manager' => include __DIR__ . '/service_manager.config.php',
    'router' => [
        'routes' => $routes
    ],
    'controllers' => include __DIR__.'/controllers.config.php',
    'view_manager' => [
        'template_path_stack' => [
            "App"=>__DIR__ . '/../view'
        ],
    ],
];
