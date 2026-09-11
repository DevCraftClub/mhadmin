<?php

declare(strict_types=1);

/**
 * ponytail: без БД — проверка equality-моста TableQuery ↔ QueryBuilder и префиксов таблиц.
 * Запуск: php devcraft/src/sdk/dle/Fluent/query_bridge.selfcheck.php
 */

/** Префиксы DLE: вне сайта задаём различимые значения, чтобы поймать подмену USERPREFIX/PREFIX. */
defined('PREFIX') || define('PREFIX', 'dle');
defined('USERPREFIX') || define('USERPREFIX', 'shared');

use DevCraft\Builders\QueryBuilder;
use DevCraft\Dle\Fluent\TableQuery;
use DevCraft\Dle\Sdk\SdkException;

/** Шим перевода: selfcheck работает без boot DevCraft (нет конфига и БД). */
function __(string $phrase, array $params = [], int $count = 0): string {
	return $params === [] ? $phrase : strtr($phrase, $params);
}

require_once dirname(__DIR__, 4) . '/vendor/autoload.php';

// TableQuery → QueryBuilder: колонки, равенства, сортировка, limit/offset.
$args = TableQuery::of('post')
	->select(['id', 'title'])
	->where('approve', '1')
	->orderBy('date', 'ASC')
	->limit(10)
	->offset(5)
	->toQueryBuilder()
	->build();

assert($args['table'] === 'post');
assert($args['columns'] === ['id', 'title']);
assert($args['conditions'] === ['approve' => '1']);
assert($args['order'] === ['date' => 'ASC']);
assert($args['limit'] === 10);
assert($args['offset'] === 5);

// Неизвестные колонки отбрасываются схемой и не попадают в билдер.
$filtered = TableQuery::of('post')->select(['id', 'no_such_column'])->toQueryBuilder()->build();
assert($filtered['columns'] === ['id']);

// LIKE / отрицание / xfields через QueryBuilder не выражаются.
$blocked = static function (callable $build): bool {
	try {
		$build()->toQueryBuilder();

		return false;
	} catch(SdkException $e) {
		return $e->errorCode() === 'query_bridge_unsupported';
	}
};

assert($blocked(static fn() => TableQuery::of('post')->where('title', '%новость')) === true);
assert($blocked(static fn() => TableQuery::of('post')->where('approve', '!1')) === true);
assert($blocked(static fn() => TableQuery::of('post')->whereXfield('director', 'Nolan')) === true);
assert($blocked(static fn() => TableQuery::of('post')->where('category', '5')) === true);

// QueryBuilder → TableQuery: значение со спецсимволом остаётся равенством.
$compiled = TableQuery::fromQueryBuilder(
	QueryBuilder::create('post')
		->withColumns(['id'])
		->withConditionsItem('title', '%100')
		->withOrderItem('id', 'DESC')
		->withLimit(3),
)->compileSelect();

assert(str_contains($compiled['sql'], '`title` = ?'));
assert(!str_contains($compiled['sql'], 'LIKE'));
assert($compiled['params'] === ['%100']);
assert(str_contains($compiled['sql'], 'LIMIT 3'));

// Составные условия и чужие колонки отклоняются.
$rejected = static function (QueryBuilder $builder): bool {
	try {
		TableQuery::fromQueryBuilder($builder);

		return false;
	} catch(SdkException) {
		return true;
	}
};

assert($rejected(QueryBuilder::create('post')->withConditionsItem('id', ['op' => '>', 'value' => 1])) === true);
assert($rejected(QueryBuilder::create('post')->withConditionsItem('no_such_column', '1')) === true);

// Таблицы модулей (Cycle) в SchemaRegistry отсутствуют.
try {
	TableQuery::fromQueryBuilder(QueryBuilder::create('api_keys'));
	assert(false);
} catch(InvalidArgumentException) {
}

// Обратный проход: TableQuery → QueryBuilder → TableQuery сохраняет SQL.
$original = TableQuery::of('users')->where('user_id', '1')->limit(1)->compileSelect();
$roundtrip = TableQuery::fromQueryBuilder(
	TableQuery::of('users')->where('user_id', '1')->limit(1)->toQueryBuilder(),
)->compileSelect();

assert($original === $roundtrip);

// Префиксы: пользовательские таблицы DLE живут под USERPREFIX, остальные под PREFIX.
assert(dle_api_table('post') === PREFIX . '_post');
assert(dle_api_table('category') === PREFIX . '_category');
assert(dle_api_table('poll') === PREFIX . '_poll');
assert(dle_api_table('users') === USERPREFIX . '_users');
assert(dle_api_table('admin_logs') === USERPREFIX . '_admin_logs');
assert(dle_api_table('conversations_messages') === USERPREFIX . '_conversations_messages');

fwrite(STDOUT, "query_bridge.selfcheck: ok\n");
