<?php
return [
	'db' => [
		'driver' 	=> _DBDUAL_DRIVER_,
		'hostname' 	=> _DBDUAL_HOST_,
		'port' 		=> _DBDUAL_PORT_,
		'username' 	=> _DBDUAL_USER_,
		'password' 	=> _DBDUAL_PASSWORD_,
		'database' 	=> _DBDUAL_NAME_,
		'options' => [
			PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION
		],
		'adapters' => [
			'db-dual' => [
				'driver' 	=> _DBDUAL_DRIVER_,
				'hostname' 	=> _DBDUAL_HOST_,
				'port' 		=> _DBDUAL_PORT_,
				'username' 	=> _DBDUAL_USER_,
				'password' 	=> _DBDUAL_PASSWORD_,
				'database' 	=> _DBDUAL_NAME_
			],
			'db-app' => [
				'driver' => _DBAPP_DRIVER_,
				'hostname' => _DBAPP_HOST_,
				'port' => _DBAPP_PORT_,
				'username' => _DBAPP_USER_,
				'password' => _DBAPP_PASSWORD_,
				'database' => _DBAPP_NAME_
			],
			'db-sys' => [
				'driver' => _DBSYS_DRIVER_,
				'hostname' => _DBSYS_HOST_,
				'port' => _DBSYS_PORT_,
				'username' => _DBSYS_USER_,
				'password' => _DBSYS_PASSWORD_,
				'database' => _DBSYS_NAME_,
				'options' => [
					PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION
				],
			],
			// 'db-oci' => [
			// 	'driver' => _DBOCI_DRIVER_,
			// 	'hostname' => _DBOCI_HOST_,
			// 	'port' => _DBOCI_PORT_,
			// 	'username' => _DBOCI_USER_,
			// 	'password' => _DBOCI_PASSWORD_,
			// 	'database' => _DBOCI_NAME_,
			// 	'connection_string' => _DBOCI_CONNSTRING_,
			// 	'character_set' => _DBOCI_CHARSET_
			// ],
		]
	],
];
