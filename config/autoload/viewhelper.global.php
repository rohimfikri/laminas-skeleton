<?php

return [
    'view_helpers'=> [
        'factories' => [
            Core\Helper\View\notika\TopMenu::class => Core\Factory\MainFactory::class,
            Core\Helper\View\notika\MobileMenu::class => Core\Factory\MainFactory::class,
        ],
        'aliases' => [
            'notikaTopMenu' => Core\Helper\View\notika\TopMenu::class,
            'notikaMobileMenu' => Core\Helper\View\notika\MobileMenu::class,
        ],
    ]
];