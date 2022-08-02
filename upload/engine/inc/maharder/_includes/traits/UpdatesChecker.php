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
	public function getUpdateUrl(): string {
		return $this->update_url;
	}

	/**
	 * @param string $update_url
	 */
	public function setUpdateUrl(string $update_url): void {
		$this->update_url = $update_url;
	}

	/**
	 * @return string
	 */
	public function getApiKey(): string {
		return $this->api_key;
	}

	/**
	 * @param string $api_key
	 */
	public function setApiKey(string $api_key): void {
		$this->api_key = $api_key;
	}

	/**
	 * @return int|null
	 */
	public function getRecourceId(): ?int {
		return $this->recource_id;
	}

	/**
	 * @param int|null $recource_id
	 */
	public function setRecourceId(?int $recource_id): void {
		$this->recource_id = $recource_id;
	}

	/**
	 * @throws \Monolog\Handler\MissingExtensionException
	 */
	public function checkUpdate(?int $res) {
		$res_id = $res !== null ? $res : $this->getRecourceId();
		if ($res_id === null) LogGenerator::generate_log('UpdatesChecker', 'checkUpdate', 'ID ресурса не было указано');
		$curl = new Curl();
	}
}