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
                            'route'    => '/edit/:uid',
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
            'manage-role' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-role',
                    'defaults' => [
                        'title' => 'Manage Role',
                        'show_title' => true,
                        'action'     => 'listrole',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-role' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add Role',
                                'show_title' => true,
                                'action'     => 'mgtroleadd',
                            ]
                        ],
                    ],
                    'edit-role' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:code',
                            'defaults' => [
                                'title' => 'Edit Role',
                                'show_title' => true,
                                'action'     => 'mgtroleedit',
                            ],
                            'constraints' => [
                                // 'code'     => '[a-zA-Z]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-ubis' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-ubis',
                    'defaults' => [
                        'title' => 'Manage BU',
                        'show_title' => true,
                        'action'     => 'listubis',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-ubis' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add BU',
                                'show_title' => true,
                                'action'     => 'mgtubisadd',
                            ]
                        ],
                    ],
                    'edit-ubis' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:code',
                            'defaults' => [
                                'title' => 'Edit BU',
                                'show_title' => true,
                                'action'     => 'mgtubisedit',
                            ],
                            'constraints' => [
                                // 'code'     => '[a-zA-Z1-9]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-ubislevel' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-ubislevel',
                    'defaults' => [
                        'title' => 'Manage BU',
                        'show_title' => true,
                        'action'     => 'listubislevel',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-ubislevel' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add BU',
                                'show_title' => true,
                                'action'     => 'mgtubisleveladd',
                            ]
                        ],
                    ],
                    'edit-ubislevel' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:code',
                            'defaults' => [
                                'title' => 'Edit BU',
                                'show_title' => true,
                                'action'     => 'mgtubisleveledit',
                            ],
                            'constraints' => [
                                // 'code'     => '[a-zA-Z]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-menu' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-menu',
                    'defaults' => [
                        'title' => 'Manage Menu',
                        'show_title' => true,
                        'action'     => 'listmenu',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-menu' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add Menu',
                                'show_title' => true,
                                'action'     => 'mgtmenuadd',
                            ]
                        ],
                    ],
                    'edit-menu' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:mid',
                            'defaults' => [
                                'title' => 'Edit Menu',
                                'show_title' => true,
                                'action'     => 'mgtmenuedit',
                            ],
                            'constraints' => [
                                'mid'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-module' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-module',
                    'defaults' => [
                        'title' => 'Manage Module',
                        'show_title' => true,
                        'action'     => 'listmodule',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-module' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add Module',
                                'show_title' => true,
                                'action'     => 'mgtmoduleadd',
                            ]
                        ],
                    ],
                    'edit-module' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'title' => 'Edit Module',
                                'show_title' => true,
                                'action'     => 'mgtmoduleedit',
                            ],
                            'constraints' => [
                                'id'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-controller' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-controller',
                    'defaults' => [
                        'title' => 'Manage Controller',
                        'show_title' => true,
                        'action'     => 'listcontroller',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-controller' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add Controller',
                                'show_title' => true,
                                'action'     => 'mgtcontrolleradd',
                            ]
                        ],
                    ],
                    'edit-controller' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'title' => 'Edit Controller',
                                'show_title' => true,
                                'action'     => 'mgtcontrolleredit',
                            ],
                            'constraints' => [
                                'id'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-action' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-action',
                    'defaults' => [
                        'title' => 'Manage Action',
                        'show_title' => true,
                        'action'     => 'listaction',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-action' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add Action',
                                'show_title' => true,
                                'action'     => 'mgtactionadd',
                            ]
                        ],
                    ],
                    'edit-action' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'title' => 'Edit Action',
                                'show_title' => true,
                                'action'     => 'mgtactionedit',
                            ],
                            'constraints' => [
                                'id'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-useraccess' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-useraccess',
                    'defaults' => [
                        'title' => 'Manage User Access',
                        'show_title' => true,
                        'action'     => 'listuseraccess',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-useraccess' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add User Access',
                                'show_title' => true,
                                'action'     => 'mgtuseraccessadd',
                            ]
                        ],
                    ],
                    'edit-useraccess' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'title' => 'Edit User Access',
                                'show_title' => true,
                                'action'     => 'mgtuseraccessedit',
                            ],
                            'constraints' => [
                                'id'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-roleaccess' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-roleaccess',
                    'defaults' => [
                        'title' => 'Manage Role Access',
                        'show_title' => true,
                        'action'     => 'listroleaccess',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-roleaccess' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add Role Access',
                                'show_title' => true,
                                'action'     => 'mgtroleaccessadd',
                            ]
                        ],
                    ],
                    'edit-roleaccess' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'title' => 'Edit Role Access',
                                'show_title' => true,
                                'action'     => 'mgtroleaccessedit',
                            ],
                            'constraints' => [
                                'id'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-rolemap' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-rolemap',
                    'defaults' => [
                        'title' => 'Manage Role Mapping',
                        'show_title' => true,
                        'action'     => 'listrolemap',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-rolemap' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add Role Mapping',
                                'show_title' => true,
                                'action'     => 'mgtrolemapadd',
                            ]
                        ],
                    ],
                    'edit-rolemap' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'title' => 'Edit Role Mapping',
                                'show_title' => true,
                                'action'     => 'mgtrolemapedit',
                            ],
                            'constraints' => [
                                'id'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-usermenu' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-usermenu',
                    'defaults' => [
                        'title' => 'Manage User Menu',
                        'show_title' => true,
                        'action'     => 'listusermenu',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-usermenu' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add User Menu',
                                'show_title' => true,
                                'action'     => 'mgtusermenuadd',
                            ]
                        ],
                    ],
                    'edit-usermenu' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'title' => 'Edit User Menu',
                                'show_title' => true,
                                'action'     => 'mgtusermenuedit',
                            ],
                            'constraints' => [
                                'id'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
            'manage-rolemenu' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/manage-rolemenu',
                    'defaults' => [
                        'title' => 'Manage Role Menu',
                        'show_title' => true,
                        'action'     => 'listrolemenu',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'add-rolemenu' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'title' => 'Add Role Menu',
                                'show_title' => true,
                                'action'     => 'mgtrolemenuadd',
                            ]
                        ],
                    ],
                    'edit-rolemenu' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:menu/:role',
                            'defaults' => [
                                'title' => 'Edit Role Menu',
                                'show_title' => true,
                                'action'     => 'mgtrolemenuedit',
                            ],
                            'constraints' => [
                                'menu'     => '[0-9]*',
                            ],
                        ],
                    ],
                ]
            ],
        ]
    ]
];