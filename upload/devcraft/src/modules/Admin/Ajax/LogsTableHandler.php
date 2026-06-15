<?php
//===============================================================
// Файл: LogsTableHandler.php                                   =
// Путь: devcraft/src/modules/Admin/Ajax/LogsTableHandler.php   =
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

namespace DevCraft\Modules\Admin\Ajax;

use DLEPlugins;
use DevCraft\Core\Application;
use DevCraft\Types\FilterSchema;
use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Admin\FilterFormService;
use DevCraft\Modules\Admin\Models\LogRecord;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Modules\Admin\Repositories\LogRecordRepository;

/**
 * AJAX-обработчик загрузки строк таблицы журнала с фильтрацией и сортировкой.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class LogsTableHandler implements AjaxHandlerInterface {

	/**
	 * Максимальное число строк, возвращаемых одним AJAX-запросом таблицы.
	 *
	 * @since 200.4.0
	 * @var int
	 */
	private const TABLE_ROW_LIMIT = 10000;

	/**
	 * Возвращает заголовок и строки таблицы журнала по текущим фильтрам.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  AJAX-запрос с параметрами фильтра и сортировки.
	 *
	 * @return JsonResponse JSON-ответ с колонками, строками и общим количеством.
	 *
	 * @example
	 *     $response = (new LogsTableHandler())->handle($request);
	 */
	public function handle(AjaxRequest $request): JsonResponse {
		$filterService = new FilterFormService();
		$query         = $filterService->parseRequestQuery();
		$schema        = $this->loadFilterSchema();
		$order         = FilterFormService::normalizeOrder((string) ($query['order'] ?? $schema->defaultOrder), $schema);
		$sort          = strtoupper((string) ($query['sort'] ?? 'DESC'));

		$rules = $filterService->parseRules($query);

		/** @var LogRecordRepository $repository */
		$repository = Application::instance()->database()->repository(LogRecord::class);
		$criteria   = $filterService->rulesToCriteria($rules, $schema);
		$result     = $repository->findFiltered(
			$criteria,
			1,
			self::TABLE_ROW_LIMIT,
			$order,
			$sort,
			$schema->sortColumnKeys(),
			$schema->defaultOrder,
		);

		$rows = [];

		foreach($result['items'] as $record) {
			if(!$record instanceof LogRecord) {
				continue;
			}

			$uuid      = $record->uuid?->toString() ?? '';
			$message   = htmlspecialchars($record->message, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
			$deleteBtn = '<button type="button" class="button small alert js-delete-log" data-uuid="'
			             . htmlspecialchars($uuid, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8')
			             . '">' . __('Удалить') . '</button>';

			$rows[] = [
				$record->id(),
				$record->log_type,
				$record->plugin,
				$record->fn_name,
				$record->time->format('Y-m-d H:i:s'),
				'<div class="text-ellipsis" style="max-width: 420px;" title="' . $message . '">' . $message . '</div>',
				$deleteBtn,
				$uuid,
			];
		}

		return new JsonResponse([
			'header' => $this->buildTableHeader($schema, $order, $sort),
			'data'   => $rows,
			'total'  => $result['total'],
		]);
	}

	/**
	 * Формирует описание колонок таблицы журнала для Metro UI DataTable.
	 *
	 * @since 200.4.0
	 *
	 * @param   FilterSchema  $schema  Схема фильтра с подписями колонок.
	 * @param   string        $order   Имя колонки активной сортировки.
	 * @param   string        $sort    Направление сортировки (`ASC` или `DESC`).
	 *
	 * @return list<array<string, mixed>> Массив описаний колонок таблицы.
	 */
	private function buildTableHeader(FilterSchema $schema, string $order, string $sort): array {
		$labels  = $schema->resolvedSortColumns();
		$sortDir = strtolower($sort) === 'asc'? 'asc' : 'desc';
		$columns = [
			['name' => 'id', 'title' => $labels['id'] ?? '#', 'size' => 80, 'sortable' => true],
			['name' => 'log_type', 'title' => $labels['log_type'] ?? __('Тип'), 'sortable' => true],
			['name' => 'plugin', 'title' => $labels['plugin'] ?? __('Плагин'), 'sortable' => true],
			['name' => 'fn_name', 'title' => $labels['fn_name'] ?? __('Функция'), 'sortable' => true],
			['name' => 'time', 'title' => $labels['time'] ?? __('Время'), 'size' => 160, 'sortable' => true],
			['name' => 'message', 'title' => $labels['message'] ?? __('Сообщение'), 'sortable' => true],
			['name' => 'actions', 'title' => '', 'size' => 100, 'sortable' => false],
			['name' => 'uuid', 'title' => '', 'show' => false, 'sortable' => false],
		];

		foreach($columns as &$column) {
			if(($column['name'] ?? '') === $order && ($column['sortable'] ?? false)) {
				$column['sortDir'] = $sortDir;
			}
		}
		unset($column);

		return $columns;
	}

	/**
	 * Загружает схему фильтра журнала из файла модуля Admin.
	 *
	 * @since 200.4.0
	 *
	 * @return FilterSchema Нормализованная схема фильтрации и сортировки.
	 */
	private function loadFilterSchema(): FilterSchema {
		$schemaFile = DEVCRAFT_MODULES . '/Admin/logs.filter.schema.php';

		/** Подключает схему фильтра журнала модуля Admin. */
		/** @var array<string, mixed> $raw */
		$raw = require DLEPlugins::Check($schemaFile);

		return FilterSchema::fromArray($raw);
	}

}
