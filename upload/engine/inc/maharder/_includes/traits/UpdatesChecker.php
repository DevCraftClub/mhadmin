<?php



trait UpdatesChecker {

	/**
	 * @var string
	 */
	private string $update_url = 'https://devcraft.club/api/resources/';
	/**
	 * Гостевой ключ с доступом на просмотр информации ресурса на сайте
	 *
	 * @var string
	 */
	private string $api_key = '8uO1gW7Ge47co5Y0tTOEzZ1V0lclAvXy';
	/**
	 * @var int|null
	 */
	private ?int $recource_id;

	/**
	 * @return string
	 */
	public function getUpdateUrl(): string {
		return $this->update_url;
	}

	/**
	 * @param string $update_url
	 */
	public function setUpdateUrl(string $update_url) : void {
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
	public function setApiKey(string $api_key) : void {
		$this->api_key = $api_key;
	}

	/**
	 * @return int|null
	 */
	public function getRecourceId() : ?int {
		return $this->recource_id;
	}

	/**
	 * @param    int    $recource_id
	 */
	public function setRecourceId(int $recource_id) : void {
		$this->recource_id = $recource_id;
	}

	/**
	 * Проверяет наличие и обновления ресурса, используя указанный или текущий идентификатор ресурса.
	 *
	 * @param int|null $res Идентификатор ресурса. Если не указан, используется идентификатор, полученный методом {@see getRecourceId()}.
	 *
	 * @return array Возвращает массив с данными об обновлении ресурса или список ошибок.
	 *
	 * @see Curl Для выполнения HTTP-запросов.
	 * @see LogGenerator::generateLog() Для генерации логов при ошибках.
	 *
	 * @throws JsonException|Throwable        Исключение, связанное с ошибками в JSON-конверсии (может быть выброшено
	 *                                        при выполнении Telegram-лога).
	 */
	public function checkUpdate(?int $res = null) : array {
		$res_id = $res !== null ? $res : $this->getRecourceId();
		if ($res_id === null) LogGenerator::generateLog('UpdatesChecker', 'checkUpdate', 'ID ресурса не было указано');
		$curl = new Curl();
		$curl->setHeader('XF-Api-Key', $this->getApiKey());
		$curl->get($this->getUpdateUrl() . $res_id . '/');

		if($curl->error) LogGenerator::generateLog('UpdatesChecker', 'checkUpdate', 'Ошибка: ' . $curl->errorCode . ': ' . $curl->errorMessage);

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