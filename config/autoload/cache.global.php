<?php
return [
	'caches' => [
		'session-file' => [
			'adapter' => [
				'name'=>'filesystem',
				'options' => [
					'ttl' => _SESSION_CACHE_TTL_,
					'namespace' => _SESSION_CACHE_NAMESPACE_,
					'cache_dir' => _SESSION_CACHE_DIR_,
					'no_atime' =>false,
					'no_ctime' =>false,
					'suffix' => 'session',
					// 'dir_permission'=>770,
					// 'file_permission'=>770
				]
			]
		],
		'view-file' => [
			'adapter' => [
				'name'=>'filesystem',
				'options' => [
					'ttl' => _VIEW_CACHE_TTL_,
					'namespace' => _VIEW_CACHE_NAMESPACE_,
					'cache_dir' => _VIEW_CACHE_DIR_,
					'no_atime' =>false,
					'no_ctime' =>false,
					'suffix' => 'view',
					// 'dir_permission'=>770,
					// 'file_permission'=>770
				]
			]
		],
		'data-file' => [
			'adapter' => [
				'name'=>'filesystem',
				'options' => [
					'ttl' => _DATA_CACHE_TTL_,
					'namespace' => _DATA_CACHE_NAMESPACE_,
					'cache_dir' => _DATA_CACHE_DIR_,
					'no_atime' =>false,
					'no_ctime' =>false,
					'suffix' => 'data',
					// 'dir_permission'=>770,
					// 'file_permission'=>770
				]
			]
		]
	]
];
