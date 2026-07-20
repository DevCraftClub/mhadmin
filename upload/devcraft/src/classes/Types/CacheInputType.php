<?php
//===============================================================
// Файл: CacheInputType.php                                     =
// Путь: devcraft/src/classes/Types/CacheInputType.php          =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Types;

use DevCraft\Core\Abstracts\AbstractType;

/**
 * Обёртка для сериализации данных файлового кэша DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Types
 */
final class CacheInputType extends AbstractType {

	/**
	 * @since 200.4.0
	 *
	 * @param   mixed  $cacheData  Полезная нагрузка кэша.
	 * @param   int    $storedAt   Unix-время записи.
	 */
	public function __construct(
		public mixed $cacheData,
		public int   $storedAt = 0,
	) {}

	/**
	 * Создаёт обёртку с текущим временем записи.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $data  Полезная нагрузка кэша.
	 */
	public static function wrap(mixed $data): self {
		return new self($data, time());
	}

	/**
	 * @inheritDoc
	 *
	 * @param   array<string, mixed>  $data
	 */
	public static function fromArray(array $data): static {
		return new self(
			$data['cacheData'] ?? NULL,
			(int) ($data['storedAt'] ?? 0),
		);
	}

	/**
	 * @inheritDoc
	 *
	 * @return array{cacheData: mixed, storedAt: int}
	 */
	public function toArray(): array {
		return [
			'cacheData' => $this->cacheData,
			'storedAt'  => $this->storedAt,
		];
	}

}
