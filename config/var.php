<?php
global $PUBLIC_CONTROLLER;
$PUBLIC_CONTROLLER = [
    'App\Controller\IndexController' => [
        '*'
    ],
    'App\Controller\AuthController' => [
        '*'
    ],
    'App\Controller\UserController' => [
        'profile'
    ]
];