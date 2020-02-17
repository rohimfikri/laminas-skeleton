<?php
namespace App;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'user' => [
        'type'    => Literal::class,
        'options' => [
            'route'    => '/user',
            'defaults' => [
                'controller' => Controller\UserController::class,
                'action'     => 'profile',
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'profile' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/profile',
                    'defaults' => [
                        'title' => 'User Profile',
                        'show_title' => true,
                        'action'     => 'profile',
                    ]
                ],
            ],
        ]
    ]
];