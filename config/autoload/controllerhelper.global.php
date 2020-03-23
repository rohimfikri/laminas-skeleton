<?php

return [
    'controller_plugins' => array(
        'factories' => [
            Core\Helper\Data\Generator::class => Core\Factory\MainFactory::class,
            Core\Helper\Mail\Email::class => Core\Factory\MainFactory::class,
            Core\Helper\Authentication\Auth::class => Core\Factory\MainFactory::class,
        ],
        'aliases' => array(
            'DataGenerator' => Core\Helper\Data\Generator::class,
            'Email' => Core\Helper\Mail\Email::class,
            'Auth' => Core\Helper\Authentication\Auth::class,
        )
    ),
];