<?php
//===============================================================
// Файл: LogsZipExportService.php                               =
// Путь: devcraft/src/modules/Admin/Services/LogsZipExportServi…=
// Последнее изменение: 2026-10-28 12:00:00                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use Cycle\Database\Injection\Parameter;
use DevCraft\Modules\Admin\Models\LogRecord;
use DevCraft\Modules\Admin\Repositories\LogRecordRepository;

/**
 * Собирает выбранные записи журнала в ZIP: `{plugin}/{fn_name}/{log_type}.log`.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Modules.Admin
 */
final class LogsZipExportService {

	private const SEPARATOR = '==============================';

	/**
	 * @since 200.4.2
	 *
	 * @param   LogRecordRepository  $repository  Репозиторий журнала.
	 */
	public function __construct(
		private readonly LogRecordRepository $repository,
	) {}

	/**
	 * Создаёт ZIP с выбранными записями.
	 *
	 * @since 200.4.2
	 *
	 * @param   list<int>  $ids  Номера записей журнала.
	 *
	 * @return string Абсолютный путь к временному ZIP.
	 *
	 * @throws \RuntimeException Если нет ZipArchive, записи не найдены или архив не записан.
	 */
	public function export(array $ids): string {
		if(!class_exists(\ZipArchive::class)) {
			throw new \RuntimeException(__('На сервере нет расширения PHP zip'));
		}

		/** @var list<LogRecord> $records */
		$records = $this->repository->select()->where('id', 'in', new Parameter($ids))->orderBy('id')->fetchAll();

		if($records === []) {
			throw new \RuntimeException(__('Выбранные записи не найдены'));
		}

		$files = [];

		foreach($records as $record) {
			$path           = $this->segment($record->plugin) . '/' . $this->segment($record->fn_name) . '/' . $this->segment($record->log_type) . '.log';
			$files[$path][] = self::SEPARATOR . "\n"
			                  . '#' . $record->id() . ":\t" . $record->time->format('Y-m-d H:i:s') . "\n"
			                  . self::SEPARATOR . "\n"
			                  . $record->message;
		}

		$zipPath = TempDownload::path('zip');
		$zip     = new \ZipArchive();

		if($zip->open($zipPath, \ZipArchive::CREATE|\ZipArchive::OVERWRITE) !== true) {
			throw new \RuntimeException(__('Не удалось создать архив'));
		}

		foreach($files as $path => $entries) {
			$zip->addFromString($path, implode("\n\n", $entries) . "\n");
		}

		if(!$zip->close()) {
			throw new \RuntimeException(__('Не удалось записать архив'));
		}

		return $zipPath;
	}

	/**
	 * Безопасная часть пути внутри архива.
	 */
	private function segment(?string $value): string {
		$value = trim(str_replace(['/', '\\', "\0"], '_', (string) $value), " .");

		return $value === ''? '_' : $value;
	}

}
