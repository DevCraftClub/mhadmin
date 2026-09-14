<?php

declare(strict_types=1);

/**
 * Bootstrap in-process DLE SDK: Schema / Fluent / Xfield / DcApi.
 *
 * Автозагрузка `DevCraft\Dle\*`, `DcApi` и функций `dle_api_*` штатно приходит из composer
 * DevCraft Admin (`devcraft/vendor/autoload.php`). Этот файл нужен там, где autoload
 * ещё не собран (свежая установка без `composer dump-autoload`), для глобальных
 * шимов `prepare()` / `query()` и для BC-алиасов прежнего namespace `DleApi\*`.
 *
 * require_once DLEPlugins::Check(DC_ROOT . '/src/sdk/dle/bootstrap.php');
 *
 * @since 200.4.1 SDK живёт в Admin под `DevCraft\Dle\*` (прежде `DleApi\*` в пакете DLE API).
 */

use DevCraft\Dle\Fluent\TableBuilder;
use DevCraft\Dle\Fluent\TableQuery;
use function DevCraft\Dle\Fluent\prepare as _prepare;
use function DevCraft\Dle\Fluent\query as _query;

$sdk_root = __DIR__;

/**
 * Подключает файл SDK через DLEPlugins, если тот доступен.
 */
$sdk_require = static function (string $file): void {
	require_once (class_exists('DLEPlugins', false) ? DLEPlugins::Check($file) : $file);
};

// Fallback-автозагрузчик: composer Admin ещё не сгенерирован или устарел.
if(!class_exists(\DevCraft\Dle\Schema\SchemaRegistry::class)) {
	spl_autoload_register(static function (string $class) use ($sdk_root, $sdk_require): void {
		if(!str_starts_with($class, 'DevCraft\\Dle\\')) {
			return;
		}
		$file = $sdk_root . '/' . str_replace('\\', '/', substr($class, strlen('DevCraft\\Dle\\'))) . '.php';
		if(is_file($file)) {
			$sdk_require($file);
		}
	});
}

/*
 * BC-алиасы прежнего namespace: `DleApi\Schema\PostSchema` и т. п. продолжают
 * работать как алиасы `DevCraft\Dle\*`. Регистрируется последним, чтобы не
 * перехватывать живые классы пакета DLE API (`DleApi\Http\V2\*`, `DleApi\OpenApi\*`).
 *
 * @deprecated 200.4.1 Использовать `DevCraft\Dle\*`; алиасы уйдут в следующем мажоре.
 */
spl_autoload_register(static function (string $class): void {
	if(!str_starts_with($class, 'DleApi\\')) {
		return;
	}
	$tail = substr($class, strlen('DleApi\\'));
	if(!preg_match('/^(Schema|Fluent|Xfield|Sdk)(\\\\|$)/', $tail)) {
		return;
	}
	$target = 'DevCraft\\Dle\\' . $tail;
	if(class_exists($target) || interface_exists($target) || trait_exists($target)) {
		class_alias($target, $class);
	}
});

if(!function_exists('dle_api_db')) {
	$sdk_require($sdk_root . '/Fluent/DcDatabase.php');
}

if(!function_exists('DevCraft\Dle\Fluent\prepare')) {
	$sdk_require($sdk_root . '/Fluent/functions.php');
}

if(!class_exists('DcApi', false)) {
	$sdk_require($sdk_root . '/DcApi.php');
}

if(!function_exists('prepare')) {
	/**
	 * Глобальный шим: INSERT/UPDATE builder по таблице DLE.
	 */
	function prepare(string $table): TableBuilder {
		return _prepare($table);
	}
}

if(!function_exists('query')) {
	/**
	 * Глобальный шим: SELECT builder по таблице DLE.
	 */
	function query(string $table): TableQuery {
		return _query($table);
	}
}
