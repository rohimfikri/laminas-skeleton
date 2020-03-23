<?php
use Laminas\Session\Storage\SessionArrayStorage;
// use Laminas\Session\Storage\ArrayStorage;
use Laminas\Session\Validator\RemoteAddr;
use Laminas\Session\Validator\HttpUserAgent;
// use Laminas\Session\Config\SessionConfig;

return [
    // 'session_config' => [
    //     'use_cookies' => false,
    //     'use_only_cookies' => false,
    //     'name' => _SESSION_NAME_,
    //     'remember_me_seconds' => _REMEMBER_ME_,
    //     'cache_expire' => _SESSION_EXPIRE_,
    //     'cookie_lifetime'     => _COOKIE_LIFETIME_, // Session cookie will expire in 1 hour.
    //     'gc_maxlifetime'      => _GCMAX_LIFETIME_, // How long to store session data on server (for 1 month).
    //     'cookie_secure' => true,
    //     'cookie_httponly' => true,
    //     'save_path' => _SESSION_SAVE_PATH_
    // ],
    // 'session_validators' => [
    //     RemoteAddr::class,
    //     HttpUserAgent::class,
    // ],
    // // Session storage configuration.
    // 'session_storage' => [
    //     'type' => SessionArrayStorage::class
    // ],
    'session_manager' => [
        // 'enable_default_container_manager'=>false,
        'options' => [
            // 'attach_default_validators'=>false,
            // 'clear_storage'=>true
            // "samesite" => "Strict",
        ],
        'config' => [
            // 'class' => SessionConfig::class,
            // https://www.php.net/manual/en/session.configuration.php
            'options'=>[
                'use_trans_sid'    => false,
                'use_cookies' => true,
                // 'strict' => "off",
                'use_only_cookies' => true,
                'name' => _SESSION_NAME_,
                'remember_me_seconds' => _REMEMBER_ME_,
                'cache_expire' => _SESSION_EXPIRE_,
                'cookie_lifetime'     => _COOKIE_LIFETIME_, // Session cookie will expire in 1 hour.
                'gc_maxlifetime'      => _GCMAX_LIFETIME_, // How long to store session data on server (for 1 month).
                // 'gc_divisor '      => 1000,
                // 'gc_probability  '      => 1,
                'cookie_secure' => false,
                'cookie_httponly' => true,
                "cookie_samesite" => "Strict",
                'save_path' => _SESSION_SAVE_PATH_
            ]
        ],
        // Session validators (used for security). 
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ],
        'storage' => SessionArrayStorage::class,
    ],
    "session_containers" => [
        "container_init",
        "container_login",
        "container_data"
    ]
];
