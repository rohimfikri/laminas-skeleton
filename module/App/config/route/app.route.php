<?php
namespace App;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'app' => [
        'type'    => Literal::class,
        'options' => [
            'route'    => '/',
            'defaults' => [
                'controller' => Controller\IndexController::class,
                'action'     => 'index',
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'auth' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => 'user-:action',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                    ],
                    'constraints' => [
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
            ],
        ]
    ]
];