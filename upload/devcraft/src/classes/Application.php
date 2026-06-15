<?php
//===============================================================
// Файл: Application.php                                        =
// Путь: devcraft/src/classes/Application.php                   =
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

namespace DevCraft\Core;

use Twig\Environment;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Admin\Router;
use DevCraft\Enums\AdminErrorKind;
use DevCraft\Core\Module\Registry;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\Support\DleDataService;
use DevCraft\Core\Twig\EnvironmentFactory;
use DevCraft\Core\Admin\AdminErrorRenderer;
use DevCraft\Core\Database\DatabaseGateway;
use DevCraft\Core\Support\DataLoaderService;
use DevCraft\Core\Exception\DevCraftException;
use DevCraft\Core\Support\AssetsCheckerService;

/**
 * Главный контейнер приложения DevCraft (Singleton).
 *
 * Инициализирует реестр модулей, Twig и лениво предоставляет сервисы
 * базы данных, загрузки данных и проверки ассетов.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core
 */
final class Application {

	/**
	 * Единственный экземпляр приложения.
	 *
	 * @since 200.4.0
	 * @var self|null
	 */
	private static ?self $instance = NULL;

	/**
	 * Признак завершённой инициализации ядра.
	 *
	 * @since 200.4.0
	 * @var bool
	 */
	private bool $booted = false;

	/**
	 * Реестр модулей DevCraft.
	 *
	 * @since 200.4.0
	 * @var Registry|null
	 */
	private ?Registry $registry = NULL;

	/**
	 * Экземпляр Twig для рендеринга шаблонов.
	 *
	 * @since 200.4.0
	 * @var Environment|null
	 */
	private ?Environment $twig = NULL;

	/**
	 * Шлюз доступа к базе данных через Cycle ORM.
	 *
	 * @since 200.4.0
	 * @var DatabaseGateway|null
	 */
	private ?DatabaseGateway $database = NULL;

	/**
	 * Сервис загрузки данных из таблиц DLE с кешированием.
	 *
	 * @since 200.4.0
	 * @var DataLoaderService|null
	 */
	private ?DataLoaderService $dataLoader = NULL;

	/**
	 * Сервис агрегированных данных DLE (пользователи, категории и т.д.).
	 *
	 * @since 200.4.0
	 * @var DleDataService|null
	 */
	private ?DleDataService $dleData = NULL;

	/**
	 * Сервис проверки целостности публичных ассетов.
	 *
	 * @since 200.4.0
	 * @var AssetsCheckerService|null
	 */
	private ?AssetsCheckerService $assetsChecker = NULL;

	/**
	 * Закрывает прямое создание экземпляра; используйте {@see instance()}.
	 *
	 * @since 200.4.0
	 */
	private function __construct() {}

	/**
	 * Возвращает единственный экземпляр приложения DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @return self Экземпляр приложения.
	 * @example
	 *        $app = Application::instance();
	 *
	 */
	public static function instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Выполняет однократную инициализацию путей, реестра модулей и Twig.
	 *
	 * @since 200.4.0
	 *
	 * @example
	 *     Application::instance()->boot();
	 */
	public function boot(): void {
		if($this->booted) {
			return;
		}

		Paths::register();
		$this->registry = new Registry();
		$this->twig     = EnvironmentFactory::create();
		$this->booted   = true;
	}

	/**
	 * Возвращает реестр модулей после автоматической инициализации ядра.
	 *
	 * @since 200.4.0
	 *
	 * @return Registry Реестр активных модулей.
	 * @example
	 *        $registry = Application::instance()->registry();
	 *
	 */
	public function registry(): Registry {
		$this->boot();

		return $this->registry;
	}

	/**
	 * Возвращает окружение Twig с подключённым расширением перевода.
	 *
	 * @since 200.4.0
	 *
	 * @return Environment Настроенный экземпляр Twig.
	 * @example
	 *        $html = Application::instance()->twig()->render('core/dashboard.twig', $ctx);
	 *
	 */
	public function twig(): Environment {
		$this->boot();
		EnvironmentFactory::ensureTranslationExtension($this->twig);

		return $this->twig;
	}

	/**
	 * Возвращает шлюз базы данных, создавая его при первом обращении.
	 *
	 * @since 200.4.0
	 *
	 * @return DatabaseGateway Шлюз Cycle ORM для DevCraft.
	 *
	 * @throws DevCraftException Если реестр модулей не инициализирован.
	 * @example
	 *        $db = Application::instance()->database();
	 *
	 */
	public function database(): DatabaseGateway {
		$this->boot();

		if($this->registry === NULL) {
			throw new DevCraftException(__('Реестр DevCraft не инициализирован.'));
		}

		return $this->database ??= new DatabaseGateway($this->registry);
	}

