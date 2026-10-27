<?php
//===============================================================
// Файл: ExceptionDetailFormatter.php                           =
// Путь: devcraft/src/classes/Support/ExceptionDetailFormatter.php
// Последнее изменение: 2026-10-27 16:10:00                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Support;

use Cycle\ORM\SchemaInterface;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Database\EntityPathResolver;
use DevCraft\Core\Module\Registry;
use Throwable;

/**
 * Собирает текст для страницы 500 и AJAX: цепочка исключений и подсказки по схеме Cycle.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Core.Support
 */
final class ExceptionDetailFormatter {

	/**
	 * @param   Registry|null  $registry  Реестр модулей для списка каталогов Models.
	 */
	public function __construct(
		private readonly ?Registry $registry = null,
	) {}

	/**
	 * Полный текст для блока «Описание ошибки» на 500.
	 */
	public function format(Throwable $exception): string {
		$parts = [$this->formatChain($exception)];

		$schemaHint = $this->formatUndefinedSchemaHint($exception);

		if($schemaHint !== '') {
			$parts[] = $schemaHint;
		}

		$parts[] = $this->formatTrace($exception);

		return implode("\n\n", $parts);
	}

	/**
	 * Краткая строка для JSON (без длинного стека).
	 */
	public function formatBrief(Throwable $exception): string {
		$parts = [$this->formatChain($exception, 3)];

		$schemaHint = $this->formatUndefinedSchemaHint($exception);

		if($schemaHint !== '') {
			$parts[] = $schemaHint;
		}

		return implode("\n\n", $parts);
	}

	/**
	 * @param   int  $maxDepth  Сколько звеньев цепочки previous показать.
	 */
	private function formatChain(Throwable $exception, int $maxDepth = 8): string {
		$lines = [];
		$current = $exception;
		$index = 0;

		while($current !== null && $index < $maxDepth) {
			$prefix = $index === 0 ? 'Исключение' : 'Причина #' . $index;
			$file = $current->getFile();
			$line = $current->getLine();
			$lines[] = sprintf(
				'%s: %s — %s (%s:%d)',
				$prefix,
				$current::class,
				$current->getMessage(),
				$file,
				$line,
			);
			$current = $current->getPrevious();
			$index++;
		}

		return implode("\n", $lines);
	}

	private function formatTrace(Throwable $exception): string {
		$frames = [];

		foreach(array_slice($exception->getTrace(), 0, 12) as $i => $frame) {
			$file = (string) ($frame['file'] ?? '?');
			$line = (int) ($frame['line'] ?? 0);
			$call = '';

			if(isset($frame['class'], $frame['type'], $frame['function'])) {
				$call = $frame['class'] . $frame['type'] . $frame['function'] . '()';
			} elseif(isset($frame['function'])) {
				$call = $frame['function'] . '()';
			}

			$frames[] = sprintf('#%d %s:%d %s', $i, $file, $line, $call);
		}

		if($frames === []) {
			return 'Стек вызовов: (пусто)';
		}

		return "Стек вызовов (первые кадры):\n" . implode("\n", $frames);
	}

