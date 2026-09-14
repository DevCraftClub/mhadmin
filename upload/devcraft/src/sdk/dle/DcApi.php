<?php

declare(strict_types=1);

use DevCraft\Dle\Fluent\TableQuery;
use DevCraft\Dle\Schema\CommentsSchema;
use DevCraft\Dle\Schema\ConversationsSchema;
use DevCraft\Dle\Schema\FilesSchema;
use DevCraft\Dle\Schema\ImagesSchema;
use DevCraft\Dle\Schema\PluginsSchema;
use DevCraft\Dle\Schema\PostSchema;
use DevCraft\Dle\Schema\SchemaRegistry;
use DevCraft\Dle\Schema\SchemaTableNames;
use DevCraft\Dle\Schema\StaticSchema;
use DevCraft\Dle\Schema\TableSchemaInterface;
use DevCraft\Dle\Schema\UsersSchema;
use DevCraft\Dle\Sdk\Presenter\XfieldFieldBuilder;
use DevCraft\Dle\Sdk\Presenter\XfieldPresenter;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * Глобальная точка входа in-process SDK (Schema facades).
 */
final class DcApi {
	/**
	 * Новость (post) + nested extras/pass/cats через with*.
	 */
	public static function news(): PostSchema {
		return new PostSchema();
	}

	/**
	 * Пользователь (users).
	 */
	public static function user(): UsersSchema {
		return new UsersSchema();
	}

	/**
	 * Комментарий (comments).
	 */
	public static function comment(): CommentsSchema {
		return new CommentsSchema();
	}

	/**
	 * Переписка (conversations).
	 */
	public static function conversation(): ConversationsSchema {
		return new ConversationsSchema();
	}

	/**
	 * Плагин (plugins).
	 */
	public static function plugin(): PluginsSchema {
		return new PluginsSchema();
	}

	/**
	 * Файл вложений или изображение.
	 *
	 * @param 'files'|'images' $kind
	 */
	public static function file(
		#[ExpectedValues(values: ['files', 'images'])]
		string $kind = 'files',
	): FilesSchema|ImagesSchema {
		return $kind === 'images' ? new ImagesSchema() : new FilesSchema();
	}

	/**
	 * Статическая страница (static).
	 */
	public static function staticPage(): StaticSchema {
		return new StaticSchema();
	}

	/**
	 * Универсальный builder по имени таблицы SchemaRegistry.
	 */
	public static function schema(
		#[ExpectedValues(valuesFromClass: SchemaTableNames::class)]
		string $name,
	): TableSchemaInterface {
		return SchemaRegistry::make($name);
	}

	/**
	 * SELECT/фильтры по любой таблице SchemaRegistry.
	 */
	public static function query(
		#[ExpectedValues(valuesFromClass: SchemaTableNames::class)]
		string $table,
	): TableQuery {
		return TableQuery::of($table);
	}

	/**
	 * Fluent одного доп. поля каталога (post по умолчанию).
	 */
	public static function xfield(string $name, string $description = ''): XfieldFieldBuilder {
		return new XfieldFieldBuilder($name, $description);
	}

	/**
	 * Презентер каталога доп. полей (post|user).
	 *
	 * @param 'post'|'user' $scope
	 */
	public static function modifyXfield(
		#[ExpectedValues(values: ['post', 'user'])]
		string $scope = 'post',
	): XfieldPresenter {
		return new XfieldPresenter($scope);
	}
}
