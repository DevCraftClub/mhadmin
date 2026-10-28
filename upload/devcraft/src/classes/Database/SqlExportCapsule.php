<?php
//===============================================================
// Файл: SqlExportCapsule.php                                   =
// Путь: devcraft/src/classes/Database/SqlExportCapsule.php     =
// Последнее изменение: 2026-10-28 12:50:00                     =
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

use Cycle\Database\DatabaseInterface;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\TableInterface;
use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\CapsuleException;
use Cycle\Migrations\Operation\Table\Drop as DropTable;
use Cycle\Migrations\OperationInterface;

/**
 * Капсула для выгрузки SQL: схема не читается из живой базы.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Core.Database
 */
final class SqlExportCapsule implements CapsuleInterface {

	/** @var array<string, AbstractTable> */
	private array $schemas = [];

	/**
	 * @since 200.4.2
	 *
	 * @param   DatabaseInterface  $database      База с драйвером записи SQL.
	 * @param   bool               $tableExists   `true` для `update`/`drop` (таблица «уже есть», но пустая).
	 */
	public function __construct(
		private readonly DatabaseInterface $database,
		private readonly bool              $tableExists = false,
	) {}

	public function getDatabase(): DatabaseInterface {
		return $this->database;
	}

	public function getTable(string $table): TableInterface {
		return $this->database->table($table);
	}

	public function getSchema(string $table): AbstractTable {
		if(!isset($this->schemas[$table])) {
			$schema = $this->database->table($table)->getSchema();

			if($this->tableExists) {
				$property = new \ReflectionProperty(AbstractTable::class, 'status');
				$property->setValue($schema, AbstractTable::STATUS_EXISTS);
			}

			$this->schemas[$table] = $schema;
		}

		return $this->schemas[$table];
	}

	public function execute(array $operations): void {
		foreach($operations as $operation) {
			if(!$operation instanceof OperationInterface) {
				throw new CapsuleException(__('Ожидалась операция миграции'));
			}

			if($operation instanceof DropTable) {
				$this->dropIndexes($operation->getTable());
			}

			$operation->execute($this);
		}
	}

	/**
	 * Перед DROP TABLE — отдельные DROP INDEX (Cycle сам их не пишет).
	 */
	private function dropIndexes(string $table): void {
		$schema  = $this->getSchema($table);
		$handler = $this->database->getDriver()->getSchemaHandler();

		foreach(array_reverse($schema->getIndexes()) as $index) {
			$handler->dropIndex($schema, $index);
		}
	}

}
