<?php
namespace App;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Core\Factory\MainFactory;

return [
    'factories' => [
        Controller\IndexController::class => InvokableFactory::class,
        Controller\AuthController::class => MainFactory::class,
        Controller\UserController::class => MainFactory::class,
        Controller\AdminController::class => MainFactory::class,
    ],
];