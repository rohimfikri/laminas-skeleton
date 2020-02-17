<?php

return [
    'controller_plugins' => array(
        'factories' => [
            Core\Helper\Mail\Email::class => Core\Factory\MainFactory::class,
            Core\Helper\Authentication\Auth::class => Core\Factory\MainFactory::class,
        ],
        'aliases' => array(
            'Email' => Core\Helper\Mail\Email::class,
            'Auth' => Core\Helper\Authentication\Auth::class,
        )
    ),
];