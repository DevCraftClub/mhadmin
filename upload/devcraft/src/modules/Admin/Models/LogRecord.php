<?php
//===============================================================
// Файл: LogRecord.php                                          =
// Путь: devcraft/src/modules/Admin/Models/LogRecord.php        =
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

namespace DevCraft\Modules\Admin\Models;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Uuid\Uuid1;
use DevCraft\Core\Abstracts\AbstractEntity;
use Cycle\Annotated\Annotation\Table\Index;
use DevCraft\Modules\Admin\Repositories\LogRecordRepository;

/**
 * Сущность записи журнала DevCraft в таблице `devcraft_logs`.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
#[Entity(role: 'log_record', repository: LogRecordRepository::class, table: 'devcraft_logs')]
#[Uuid1(field: 'uuid', node: '00000fffffff', clockSeq: 0xffff, nullable: false)]
#[Index(columns: ['uuid'], unique: true)]
class LogRecord extends AbstractEntity {

	/**
	 * UUID записи журнала (генерируется ORM-поведением Uuid1).
	 *
	 * @since 200.4.0
	 * @var UuidInterface|null
	 */
	#[Column(type: 'uuid')]
	public ?UuidInterface $uuid = NULL;

	/**
	 * Тип события журнала (error, info, debug и т.д.).
	 *
	 * @since 200.4.0
	 * @var string
	 */
	#[Column(type: 'string')]
	public string $log_type = '';

	/**
	 * Код плагина или модуля-источника записи.
	 *
	 * @since 200.4.0
	 * @var string
	 */
	#[Column(type: 'string')]
	public string $plugin = '';

	/**
	 * Имя функции или метода, породившего запись.
	 *
	 * @since 200.4.0
	 * @var string
	 */
	#[Column(type: 'string')]
	public string $fn_name = '';

	/**
	 * Время создания записи журнала.
	 *
	 * @since 200.4.0
	 * @var DateTimeImmutable
	 */
	#[Column(type: 'datetime')]
	public DateTimeImmutable $time;

	/**
	 * Текст сообщения журнала.
	 *
	 * @since 200.4.0
	 * @var string
	 */
	#[Column(type: 'text')]
	public string $message = '';

	/**
	 * Инициализирует запись с текущим временем по умолчанию.
	 *
	 * @since 200.4.0
	 *
	 * @example
	 *     $record = new LogRecord();
	 */
	public function __construct() {
		$this->time = new DateTimeImmutable();
	}

	/**
	 * Возвращает значение колонки по логическому имени для таблицы и фильтров.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $name  Имя колонки (`id`, `log_type`, `plugin`, `fn_name`, `time`, `message`).
	 *
	 * @return mixed Скалярное значение колонки или `null` для неизвестного имени.
	 *
	 * @example
	 *     $type = $record->getColumnVal('log_type');
	 */
	public function getColumnVal(string $name): mixed {
		return match ($name) {
			'id'               => $this->id(),
			'log_type', 'type' => $this->log_type,
			'plugin'           => $this->plugin,
			'fn_name', 'fn'    => $this->fn_name,
			'time'             => $this->time->format('Y-m-d H:i:s'),
			'message'          => $this->message,
			default            => NULL,
		};
	}

}
