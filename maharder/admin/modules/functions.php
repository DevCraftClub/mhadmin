<?php

//===============================================================
// Mod: MaHarder_Admin
// File: functions.php
// Path: /engine/inc/maharder/admin/modules/functions.php
// ============================================================ =
// Author: Maxim Harder <dev@devcraft.club> (c) 2020 
// Website: https://www.devcraft.club
// Telegram: http://t.me/MaHarder
// ============================================================ =
// Do not change anything!                                      
//===============================================================

function letsCurl($section, $request = 'GET', $header = array(), $post = array()) {
	global $config;
	
	$curl = curl_init();
	
	$headerOptions = [
		"Content-Type: application/x-www-form-urlencoded",
		"x-api-key: {$config['api_key']}",
	];
		
	if(!empty($header))	foreach($header as $head) $headerOptions[] = $head;
	
	$curlOptions = array(
		CURLOPT_URL => "{$config['api_url']}/{$section}/",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_HTTPHEADER => $headerOptions,
		CURLOPT_CUSTOMREQUEST => $request,
	);
	if(!empty($post)) $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($post);
	curl_setopt_array($curl, $curlOptions);

	$response = curl_exec($curl);

	curl_close($curl);
	return $response;
}

function htmlStatic($data = array(), $type = 'css') {
	$out = array();
	
	foreach($data as $d) {
		if($type == 'css') $out[] = "<link rel='stylesheet' type='text/css' href='{$d}'>";
		elseif($type == 'js') $out[] = "<srcipt src='{$d}'></script>";
	}
	
	return $out;
}