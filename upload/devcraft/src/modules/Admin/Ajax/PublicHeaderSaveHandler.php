<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Application;
use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Core\Support\DataManager;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;
use RuntimeException;
use Throwable;

/** Сохранение meta-заголовка. */
final class PublicHeaderSaveHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$name    = trim((string) ($request->data['name'] ?? ''));
			$content = trim((string) ($request->data['content'] ?? ''));
			$id      = (int) ($request->data['id'] ?? 0);

			if($name === '' || $content === '') {
				throw new RuntimeException(__('Имя и содержимое обязательны'));
			}

			/** @var PublicHeaderEntryRepository $repo */
			$repo  = Application::instance()->database()->repository(PublicHeaderEntry::class);
			$entry = $id > 0 ? $repo->findByPK($id) : null;

			if($id > 0 && !$entry instanceof PublicHeaderEntry) {
				throw new RuntimeException(__('Запись не найдена'));
			}

			if($entry === null) {
				$entry             = new PublicHeaderEntry();
				$entry->origin     = 'manual';
				$entry->sort_order = $repo->maxSortOrder() + 1;
			}

			$entry->name    = $name;
			$entry->content = $content;
			$entry->active  = array_key_exists('active', $request->data)
				? !empty($request->data['active'])
				: ($entry->active ?? true);

			/** @var PublicHeaderEntry $saved */
			$saved = $repo->saveEntity($entry);

			return JsonResponse::toast(__('Сохранено'), ['id' => $saved->id()]);
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
