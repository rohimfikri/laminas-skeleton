<?php
namespace App;
use Core\Factory\MainFactory;

return [
    'factories' => [
        Model\SysModel::class => MainFactory::class,
        Model\UbisModel::class => MainFactory::class,
        Model\UbislevelModel::class => MainFactory::class,
        Model\RoleModel::class => MainFactory::class,
        Model\UserModel::class => MainFactory::class,
        Model\MenuModel::class => MainFactory::class,
        Model\InboxModel::class => MainFactory::class,
        Model\AlertModel::class => MainFactory::class
    ],
    'invokables' => [
        
    ],
];
