<?php
//===============================================================
// Файл: TwigTranslatorBridge.php                               =
// Путь: devcraft/src/classes/I18n/TwigTranslatorBridge.php     =
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

namespace DevCraft\Core\I18n;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Прокси для Symfony TranslationExtension: всегда использует актуальный Translator после reset().
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.I18n
 */
final class TwigTranslatorBridge implements TranslatorInterface {

	/**
	 * Переводит строку через актуальный экземпляр Translation::getTranslator().
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $id          Идентификатор перевода.
	 * @param   array<string, mixed>  $parameters  Параметры подстановки.
	 * @param   string|null           $domain      Домен перевода.
	 * @param   string|null           $locale      Локаль.
	 *
	 * @return string Переведённая строка или исходный id.
	 *
	 * @example
	 *     $bridge = new TwigTranslatorBridge();
	 *     $text = $bridge->trans('Заголовок');
	 */
	public function trans(string $id, array $parameters = [], ?string $domain = NULL, ?string $locale = NULL): string {
		$translator = Translation::getTranslator();

		if($translator === NULL) {
			return $id;
		}

		return $translator->trans($id, $parameters, $domain, $locale);
	}

	/**
	 * Возвращает текущую локаль приложения.
	 *
	 * @since 200.4.0
	 *
	 * @return string Тег локали.
	 *
	 * @example
	 *     $bridge = new TwigTranslatorBridge();
	 *     $locale = $bridge->getLocale();
	 */
	public function getLocale(): string {
		return Translation::getLocale();
	}

}
