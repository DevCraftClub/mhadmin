<?php
//===============================================================
// Файл: settings.schema.php                                    =
// Путь: devcraft/src/modules/Admin/settings.schema.php         =
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

use DevCraft\Core\Config\Paths;
use DevCraft\Core\Enums\FormLayout;
use DevCraft\Core\I18n\Translation;
use DevCraft\Form\FormSchemaBuilder;

/**
 * Схема настроек модуля DevCraft Admin для SettingsFormService.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
$telegramTypeDescr = __('Вариация оповещений в канал:')
                     . '<ul>'
                     . __('<li><b>Все</b> — отправляет буквально все отчёты и логи в канал</li>')
                     . __('<li><b>Ошибки</b> — отправляет только ошибки в канал</li>')
                     . __('<li><b>Информация</b> — отправляет только информационные отчёты в канал</li>')
                     . __('<li><b>Уведомления</b> — отправляет только уведомления в канал</li>')
                     . __('<li><b>Предупреждения</b> — отправляет только предупреждения в канал</li>')
                     . __('<li><b>Критические ошибки</b> — отправляет только критические ошибки в канал</li>')
                     . __('<li><b>Отладка</b> — отправляет только информацию об отладке в канал</li>')
                     . '</ul><br>'
                     . __('Если в плагине логирование не проставлено, то и сообщения этого рода тоже не будут отправлены.<br>')
                     . __('Если выбрано «Все», то остальные выбранные параметры игнорируются');

return FormSchemaBuilder::create('devcraft')
                        ->layout(FormLayout::TABS)
                        ->section(__('Общие'))
	                        ->number('list_count', __('Количество объектов'))
		                        ->description(
			                        __('Введите количество объектов, которые будут отображены в списках, таблицах и т.д. ')
			                        . __('Это глобальное значение для всех модулей автора. ')
			                        . __('При пустом значении будут браться значения из настройки движка ')
			                        . __('«Количество отображаемых новостей на страницу».'),
		                        )
		                        ->filter(FILTER_VALIDATE_INT)
	                        ->number('cache_timer', __('Время хранения кеша'))
		                        ->description(
			                        __('Введите время в минутах, сколько кеш запросов должен быть сохранён на сервере. ')
			                        . __('При запросе скрипт будет проверять, когда был создан файл кеша, ')
			                        . __('и если он будет превышать заданный лимит, то кеш будет пересоздан.<br>')
			                        . __('<b><i>По умолчанию: 60 минут</i></b>'),
		                        )
		                        ->filter(FILTER_VALIDATE_INT)
		                        ->default(60)
	                        ->select('language', __('Язык админки'))
		                        ->description(__('Данная опция позволит использовать админпанель и модули на разных языках'))
								->options(array_column(Translation::getFormattedLanguageList(), 'name', 'tag'))
		                        ->default('ru_RU')
	                        ->select('theme', __('Тема оформления админки'))
	                            ->description(__('Пока можно выбирать между светлой (стандартной) и тёмной. Сохраните и обновите страницу'))
		                        ->options([
			                        'light' => __('Светлая'),
			                        'dark'  => __('Тёмная'),
		                        ])
		                        ->default('light')
//	                        ->checkbox('cache_icon', __('Добавить кнопку с очищением кеша?'))
//		                        ->description(
//			                        __('При включенном параметре в шапку админпанели DLE добавится кнопка с функционалом об очищении кеша системы'),
//		                        )
//		                        ->default(false)
                        ->section(__('Отладка'))
	                        ->checkbox('debug', __('Режим отладки'))
		                        ->description(
			                        __('Подробные диагностические сообщения в консоль браузера, error_log сервера и журнал (тип debug). ')
			                        .
			                        __('Для разовой проверки без сохранения настроек: <code>?dc_debug=1</code> (сессия DLE, действует во всех вкладках браузера).'),
		                        )
		                        ->default(false)
                        ->section(__('Пути'))
	                        ->text('cache_path', __('Путь до кеша файлов'))
		                        ->description(
			                        __('Укажите путь, где будут храниться файлы кеша.<br>По умолчанию: <code>{path}</code>',
				                        ['{path}' => str_replace(ROOT_DIR, '', Paths::cache())]),
		                        )
		                        ->default(str_replace(ROOT_DIR, '', Paths::cache()))
	                        ->text('locales_path', __('Путь до файлов языков'))
		                        ->description(
			                        __('Укажите путь, откуда будут браться языковые файлы.<br>По умолчанию: <code>{path}</code>',
				                        ['{path}' => str_replace(ROOT_DIR, '', Paths::locales())]),
		                        )
		                        ->default(str_replace(ROOT_DIR, '', Paths::locales()))
		                ->section(__('Логирование'))
	                        ->checkbox('logs', __('Включить логирование?'))
		                        ->description(__('При включенном параметре будет создавать текстовые логи в папке <b>{path}</b>',
			                        ['{path}' => str_replace(ROOT_DIR, '', Paths::logs())]))
		                        ->default(false)
	                        ->checkbox('logs_db', __('Сохранять логи в базе данных?'))
		                        ->description(__('При включённом параметре все логи будут сохраняться в базу данных'))
		                        ->default(false)
	                        ->checkbox('logs_telegram', __('Отправлять логи в телеграм?'))
		                        ->description(
			                        __('При включённом параметре и заполненных полях ниже — скрипт будет отправлять логи в ваш телеграм-канал'),
		                        )
		                        ->default(false)
	                        ->text('logs_telegram_api', __('API ключ бота'))
		                        ->description(
			                        __('API ключ бота, который можно получить у <a href="https://t.me/BotFather" target="_blank" rel="noopener">@BotFather</a>, более детальная информация <a href="https://readme.devcraft.club/latest/dev/telegramposting/bot/" target="_blank" rel="noopener">здесь</a>.'),
		                        )
		                    ->text('logs_telegram_channel', __('Канал телеграма'))
		                        ->description(
			                        __('Как получить ID канала можно узнать <a href="https://readme.devcraft.club/latest/dev/telegramposting/bot/#_5" target="_blank" rel="noopener">здесь</a>.'),
		                        )
	                        ->multi('logs_telegram_type', __('Тип оповещений'))
		                        ->description($telegramTypeDescr)
		                        ->options([
			                        'all'      => __('Все'),
			                        'error'    => __('Ошибки'),
			                        'info'     => __('Информация'),
			                        'notice'   => __('Уведомления'),
			                        'warning'  => __('Предупреждения'),
			                        'critical' => __('Критические ошибки'),
			                        'debug'    => __('Отладка'),
		                        ])
		                        ->default('all')
                        ->build();