	private function formatUndefinedSchemaHint(Throwable $exception): string {
		$missing = $this->extractMissingSchemaName($exception);

		if($missing === null) {
			return '';
		}

		$lines = [
			'Подсказка по схеме Cycle:',
			'— Искали роль или класс: ' . $missing,
		];

		$classExists = class_exists($missing, true);
		$lines[] = '— class_exists: ' . ($classExists ? 'да' : 'нет');

		if(str_contains($missing, '\\')) {
			$file = $this->guessClassFile($missing);
			$lines[] = '— Файл класса: ' . ($file === null ? 'не найден' : ($file . (is_file($file) ? ' (есть)' : ' (нет на диске)')));
		}

		$roles = $this->rolesFromCachedSchema();
		$lines[] = '— Ролей в сохранённой схеме: ' . (string) count($roles);

		if($roles !== []) {
			$sample = array_slice($roles, 0, 40);
			$lines[] = '— Примеры ролей: ' . implode(', ', $sample);
			$matched = $this->findRoleForClass($missing, $roles);

			if($matched !== null) {
				$lines[] = '— В схеме есть роль с этим классом: ' . $matched . ' (псевдоним класса мог не создаться — класс не загрузился при сборке ORM)';
			} else {
				$lines[] = '— В сохранённой схеме этого класса/роли нет: модели, скорее всего, не попали в обход ClassLocator или каталог Models не был в списке.';
			}
		} else {
			$lines[] = '— Сохранённой схемы нет или она пуста (devcraft/cache/cycle_orm_schema.ser).';
		}

		$dirs = $this->modelDirectories();
		$lines[] = '— Каталоги Models: ' . ($dirs === [] ? '(пусто)' : implode(' | ', $dirs));

		return implode("\n", $lines);
	}

	private function extractMissingSchemaName(Throwable $exception): ?string {
		$current = $exception;

		while($current !== null) {
			if(preg_match('/Undefined schema `([^`]+)`, not found/', $current->getMessage(), $match) === 1) {
				return $match[1];
			}

			$current = $current->getPrevious();
		}

		return null;
	}

	private function guessClassFile(string $class): ?string {
		if(!str_starts_with($class, 'DevCraft\\')) {
			return null;
		}

		$src = rtrim(Paths::src(), '/\\') . DIRECTORY_SEPARATOR;

		$map = [
			'DevCraft\\Core\\'    => 'classes/',
			'DevCraft\\Modules\\' => 'modules/',
		];

		foreach($map as $prefix => $base) {
			if(!str_starts_with($class, $prefix)) {
				continue;
			}

			$relative = substr($class, strlen($prefix));

			return $src . $base . str_replace('\\', '/', $relative) . '.php';
		}

		return null;
	}

	/**
	 * @return list<string>
	 */
	private function rolesFromCachedSchema(): array {
		$path = rtrim(Paths::cache(), '/\\') . '/cycle_orm_schema.ser';

		if(!is_file($path)) {
			return [];
		}

		$raw = @file_get_contents($path);

		if($raw === false || $raw === '') {
			return [];
		}

		/** @var mixed $payload */
		$payload = @unserialize($raw);

		if(!is_array($payload) || !is_array($payload['schema'] ?? null)) {
			return [];
		}

		/** @var array<string, mixed> $schema */
		$schema = $payload['schema'];
		$roles = [];

		foreach($schema as $role => $def) {
			if(is_string($role) && $role !== '' && is_array($def)) {
				$roles[] = $role;
			}
		}

		sort($roles);

		return $roles;
	}

	/**
	 * @param   list<string>  $roles
	 */
	private function findRoleForClass(string $class, array $roles): ?string {
		$path = rtrim(Paths::cache(), '/\\') . '/cycle_orm_schema.ser';

		if(!is_file($path)) {
			return null;
		}

		$raw = @file_get_contents($path);

		if($raw === false) {
			return null;
		}

		/** @var mixed $payload */
		$payload = @unserialize($raw);

		if(!is_array($payload) || !is_array($payload['schema'] ?? null)) {
			return null;
		}

		/** @var array<string, mixed> $schema */
		$schema = $payload['schema'];

		foreach($schema as $role => $def) {
			if(!is_array($def) || !is_string($role)) {
				continue;
			}

			$entity = $def[SchemaInterface::ENTITY] ?? null;

			if($entity === $class || $role === $class) {
				return $role;
			}
		}

		return in_array($class, $roles, true) ? $class : null;
	}

	/**
	 * @return list<string>
	 */
	private function modelDirectories(): array {
		if($this->registry === null) {
			return [];
		}

		try {
			return (new EntityPathResolver($this->registry))->entityModelDirectories();
		} catch(Throwable) {
			return [];
		}
	}

}
