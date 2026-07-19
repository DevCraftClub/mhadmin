<?php
//===============================================================
// Файл: LogsPage.php                                           =
// Путь: devcraft/src/modules/Admin/Pages/LogsPage.php          =
// ==============================================================

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Pages;

use DLEPlugins;
use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Types\FilterSchema;
use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Core\Admin\FilterFormService;
use DevCraft\Modules\Admin\Models\LogRecord;
use DevCraft\Modules\Admin\Services\LogMessagePresenter;
use DevCraft\Modules\Admin\Repositories\LogRecordRepository;

/**
 * Страница просмотра и фильтрации журнала событий DevCraft.
 */
final class LogsPage extends AbstractPage {

	public function handle(): array {
		$uuid = trim((string) ($_GET['uuid'] ?? ''));
		if($uuid !== '') {
			return $this->viewPage($uuid);
		}

		return $this->listPage();
	}

	/**
	 * @return array{view: string, data: array<string, mixed>}
	 */
	private function listPage(): array {
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
				'table_source_url'         => Paths::ajaxUrl('logs_table', 'admin', 'devcraft'),
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
	 * Readonly-деталь записи журнала по UUID.
	 *
	 * @return array{view: string, data: array<string, mixed>}
	 */
	private function viewPage(string $uuid): array {
		$this->addBreadcrumb(__('Вывод логов'), $this->buildBackUrl());
		$backUrl = $this->buildBackUrl();

		/** @var LogRecordRepository $repository */
		$repository = Application::instance()->database()->repository(LogRecord::class);
		$record     = $repository->findByUuid($uuid);

		if($record === NULL) {
			$this->addBreadcrumb(__('Запись не найдена'));

			return [
				'view' => 'admin/logs_view.twig',
				'data' => [
					'page_title' => __('Запись не найдена'),
					'error'      => __('Запись журнала не найдена или UUID указан неверно.'),
					'back_url'   => $backUrl,
				],
			];
		}

		$presenter    = new LogMessagePresenter();
		$presentation = $presenter->present($record->message);
		$pageTitle    = __('Запись #{id}', ['{id}' => (string) $record->id()]);
		$this->addBreadcrumb($pageTitle);

		return [
			'view' => 'admin/logs_view.twig',
			'data' => [
				'page_title'   => $pageTitle,
				'record'       => [
					'id'       => $record->id(),
					'log_type' => $record->log_type,
					'plugin'   => $record->plugin,
					'fn_name'  => $record->fn_name,
					'time'     => $record->time->format('Y-m-d H:i:s'),
					'uuid'     => $record->uuid?->toString() ?? '',
					'message'  => $record->message,
				],
				'presentation' => $presentation,
				'back_url'     => $backUrl,
			],
		];
	}

	private function buildBackUrl(): string {
		$params = $_GET;
		unset($params['uuid']);
		$params['mod']    = 'devcraft';
		$params['action'] = 'logs';

		return '?' . http_build_query($params);
	}

	private function loadFilterSchema(): FilterSchema {
		$schemaFile = Paths::modules() . '/Admin/Filter/logs.filter.schema.php';

		/** @var array<string, mixed> $raw */
		$raw = require DLEPlugins::Check($schemaFile);

		return FilterSchema::fromArray($raw);
	}

}
