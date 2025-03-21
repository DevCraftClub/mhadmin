<?php
//===============================================================
// Файл: changelog.php                                          =
// Путь: engine/inc/maharder/admin/modules/admin/changelog.php  =
// Последнее изменение: 2024-03-14 15:42:27                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

global $mh;

$logs = [
	'180.3.5.1' => [
		__('[FIX] Исправление путей и проверки кеша класса ComposerAction'),
	],
	'180.3.5' => [
		__('[NEW] Добавлен метод сохранения конфигурации плагина, <kbd>saveConfig</kbd> в классе <kbd>DataManager</kbd>'),
		__('[FIX] Исправлена функция <kbd>joinPaths</kbd> для корректной обработки путей, особенно в средах Windows'),
		__('[FIX] Исправлены проблемы с переменными при генерации новых модулей.'),
		__('[FIX] Исправлены проблемы системой кеширования.'),
	],
	'173.3.4' => [
		__('[NEW] Новый вид в input.twig: date, time и datetime'),
		__('[NEW] Добавление новых фильтров (rangeFilter и dateRangeFilter) для TwigFilter'),
		__('[NEW] Добавлена функция `getFullUserGroups()` в трейте `DleData` для вывода информации о всех группах'),
		__('[NEW] Добавлена функция `userGroup()` для вывода определённой группы пользователей в шаблонах Twig'),
		__('[NEW] Добавлены переменная `current_user` для вывода массива информации о текущем пользователе и функция `userInfo()` для вывода определённого пользователя в шаблонах Twig'),
		__('[NEW] Добавлены поля "Кто создал" и "Кто изменил" в базовую модель (BasisModel). Данные можно увидеть в самой базе данных, либо в просмотре самой сущности.'),
		__('[UPDATE] Обновлён редактор для больших полей, где редактор подключается. Используется TinyMCE с конфигурацией от DLE. Пока без загрузки файлов.'),
		__('[UPDATE] Обновлена документация методов getGlobals и getFunctions в AdminUrlExtension'),
		__('[UPDATE] Добавлен новый метод createOrUpdate для класса MhDB'),
		__('[FIX] Исправлены мелкие ошибки'),
	],
	'173.3.3' => [
		__('[FIX] Исправлена работа с composer'),
		__('[FIX] Исправлена проблема цикличности в классе переводов'),
	],
	'173.3.2' => [
		__('[FIX] По неопределённой причине закинул старую версию поверх новой при мёрдже')
	],
	'173.3.1' => [
		__('[FIX] Исправлена загрузка скриптов при помощи composer'),
		__('[FIX] Исправлены мелкие ошибки в коде'),
		__('[FIX] Добавлены placeholder для стилей и скриптов'),
	],
	'173.3.0' => [
		__('[NEW] Добавлена мультиязычная поддержка, перевод можно осуществить при помощи сервиса Crowdin. Ссылка в подвале каждого модуля.'),
		__('[NEW] Добавлена возможность выводов логов на отдельной странице.'),
		__('[NEW] Добавлено моделирование таблиц в базе данных при помощи <a href="https://cycle-orm.dev" target="_blank">Cycle ORM</a>. Это поможет в дальнейшем создавать модули с более сложной структурой и работу с данными.'),
		__('[NEW] Все изменения в таблицах будут храниться в отдельной таблице "migrations" и в папке "_migrations". Это позволит легко сделать откат изменений.'),
		__('[NEW] Добавлена возможность сортировать и фильтровать данные таблиц на страницах административной панели (где такие есть, пример: Логи).'),
		__('[NEW] Добавлена возможность проверять на обновление плагина на сайте <a href="https://devcraft.club" target="_blank">devcraft.club</a>. Используется гостевой ключ.'),
		__('[UPDATE] Минимальная версия PHP 8.3'),
		__('[UPDATE] Функционал обновлён до версий DLE 17.3'),
		__('[UPDATE][BETA] Установщик упрощён. Теперь зависимости загружаются и устанавливаются при первом открытии [любого] модуля. Может занять несколько минут. Теперь установщик весит мало.'),
		__('[UPDATE] Откат функций, которые использовали класс DLEFiles. Он не работает со сторонними разработками как надо. Либо работает, но документации к нему то нет.'),
		__('[UPDATE] Изменена структура файлов админпанели. Теперь все файлы модуля находятся в папке "_modules", a шаблоны в папке "_templates".'),
		__('[DELETE] Удалены устаревшие и ненужные методы логирования.'),
		__('[DELETE] Удалена замена иконок в админпанели DLE.'),
	],
	'2.0.8' => [
		__('[UPDATE] Решено было использовать стандартные классы DLE для работы с файлами'),
		__('[FIX] Исправлена ошибка: https://devcraft.club/tickets/http-error-500.6/'),
	],
	'2.0.7' => [
		__('[NEW] Добавлен функционал проверки обновления плагина'),
		__('[UPDATE] Изменён подход к некоторым классам'),
		__('[FIX] Вернул поддержку PHP 7.2'),
		__('[FIX] Вернул поддержку PHP >= 8'),
	],
	'2.0.6.1' => [
		__('[FIX] Удалены остаточные файлы (кеш, конфигурации, ...)'),
		__('[FIX] Вернул нужные, но удалённые файлы'),
	],
	'2.0.6' => [
		__('[NEW] Добавлена функция отправки логов в телеграм [БЕТА]'),
		__('[FIX] Исправил ошибку работы логирования'),
	],
	'2.0.5' => [
		__('[FIX] Исправил работу моделей'),
		__('[FIX] Исправил обработку кеша'),
	],
	'2.0.4' => [
		__('[FIX] Обновил файлы для генерации модуля'),
	],
	'2.0.3' => [
		__('[NEW] Добавлена возможность пользователю самому решать использовать ли в админпанели обновлённые иконки или нет'),
		__('[NEW] Добавлена возможность пользователю самому решать подключать в админке глобальную кнопку по очистке кеша или нет'),
		__('[NEW] Дальнейшая работа с базой данных будет происходить только через созданные мною модели. Для этого будет использоваться функционал классов Model и Table'),
		__('[UPDATE] Логирование ошибок будет происходить в дальнейшем через <a href="https://seldaek.github.io/monolog/" target="_blank">Monolog - Logging for PHP</a>. При включённом функционале - библиотека будет отправлять уведомления в консоль браузера, а так-же сохранять файлов в папку логов'),
		__('[UPDATE] Обновление библиотек до последних версий с минимально возможной PHP версией <b>7.2.9</b>'),
		__('[UPDATE] Подключены иконки FontAwesome 6.1.1 Pro, используется вариант light (о не достающих или пропавших иконок прошу сообщать)'),
		__('[FIX] Путь до иконки модуля исправлен 🤦‍♂️'),
		__('[FIX] Продокументировал классы с небольшими объяснениями, что делает какая функция и для чего используется та или иная переменная'),
		__('[DEL] Удалён кошелёк WMR'),
	],
	'2.0.2' => [
		__('FIX: Добавлена иконка'),
		__('FIX: Исправлен файл AJAX'),
	],
	'2.0.1' => [
		__('В связи с некоторыми обстоятельствами пришлось отказаться от i18n модуля. Возможно он выйдет отдельным модулем.'),
		__('Обновление зависимостей до минимально требуемой версии PHP 7.4'),
		__('Улучшена реструктуризация классов'),
		__('Добавлена тёмная тема оформления'),
		__('Для желающих создать модуль на основе моей админки была создана функция генерации путей и файлов'),
		__('Fontawesome были обновлены до 6ой версии'),
		__('Добавлена проверка на целостность файлов и их обновления'),
	],
	'2.0.0' => [
		__('Полностью новая админпанель, которая не зависит от оформления и функционала самой DLE'),
		__('За основу панели был взят движок Twig, создавать свои шаблоны будет проще'),
		__('Очищать кеш в админке стало проще - кнопка была выведена в ряд с "Добавить новость" и "Редактировать новости"'),
		__('Внедрён i18n в систему DLE, это даёт возможность создавать мультиязычные сайты (модуль отдельно продаваться или делаться не будет)'),
		__('i18n: не зависит от базы данных, все фразы хранятся в файлах json'),
		__('i18n: в шаблонах достаточно использовать тег {trans text="сюда переводимый текст"}, чтобы перевести фразу'),
		__('i18n: в скриптах PHP достаточно использовать функцию translate("сюда переводимый текст"), чтобы перевести фразу'),
		__('i18n: в скриптах JS достаточно использовать функцию translateJS("сюда переводимый текст"), чтобы перевести фразу. Желательно подгружать фразы при загруженной странице, т.к. async не доработан'),
		__('i18n: мультиязычность поддерживается в "Новости", "Баннеры", "Категории", "Электронная почта / Рассылки", "Метатеги", "Опросы", "Вопросы и ответы", "Статические страницы", "Группы пользователей", а так-же вывод новостей и статических страниц на сайте'),
		__('i18n: поддерживается машинный перевод через сервис RapidApi'),
		__('i18n: динамичное использование, не требующее массивов данных - достаточно указать текст'),
	],
	'1.7.1' => [
		__('небольшой фикс в языковом файле'),
	],
	'1.7' => [
		__('Обновление иконок от fontawesome глобально'),
		__('Обновление многих функций'),
		__('Добавление языковых файлов (в дальнейшем будет проще локализовать модули)'),
		__('Добавлен немецкий язык к оболочке'),
	],
	'1.6' => [
		__('Фикс'),
	],
	'1.5' => [
		__('Новые модальные и всплывающие окна'),
		__('Улучшены некоторые функции'),
		__('Добавлен скрипт DateTimePicker'),
		__('Добавлены несколько украшательств'),
	],
	'1.4' => [
		__('Вывод категорий'),
	],
	'1.3' => [
		__('Автоматический вывод доп. полей в админке'),
		__('Автоматический вывод пользователей'),
		__('Исправлены баги в JS'),
	],
	'1.1' => [
		__('Обновление до актуальных версий DLE'),
		__('Мелкие правки'),
	],
	'1.0.0' => [
		__('Основной релиз'),
	],
];

$modVars = [
	'title' => __('История изменений'),
	'module_icon' => 'fad fa-robot',
	'logs' => $logs,
];

// Настройка хлебных крошек
// Крошки это массив с массивами, которые содержат информацию о ссылке (url) и её названии (name)
// Крошки добавляются в каждом файле модуля с исключением самого главного
$mh->setBreadcrumb(new BreadCrumb($modVars['title'], $mh->getLinkUrl('changelog')));


$htmlTemplate = 'admin/changelog.html';