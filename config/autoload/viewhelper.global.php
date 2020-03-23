<?php

return [
    'view_helpers'=> [
        'factories' => [
            Core\Helper\Data\Generator::class => Core\Factory\MainFactory::class,
            Core\Helper\View\notika\TopMenu::class => Core\Factory\MainFactory::class,
            Core\Helper\View\notika\MobileMenu::class => Core\Factory\MainFactory::class,
            Core\Helper\View\notika\Notification::class => Core\Factory\MainFactory::class,
            Core\Helper\View\notika\Generator::class => Core\Factory\MainFactory::class,
        ],
        'aliases' => [
            'dataGenerator' => Core\Helper\Data\Generator::class,
            'notikaTopMenu' => Core\Helper\View\notika\TopMenu::class,
            'notikaMobileMenu' => Core\Helper\View\notika\MobileMenu::class,
            'notikaNotif' => Core\Helper\View\notika\Notification::class,
            'notikaGenerator' => Core\Helper\View\notika\Generator::class,
        ],
    ]
];