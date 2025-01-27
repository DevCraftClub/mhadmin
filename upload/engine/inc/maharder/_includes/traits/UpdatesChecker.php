<?php

/**
 * Трейт UpdatesChecker предоставляет функции для работы с информацией об обновлениях ресурсов.
 * Основной функционал включает методы для установки и получения параметров API, таких как URL для запросов,
 * ключ API и идентификатор ресурса, а также метод для проверки обновлений ресурса через API.
 *
 * @property string   $update_url  URL API, используемый для проверки обновлений.
 * @property string   $api_key     Гостевой ключ с доступом к информации о ресурсе.
 * @property int|null $recource_id Идентификатор ресурса, для которого проверяются обновления.
 */
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
	 * @param int $recource_id
	 */
	public function setRecourceId(int $recource_id): void {
		$this->recource_id = $recource_id;
	}

	/**
	 * Проверяет наличие и обновления ресурса, используя указанный или текущий идентификатор ресурса.
	 *
	 * @param int|null $res Идентификатор ресурса. Если не указан, используется идентификатор, полученный методом
	 *                      {@see getRecourceId()}.
	 *
	 * @return array Возвращает массив с данными об обновлении ресурса или список ошибок.
	 * @throws JsonException|Throwable        Исключение, связанное с ошибками в JSON-конверсии (может быть выброшено
	 *                                        при выполнении Telegram-лога).
	 * @see LogGenerator::generateLog() Для генерации логов при ошибках.
	 * @see Curl Для выполнения HTTP-запросов.
	 */
	public function checkUpdate(?int $res = null): array {
		$res_id = $res ?? $this->getRecourceId();
		if ($res_id === null) LogGenerator::generateLog(
			'UpdatesChecker',
			'checkUpdate',
			__('ID ресурса не было указано')
		);
		$curl = new Curl();
		$curl->setHeader('XF-Api-Key', $this->getApiKey());
		$curl->get($this->getUpdateUrl() . $res_id . '/');

		if ($curl->error) LogGenerator::generateLog(
			'UpdatesChecker',
			'checkUpdate',
			'Ошибка: ' . $curl->errorCode . ': ' . $curl->errorMessage
		);

		$res = $curl->response;
		$curl->close();

		if (!$curl->response->errors) {
			return [
				'download_link'  => $res->resource->view_url . 'download/',
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
				'data'   => $res->errors,
			];
		}
	}
}