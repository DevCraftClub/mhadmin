<?php

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

use Curl\Curl;


trait UpdatesChecker {

	/**
	 * @var string
	 */
	private $update_url = 'https://devcraft.club/api/resources/';
	/**
	 * Гостевой ключ с доступом на просмотр информации ресурса на сайте
	 *
	 * @var string
	 */
	private $api_key = 'F1szgi4FegnDEHRM4COV17NQyZholz0n';
	/**
	 * @var int|null
	 */
	private $recource_id;

	/**
	 * @return string
	 */
	public function getUpdateUrl(){
		return $this->update_url;
	}

	/**
	 * @param string $update_url
	 */
	public function setUpdateUrl($update_url){
		$this->update_url = $update_url;
	}

	/**
	 * @return string
	 */
	public function getApiKey(){
		return $this->api_key;
	}

	/**
	 * @param string $api_key
	 */
	public function setApiKey($api_key){
		$this->api_key = $api_key;
	}

	/**
	 * @return int|null
	 */
	public function getRecourceId() {
		return $this->recource_id;
	}

	/**
	 * @param int|null $recource_id
	 */
	public function setRecourceId($recource_id){
		$this->recource_id = $recource_id;
	}

	/**
	 * @throws \Monolog\Handler\MissingExtensionException
	 * @return array
	 */
	public function checkUpdate($res = null) {
		$res_id = $res !== null ? $res : $this->getRecourceId();
		if ($res_id === null) LogGenerator::generate_log('UpdatesChecker', 'checkUpdate', 'ID ресурса не было указано');
		$curl = new Curl();
		$curl->setHeader('XF-Api-Key', $this->getApiKey());
		$curl->get($this->getUpdateUrl() . $res_id . '/');

		if($curl->error) LogGenerator::generate_log('UpdatesChecker', 'checkUpdate', 'Ошибка: ' . $curl->errorCode . ': ' . $curl->errorMessage);

		$res = $curl->response;
		$curl->close();

		if(!$curl->response->errors) {
			return [
				'download_link'  => $res->resource->view_url.'download/',
				'download_count' => $res->resource->download_count,
				'last_update'    => $res->resource->last_update,
				'title'          => $res->resource->title,
				'update_count'   => $res->resource->update_count,
				'site_link'      => $res->resource->view_url,
				'version'        => $res->resource->version,

			];
		} else {
			return [
				'errors' => count($res->errors),
				'data' => $res->errors,
			];
		}
	}
}