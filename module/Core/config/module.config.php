<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Core;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
// !d(ManagerInterface::class);die();

return [
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'layout' => 'layout/blank',
        'default_template_suffix' => 'phtml',
        'template_map' => [
            'layout/main' => APP_PATH.'/view/layout/hogo-layout.phtml',
            'layout/hogo' => APP_PATH.'/view/layout/hogo-layout.phtml',
            'layout/blank' => APP_PATH.'/view/layout/blank-layout.phtml',

            'error/404' => APP_PATH.'/view/error/404-A.phtml',
            'error/index' => APP_PATH.'/view/error/index.phtml',
            'error/error' => APP_PATH.'/view/error/error.phtml',
        ],
        'template_path_stack' => [
            "Core"=>APP_PATH . '/view',
            "Error"=>APP_PATH . '/view/error',
            "Layout"=>APP_PATH . '/view/layout',
            "Render"=>APP_PATH . '/view/render',
        ],
        'strategies' => [
            'ViewJsonStrategy', // register JSON renderer strategy
            'ViewFeedStrategy', // register Feed renderer strategy
        ],
    ],
];