	/**
	 * Запускает обработку запроса админ-модуля DevCraft.
	 *
	 * Определяет action из GET-параметров, загружает контекст плагина
	 * и передаёт управление маршрутизатору; ошибки отображает через AdminErrorRenderer.
	 *
	 * @since 200.4.0
	 *
	 * @param   string       $moduleDir  Имя каталога модуля относительно DEVCRAFT_MODULES.
	 * @param   string|null  $mod        Переопределение идентификатора модуля в URL (необязательно).
	 *
	 * @example
	 *        Application::instance()->runAdmin('Admin', 'devcraft');
	 *
	 */
	public function runAdmin(string $moduleDir, ?string $mod = NULL): void {
		$this->boot();

		$action = '';

		if(isset($_GET['action']) && (string) $_GET['action'] !== '') {
			$action = (string) $_GET['action'];
		} elseif(isset($_GET['sites']) && (string) $_GET['sites'] !== '') {
			$action = (string) $_GET['sites'];
		}

		$errorRenderer = new AdminErrorRenderer();

		try {
			$plugin = $this->registry()->forModuleDir($moduleDir, $mod);

			if($plugin === NULL) {
				$errorRenderer->render(
					AdminErrorKind::Generic,
					__('Ошибка конфигурации'),
					__('Манифест плагина не найден или недействителен.'),
					503,
					__('Проверьте каталог модуля «{mod}» и файл manifest.php.', ['{mod}' => $moduleDir]),
				);

				return;
			}

			(new Router())->dispatch($plugin, $action);
		} catch(DevCraftException $exception) {
			$errorRenderer->render(
				AdminErrorKind::Generic,
				__('Ошибка'),
				$exception->getMessage(),
				403,
			);
		} catch(\Throwable $exception) {
			$errorRenderer->render(
				AdminErrorKind::ServerError,
				__('Внутренняя ошибка'),
				__('Что-то пошло не так. Повторите попытку позже.'),
				500,
				$exception->getMessage(),
			);
		}
	}

	/**
	 * Возвращает сервис загрузки данных с учётом таймера кеша из настроек.
	 *
	 * @since 200.4.0
	 *
	 * @return DataLoaderService Сервис выборки данных DLE.
	 * @example
	 *        $rows = Application::instance()->dataLoader()->loadData(['table' => 'users']);
	 *
	 */
	public function dataLoader(): DataLoaderService {
		$this->boot();

		$config = DevCraftConfig::all();
		$timer  = (int) ($config['cache_timer'] ?? 3600);

		return $this->dataLoader ??= new DataLoaderService($this->database(), $timer);
	}

	/**
	 * Возвращает сервис агрегированных данных DLE с кешированием.
	 *
	 * @since 200.4.0
	 *
	 * @return DleDataService Сервис высокоуровневых данных DLE.
	 * @example
	 *        $users = Application::instance()->dleData()->users();
	 *
	 */
	public function dleData(): DleDataService {
		$this->boot();

		$config = DevCraftConfig::all();
		$timer  = (int) ($config['cache_timer'] ?? 3600);

		return $this->dleData ??= new DleDataService($this->dataLoader(), $timer);
	}

	/**
	 * Возвращает сервис проверки публичных ассетов DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @return AssetsCheckerService Сервис AssetsChecker.
	 * @example
	 *        $checker = Application::instance()->assetsChecker();
	 *
	 */
	public function assetsChecker(): AssetsCheckerService {
		$this->boot();

		return $this->assetsChecker ??= new AssetsCheckerService();
	}

	/**
	 * Возвращает публичный URL каталога core/assets шаблонов DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @return string URL с завершающим слэшем.
	 * @example
	 *        $cssUrl = Application::instance()->public_asset_url() . 'css/admin.css';
	 *
	 */
	public function public_asset_url(): string {
		return str_replace(ROOT_DIR, Paths::base(), Paths::templates()) . '/core/assets/';
	}

	/**
	 * Возвращает базовый URL каталога Public модуля (JS/CSS из manifest assets).
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $modulePath  Абсолютный путь к корню модуля.
	 *
	 * @return string URL каталога Public с завершающим слэшем.
	 * @example
	 *        $jsUrl = Application::instance()->modulePublicAssetUrl($plugin->modulePath()) . 'app.js';
	 *
	 */
	public function modulePublicAssetUrl(string $modulePath): string {
		return str_replace(ROOT_DIR, Paths::base(), rtrim($modulePath, '/\\')) . '/Public/';
	}

}
