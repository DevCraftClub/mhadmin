<?php
//===============================================================
// Файл: LogsPage.php                                           =
// Путь: devcraft/src/modules/Admin/Pages/LogsPage.php          =
// Последнее изменение: 2026-06-13 19:29:35                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Pages;

use DLEPlugins;
use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Types\FilterSchema;
use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Core\Admin\FilterFormService;
use DevCraft\Modules\Admin\Models\LogRecord;
use DevCraft\Modules\Admin\Repositories\LogRecordRepository;

/**
 * Страница просмотра и фильтрации журнала событий DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class LogsPage extends AbstractPage {

	/**
	 * Формирует представление и данные страницы журнала с фильтрами и таблицей.
	 *
	 * @since 200.4.0
	 *
	 * @global string $dle_login_hash Хеш сессии DLE для AJAX-запросов таблицы.
	 *
	 * @return array{view: string, data: array<string, mixed>} Ключ шаблона и данные для Twig.
	 *
	 * @example
	 *     $result = (new LogsPage())->handle();
	 */
	public function handle(): array {
		$filterService = new FilterFormService();
		$query         = $filterService->parseRequestQuery();
		$schema        = $this->loadFilterSchema();
		$order         = FilterFormService::normalizeOrder((string) ($query['order'] ?? $schema->defaultOrder), $schema);
		$sort          = strtoupper((string) ($query['sort'] ?? 'DESC'));
		$perPage       = FilterFormService::resolveListCount();
		$rules         = $filterService->parseRules($query);

		/** @var LogRecordRepository $repository */
		$repository = Application::instance()->database()->repository(LogRecord::class);
		$criteria   = $filterService->rulesToCriteria($rules, $schema);
		$listResult = $repository->findFiltered(
			$criteria,
			max(1, (int) ($query['page'] ?? 1)),
			$perPage,
			$order,
			$sort,
			$schema->sortColumnKeys(),
			$schema->defaultOrder,
		);
		$catalog    = $filterService->buildCatalogViewModel($schema, $repository);
		$chips      = $filterService->buildChipViewModel($rules, $schema);
		$this->addBreadcrumb(__('Вывод логов'));

		global $dle_login_hash;

		return [
			'view' => 'admin/logs.twig',
			'data' => [
				'page_title'               => __('Вывод логов'),
				'order'                    => $order,
				'sort'                     => $sort,
				'total'                    => $listResult['total'],
				'per_page'                 => $perPage,
				'filter_rules'             => $rules,
				'filter_chips'             => $chips,
				'filter_catalog'           => $catalog,
				'query'                    => $query,
				'table_source_url'         => Paths::ajaxUrl('logs_table'),
				'table_initial_source_url' => $filterService->buildLogsTableAjaxUrl(
					$query,
					$dle_login_hash ?? '',
					$order,
					$sort,
				),
				'table_id'                 => 'dc-logs-table',
			],
		];
	}

	/**
	 * Загружает схему фильтра журнала из файла модуля Admin.
	 *
	 * @since 200.4.0
	 *
	 * @return FilterSchema Нормализованная схема фильтрации и сортировки.
	 */
	private function loadFilterSchema(): FilterSchema {
		$schemaFile = Paths::modules() . '/Admin/logs.filter.schema.php';

		/** Подключает схему фильтра журнала модуля Admin. */
		/** @var array<string, mixed> $raw */
		$raw = require DLEPlugins::Check($schemaFile);

		return FilterSchema::fromArray($raw);
	}

}
