<?php
//===============================================================
// Файл: ParseTemplateTags.php                                  =
// Путь: devcraft/src/classes/Support/ParseTemplateTags.php     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Support;

use DLEPlugins;

/**
 * Парсинг тегов шаблонов новости DLE (short/full) через dle_template.
 *
 * Эталон: engine/modules/show.short.php, show.full.php, show.custom.php.
 *
 * @package    DevCraft
 * @since      200.5.0
 * @subpackage Core.Support
 */
final class ParseTemplateTags {

	/**
	 * URL полной новости — как в show.full.php через DLEUrl::BuildUrl('showfull').
	 *
	 * @param   array<string, mixed>  $row  Поля post (id, alt_name, category, date).
	 */
	public static function fullLink(array $row): string {
		global $config;

		self::ensureDleClasses();

		$row = self::normalizeRow($row);
		$id  = (int) ($row['id'] ?? 0);

		if($id <= 0) {
			return rtrim((string) ($config['http_home_url'] ?? '/'), '/') . '/';
		}

		$ts = (int) $row['date'];

		return (string) \DLEUrl::BuildUrl('showfull', [
			'category'  => function_exists('get_url')? get_url($row['category']) : '',
			'year'      => date('Y', $ts),
			'month'     => date('m', $ts),
			'day'       => date('d', $ts),
			'news_name' => (string) ($row['alt_name'] ?? ''),
			'newsid'    => $id,
		]);
	}

	/**
	 * Экранированный {title} как в DLE.
	 *
	 * @param   array<string, mixed>  $row
	 */
	public static function title(array $row): string {
		$title = stripslashes((string) ($row['title'] ?? ''));

		return str_replace('&amp;amp;', '&amp;', htmlspecialchars($title, ENT_QUOTES, 'UTF-8'));
	}

	/**
	 * Парсит строку шаблона всеми тегами новости DLE + модульными $extra.
	 *
	 * @param   array<string, mixed>                  $row      Строка post (+ extras при наличии)
	 * @param   array<string, string>                 $extra    Модульные плейсхолдеры ({user}, {suggested_tags}, …)
	 * @param   array{mode?: string, globals?: bool}  $options  mode=short|full, globals=compile_global_tags
	 */
	public static function apply(string $template, array $row, array $extra = [], array $options = []): string {
		if($template === '') {
			return '';
		}

		self::ensureDleClasses();

		$row       = self::normalizeRow($row);
		$mode      = (($options['mode'] ?? 'short') === 'full')? 'full' : 'short';
		$full_link = self::fullLink($row);
		$tpl       = self::createTemplateEngine($template);

		/** @var \dle_template $tpl */
		$fillFile = __DIR__ . '/ParseTemplateTags/fill_news.php';
		/** @noinspection PhpIncludeInspection */
		include DLEPlugins::Check($fillFile);

		$content = (string) ($tpl->result['dc_parse'] ?? '');

		if(!empty($options['globals']) && method_exists($tpl, 'compile_global_tags')) {
			$content = (string) $tpl->compile_global_tags($content);
		}

		// Совместимость TagsAdd: %title% / %link% если не переданы в $extra
		$compat = [
			'%title%' => self::title($row),
			'%link%'  => $full_link,
		];

		foreach($compat as $key => $value) {
			if(!array_key_exists($key, $extra)) {
				$extra[$key] = $value;
			}
		}

		foreach($extra as $key => $value) {
			$content = str_ireplace((string) $key, (string) $value, $content);
		}

		return $content;
	}

	/**
	 * @param   array<string, mixed>  $row
	 *
	 * @return array<string, mixed>
	 */
	private static function normalizeRow(array $row): array {
		$defaults = [
			'id'          => 0,
			'title'       => '',
			'alt_name'    => '',
			'category'    => 0,
			'date'        => time(),
			'autor'       => '',
			'short_story' => '',
			'full_story'  => '',
			'xfields'     => '',
			'comm_num'    => 0,
			'news_read'   => 0,
			'allow_rate'  => 0,
			'rating'      => 0,
			'vote_num'    => 0,
			'votes'       => '',
			'fixed'       => 0,
			'allow_comm'  => 1,
			'tags'        => '',
			'editdate'    => 0,
			'editor'      => '',
			'reason'      => '',
			'view_edit'   => 0,
			'approve'     => 1,
		];

		$row = array_merge($defaults, $row);

		if(!is_numeric($row['date'])) {
			$ts          = strtotime((string) $row['date']);
			$row['date'] = $ts !== false? $ts : time();
		} else {
			$row['date'] = (int) $row['date'];
		}

		if(!empty($row['editdate']) && !is_numeric($row['editdate'])) {
			$ts              = strtotime((string) $row['editdate']);
			$row['editdate'] = $ts !== false? $ts : 0;
		} else {
			$row['editdate'] = (int) ($row['editdate'] ?? 0);
		}

		$row['id']         = (int) $row['id'];
		$row['category']   = (string) $row['category'];
		$row['comm_num']   = (int) $row['comm_num'];
		$row['news_read']  = (int) $row['news_read'];
		$row['allow_rate'] = (int) $row['allow_rate'];
		$row['rating']     = (int) $row['rating'];
		$row['vote_num']   = (int) $row['vote_num'];
		$row['fixed']      = (int) $row['fixed'];
		$row['allow_comm'] = (int) $row['allow_comm'];
		$row['view_edit']  = (int) $row['view_edit'];

		return $row;
	}

	private static function createTemplateEngine(string $template): \dle_template {
		$tpl                = new \dle_template();
		$tpl->template      = $template;
		$tpl->copy_template = $template;
		$tpl->data          = [];
		$tpl->block_data    = [];
		$tpl->result        = [];

		return $tpl;
	}

	private static function ensureDleClasses(): void {
		if(!defined('DATALIFEENGINE')) {
			throw new \RuntimeException('ParseTemplateTags требует загруженный DLE (DATALIFEENGINE)');
		}

		if(!class_exists('dle_template', false)) {
			require_once DLEPlugins::Check(ENGINE_DIR . '/classes/templates.class.php');
		}

		if(!class_exists('DLEUrl', false)) {
			require_once DLEPlugins::Check(ENGINE_DIR . '/classes/urls.class.php');
		}

		if(!class_exists('DLEXFields', false)) {
			require_once DLEPlugins::Check(ENGINE_DIR . '/classes/xfields.class.php');
		}
	}

}
