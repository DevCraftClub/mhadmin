<?php
//===============================================================
// Файл: SqlCaptureDriver.php                                   =
// Путь: devcraft/src/classes/Database/SqlCaptureDriver.php     =
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

namespace DevCraft\Core\Database;

use Cycle\Database\Config\DriverConfig;
use Cycle\Database\Driver\Driver;
use Cycle\Database\Driver\MySQL\MySQLDriver;
use Cycle\Database\Query\Interpolator;

/**
 * Драйвер MySQL, который читает базу как обычно, а изменяющие команды только записывает.
 *
 * Нужен, чтобы показать SQL миграции, не меняя базу (DDL в MySQL нельзя откатить транзакцией).
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Core.Database
 */
final class SqlCaptureDriver extends MySQLDriver {

	/** @var list<string> */
	private array $statements = [];

	/**
	 * Создаёт драйвер с тем же подключением, что у рабочего.
	 *
	 * @since 200.4.2
	 *
	 * @param   Driver  $driver  Рабочий драйвер Cycle.
	 *
	 * @return self Драйвер записи SQL.
	 */
	public static function fromDriver(Driver $driver): self {
		// ponytail: Cycle не отдаёт DriverConfig наружу (поле protected). Когда появится getter — убрать Reflection.
		$config = (new \ReflectionProperty(Driver::class, 'config'))->getValue($driver);

		if(!$config instanceof DriverConfig) {
			throw new \RuntimeException(__('Не удалось прочитать конфигурацию драйвера'));
		}

		return self::create($config);
	}

	/**
	 * Записывает команду вместо выполнения.
	 *
	 * @since 200.4.2
	 */
	public function execute(string $query, array $parameters = []): int {
		$this->statements[] = $parameters === []? $query : Interpolator::interpolate($query, $parameters);

		return 0;
	}

	/**
	 * Возвращает записанные команды и очищает список.
	 *
	 * @since 200.4.2
	 *
	 * @return list<string>
	 */
	public function takeStatements(): array {
		$statements       = $this->statements;
		$this->statements = [];

		return $statements;
	}

}
