<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DLEPlugins;
use DevCraft\Types\FilterSchema;
use DevCraft\Core\Application;
use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Admin\FilterFormService;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Composer\Models\ComposerData;
use DevCraft\Core\Composer\Repositories\ComposerDataRepository;
use DevCraft\Core\Composer\ComposerDbSyncService;

final class ComposerTableHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): JsonResponse {
		try {
			$filterService = new FilterFormService();
			$query         = $filterService->parseRequestQuery();
			$schema        = $this->loadFilterSchema();
			$order         = FilterFormService::normalizeOrder((string) ($query['order'] ?? $schema->defaultOrder), $schema);
			$allowedSortColumns = $schema->sortColumnKeys();
			if(!in_array($order, $allowedSortColumns, true)) {
				$order = in_array('package', $allowedSortColumns, true) ? 'package' : $schema->defaultOrder;
			}
			$sort          = strtoupper((string) ($query['sort'] ?? 'ASC'));
			$rules         = $filterService->parseRules($query);
			$criteria      = $filterService->rulesToCriteria($rules, $schema);
			$perPage       = max(1, FilterFormService::resolveListCount());

			/** @var ComposerDataRepository $repository */
			$repository = Application::instance()->database()->repository(ComposerData::class);
			$listResult = $repository->findFiltered(
				$criteria,
				max(1, (int) ($query['page'] ?? 1)),
				$perPage,
				$order,
				$sort,
				$allowedSortColumns,
				$schema->defaultOrder,
			);

			// Если таблица пуста, выполняем мягкий автосинк из composer.json и повторяем выборку.
			if((int) ($listResult['total'] ?? 0) === 0) {
				(new ComposerDbSyncService())->syncFromRuntimeSnapshot();
				$listResult = $repository->findFiltered(
					$criteria,
					max(1, (int) ($query['page'] ?? 1)),
					$perPage,
					$order,
					$sort,
					$allowedSortColumns,
					$schema->defaultOrder,
				);
			}

			$rows = [];
			foreach($listResult['items'] as $item) {
				if(!$item instanceof ComposerData) {
					continue;
				}

				$rows[] = [
					$item->package,
					$item->version,
					$item->plugin,
					$item->required ? __('Да') : __('Нет'),
					$item->installed ? __('Да') : __('Нет'),
					$item->appCode,
					$this->actionsCell($item),
				];
			}

			return new JsonResponse([
				'header' => [
					['name' => 'package', 'title' => __('Пакет'), 'sortable' => true],
					['name' => 'version', 'title' => __('Версия'), 'sortable' => true],
					['name' => 'plugin', 'title' => __('Модуль'), 'sortable' => true],
					['name' => 'required', 'title' => __('Обязательный'), 'sortable' => true],
					['name' => 'installed', 'title' => __('Установлен'), 'sortable' => true],
					['name' => 'appCode', 'title' => __('Код приложения'), 'sortable' => true],
					['name' => 'actions', 'title' => '', 'sortable' => false],
				],
				'data'   => $rows,
				'total'  => $listResult['total'],
			]);
		} catch(\Throwable $e) {
			return new JsonResponse([
				'header' => [
					['name' => 'package', 'title' => __('Пакет'), 'sortable' => true],
					['name' => 'version', 'title' => __('Версия'), 'sortable' => true],
					['name' => 'plugin', 'title' => __('Модуль'), 'sortable' => true],
					['name' => 'required', 'title' => __('Обязательный'), 'sortable' => true],
					['name' => 'installed', 'title' => __('Установлен'), 'sortable' => true],
					['name' => 'appCode', 'title' => __('Код приложения'), 'sortable' => true],
					['name' => 'actions', 'title' => '', 'sortable' => false],
				],
				'data'   => [[
					'—',
					'—',
					'—',
					'—',
					'—',
					'—',
					'<span class="fg-red">' . htmlspecialchars(__('Ошибка загрузки: {msg}', ['{msg}' => $e->getMessage()]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</span>',
				]],
				'total'  => 1,
				'error'  => [
					'code'    => 'composer_table_failed',
					'message' => __('Не удалось загрузить таблицу Composer. Проверьте миграции базы данных.'),
					'detail'  => $e->getMessage(),
				],
			], 200);
		}
	}

	private function actionsCell(ComposerData $item): string {
		$safeName    = htmlspecialchars($item->package, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$safeVersion = htmlspecialchars((string) $item->version, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

		if(!$item->installed) {
			return '<div class="d-flex flex-wrap gap-1">'
				. '<button type="button" class="button small primary js-composer-action" data-action-type="install" data-package="' . $safeName . '" data-version="' . $safeVersion . '">' . __('Установить') . '</button>'
				. '</div>';
		}

		return '<div class="d-flex flex-wrap gap-1">'
			. '<button type="button" class="button small success js-composer-action" data-action-type="update" data-package="' . $safeName . '">' . __('Обновить') . '</button>'
			. '<button type="button" class="button small alert js-composer-action" data-action-type="remove" data-package="' . $safeName . '">' . __('Удалить') . '</button>'
			. '</div>';
	}

	private function loadFilterSchema(): FilterSchema {
		$schemaFile = DEVCRAFT_MODULES . '/Admin/composer.filter.schema.php';
		/** @var array<string, mixed> $raw */
		$raw = require DLEPlugins::Check($schemaFile);

		return FilterSchema::fromArray($raw);
	}
}
