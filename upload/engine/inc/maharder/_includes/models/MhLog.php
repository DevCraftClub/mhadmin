<?php

	require_once (DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php'));

	class MhLog extends Model {
		public function __construct() {
			parent::__construct(
				'MhLog',
				'maharder_logs',
				'id',
				[
					[
						'name'  => 'type',
						'type'  => 'varchar',
						'limit' => 50
					],
					[
						'name'  => 'plugin',
						'type'  => 'varchar',
						'limit' => 255
					],
					[
						'name'  => 'fn_name',
						'type'  => 'varchar',
						'limit' => 255
					],
					[
						'name'  => 'time',
						'type'  => 'datetime',
						'limit' => 255,
						'default' => 'CURRENT_TIMESTAMP'
					],
					[
						'name'  => 'message',
						'type'  => 'text',
					],
				]
			);
		}

	}