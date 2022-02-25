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
	$mh_admin->generate_log('maharder', 'save_setting', serialize(sprintf('Папка "%s" не была создана',
		$concurrentDirectory)));
}
$file = $concurrentDirectory.'/'.$_POST['module'].'.json';


if (empty($data['list_count']) || !isset($data['list_count'])) {
	$data['list_count'] = $config['news_number'];
}


$data = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
file_put_contents($file, $data);
clear_cache();

echo 'ok';