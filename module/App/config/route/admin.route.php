<?php
namespace App;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'admin' => [
        'type'    => Literal::class,
        'options' => [
            'route'    => '/admin',
            'defaults' => [
                'controller' => Controller\AdminController::class,
                'title' => 'App Summary',
                'show_title' => true,
                'action'     => 'index',
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'manage-user' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-user',
                    'defaults' => [
                        'title' => 'Manage User',
                        'show_title' => true,
                        'action'     => 'listuser',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-user' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add User',
                                'show_title' => true,
                                'action'     => 'mgtuseradd',
                            ]
                        ],
                    ],
                    'edit-user' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit-:uid',
                            'defaults' => [
                                'title' => 'Edit User',
                                'show_title' => true,
                                'action'     => 'mgtuseredit',
                            ],
                            'constraints' => [
                                'uid'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
        ]
    ]
];