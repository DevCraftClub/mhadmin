<?php


/**
 * Вспомогательный класс для работы с системой управления DLE (Data Life Engine),
 * загрузкой данных и проверкой ресурсов. Содержит набор методов, облегчающих
 * выполнение AJAX-запросов, валидацию и взаимодействие с внешними и внутренними ресурсами.
 *
 */
class MhAjax {
	use AssetsChecker;
	use UpdatesChecker;
	use DataLoader;
	use DleData;

	/**
	 * Конструктор класса MhAjax.
	 *
	 * Инициализирует объект класса, содержащего вспомогательные методы
	 * для работы с DLE, загрузкой данных и проверкой ресурсов.
	 */
	public function __construct() {
	}

	/**
	 * Возвращает URL панели администратора DLE.
	 *
	 * Использует глобальный массив настроек DLE `$config` для формирования полного URL.
	 * Формат возвращаемого значения: http(s)://your-site.com/admin_path.
	 *
	 * @global array $config Глобальный массив конфигурации DLE.
	 * @return string Конечный URL панели администратора DLE.
	 */

	public function getDleUrl() {
		global $config;

		return "{$config['http_home_url']}{$config['admin_path']}";
	}
}