<?php

	if (!$is_logged) {
		exit('error');
	}

	if ('' == $_REQUEST['user_hash'] or $_REQUEST['user_hash'] != $dle_login_hash) {
		exit('error');
	}

	if (!mkdir($concurrentDirectory = ENGINE_DIR . '/inc/maharder/_config', 0777, true)
	    && !is_dir(
			$concurrentDirectory
		)) {
		LogGenerator::setLogs(1);
		LogGenerator::generate_log('maharder', 'save_setting', sprintf('Папка "%s" не была создана',
		                                                               $concurrentDirectory));
	}
	$file = $concurrentDirectory.'/'.$_POST['module'].'.json';


	if (empty($save_con['list_count']) || !isset($save_con['list_count'])) {
		$save_con['list_count'] = $config['news_number'];
	}

	if (empty($save_con['cache_timer']) || !isset($save_con['cache_timer'])) {
		$save_con['cache_timer'] = 60;
	}

	if (empty($save_con['theme']) || !isset($save_con['theme'])) {
		$save_con['theme'] = 'light';
	}

	file_put_contents($file, json_encode($save_con, JSON_UNESCAPED_UNICODE));
	clear_cache();

	echo 'ok';
