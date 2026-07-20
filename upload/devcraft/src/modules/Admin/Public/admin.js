/**
 * Admin-Modul DevCraft (Assets, Dashboard, Composer; Filter — в filter.js)
 */
(function (global) {
	'use strict';

	if (!global.DevCraft) {
		console.error('[DevCraft] Core devcraft.js muss vor admin.js geladen werden.');
		return;
	}

	const DevCraft = global.DevCraft;
	const DevCraftMetro = DevCraft.Metro;
	const DevCraftAjax = DevCraft.Ajax;
	const t = function (phrase, params) {
		return DevCraft.__(phrase, params || {});
	};
	const NOTIFY_CLS = {
		success: 'bg-green fg-white',
		warning: 'bg-orange fg-white',
		error: 'alert bg-red fg-white',
		info: '',
	};

	class DevCraftAssets {
		static lastReport = null;
	}

	class DevCraftDashboard {}

	class DevCraftComposer {}

	function shiftNewModuleField(fieldId, hasError) {
		const input = document.getElementById(fieldId) || document.querySelector('[name="' + fieldId + '"]');

		if (!input) {
			return;
		}

		const row = input.closest('.dc-field-row');

		if (!row) {
			return;
		}

		if (hasError) {
			row.classList.add('invalid');
		} else {
			row.classList.remove('invalid');
		}
	}

	function newModuleListItem(name, iconClass, descr) {
		return '<div class="item">' +
			'<span class="' + iconClass + ' icon mr-2"></span>' +
			'<div class="content">' +
			'<div class="text-bold">' + name + '</div>' +
			(descr ? '<div class="text-small">' + descr + '</div>' : '') +
			'</div></div>';
	}

	function renderNewModuleReport(report) {
		const lists = document.getElementById('lists');

		if (!lists || !report) {
			return;
		}

		['name', 'description', 'version', 'translit'].forEach(function (field) {
			shiftNewModuleField(field, false);
		});

		const containers = [
			'dirs_success',
			'dirs_failed',
			'files_success',
			'files_failed',
			'plugin_success',
			'plugin_failed',
		];

		containers.forEach(function (id) {
			const node = document.getElementById(id);

			if (node) {
				node.innerHTML = '';
				node.style.display = 'none';
			}
		});

		if (report.dirs && report.dirs.success) {
			report.dirs.success.forEach(function (entry) {
				document.getElementById('dirs_success').appendChild((function () {
					const wrap = document.createElement('div');
					wrap.innerHTML = newModuleListItem(entry, 'mif-folder');
					return wrap.firstChild;
				})());
			});
			document.getElementById('dirs_success').style.display = 'block';
		}

		if (report.files && report.files.success) {
			report.files.success.forEach(function (entry) {
				const label = typeof entry === 'string' ? entry : (entry.path || entry.file || '');
				document.getElementById('files_success').appendChild((function () {
					const wrap = document.createElement('div');
					wrap.innerHTML = newModuleListItem(t('Файл {file} создан', { file: label }), 'mif-file-empty');
					return wrap.firstChild;
				})());
			});
			document.getElementById('files_success').style.display = 'block';
		}

		if (report.plugin && report.plugin.success) {
			report.plugin.success.forEach(function (entry) {
				const name = entry.name || '';
				const link = entry.link || '';
				const descr = link ? '<a href="' + link + '" target="_blank" rel="noopener">' + t('Открыть на сайте') + '</a>' : '';
				document.getElementById('plugin_success').appendChild((function () {
					const wrap = document.createElement('div');
					wrap.innerHTML = newModuleListItem(t('Плагин «{name}» был создан', { name: name }), 'mif-cloud', descr);
					return wrap.firstChild;
				})());
			});
			document.getElementById('plugin_success').style.display = 'block';
		}

		if (report.dirs && report.dirs.fails) {
			report.dirs.fails.forEach(function (entry) {
				document.getElementById('dirs_failed').appendChild((function () {
					const wrap = document.createElement('div');
					wrap.innerHTML = newModuleListItem(entry.dir || entry.path || '', 'mif-folder', entry.message || '');
					return wrap.firstChild;
				})());
			});
			document.getElementById('dirs_failed').style.display = 'block';
		}

		if (report.files && report.files.fails) {
			report.files.fails.forEach(function (entry) {
				document.getElementById('files_failed').appendChild((function () {
					const wrap = document.createElement('div');
					wrap.innerHTML = newModuleListItem(entry.file || entry.path || '', 'mif-file-empty', entry.message || '');
					return wrap.firstChild;
				})());
			});
			document.getElementById('files_failed').style.display = 'block';
		}

		if (report.plugin && report.plugin.fails) {
			report.plugin.fails.forEach(function (entry) {
				document.getElementById('plugin_failed').appendChild((function () {
					const wrap = document.createElement('div');
					wrap.innerHTML = newModuleListItem(t('Плагин не был создан!'), 'mif-cloud', entry.message || '');
					return wrap.firstChild;
				})());
			});
			document.getElementById('plugin_failed').style.display = 'block';
		}

		lists.style.display = 'block';
	}

	function handleNewModuleSubmit() {
		const form = document.getElementById('new-module-form');
		const result = document.getElementById('new-module-result');

		if (!form) {
			return;
		}

		if (result) {
			result.innerHTML = '';
		}

		const payloadData = DevCraftAjax.serializeForm(form);
		const userHashInput = document.querySelector('input[name="user_hash"]');

		fetch(DevCraftAjax.url(DevCraftAjax.baseUrl(), {
			controller: 'admin',
			method: 'new_module',
		}), {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({
				user_hash: userHashInput ? userHashInput.value : '',
				data: JSON.stringify(payloadData),
			}).toString(),
		})
			.then(DevCraftAjax.parseResponse)
			.then(function (payload) {
				if (payload.error && Array.isArray(payload.error.fields)) {
					payload.error.fields.forEach(function (field) {
						shiftNewModuleField(field, true);
					});
				}

				const report = payload.data && payload.data.report
					? payload.data.report
					: (payload.error && payload.error.detail ? payload.error.detail : null);

				if (report) {
					renderNewModuleReport(report);
				}

				if (payload.success) {
					const defaultVersion = form.getAttribute('data-default-version') || '';
					form.reset();

					const versionInput = form.querySelector('[name="version"]');

					if (versionInput && defaultVersion) {
						versionInput.value = defaultVersion;
					}

					const dbCheckbox = form.querySelector('[name="db"]');

					if (dbCheckbox) {
						dbCheckbox.checked = true;
					}
				}

				DevCraftAjax.handleNotice(payload);
			})
			.catch(function (err) {
				if (result) {
					result.innerHTML = '';
				}

				DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось создать модуль'), err);
			});
	}

	function versionCompare(v1, v2, operator) {
		const prep = function (v) {
			v = String(v).replace(/[_\-+]/g, '.');
			v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');

			return v.length ? v.split('.') : ['-8'];
		};
		const vm = { dev: -6, alpha: -5, a: -5, beta: -4, b: -4, RC: -3, rc: -3, '#': -2, p: 1, pl: 1 };
		const num = function (v) {
			return !v ? 0 : (isNaN(v) ? (vm[v] || -7) : parseInt(v, 10));
		};
		const a = prep(v1);
		const b = prep(v2);
		const len = Math.max(a.length, b.length);
		let compare = 0;

		for (let i = 0; i < len; i++) {
			if (num(a[i]) < num(b[i])) {
				compare = -1;
				break;
			}

			if (num(a[i]) > num(b[i])) {
				compare = 1;
				break;
			}
		}

		if (!operator) {
			return compare;
		}

		switch (operator) {
			case '>':
				return compare > 0;
			case '>=':
				return compare >= 0;
			case '<':
				return compare < 0;
			case '<=':
				return compare <= 0;
			case '===':
				return compare === 0;
			default:
				return compare === 0;
		}
	}

	function buildFileListHtml(title, paths) {
		if (!paths || !paths.length) {
			return '';
		}

		let html = '<p class="mt-2 mb-1"><strong>' + title + '</strong> (' + paths.length + ')</p><ul class="dc-assets-file-list">';

		paths.forEach(function (path) {
			html += '<li><code>' + path + '</code></li>';
		});

		html += '</ul>';

		return html;
	}

	function setAssetsLoading(loading) {
		document.querySelectorAll('.js-check-assets-icon').forEach(function (icon) {
			icon.classList.toggle('rotating', loading);
		});
	}

	function updateAssetsBadge(diffCount) {
		const badge = document.getElementById('dc-assets-badge');

		if (!badge) {
			return;
		}

		if (diffCount > 0) {
			badge.textContent = String(diffCount);
			badge.classList.remove('d-none');
		} else {
			badge.textContent = '0';
			badge.classList.add('d-none');
		}
	}

	function renderAssetsStatus(report, state) {
		const status = document.getElementById('dc-assets-status');

		if (!status) {
			return;
		}

		status.classList.remove('info', 'success', 'warning', 'alert');

		if (state === 'loading') {
			status.classList.add('info');
			status.innerHTML = '<span class="mif-loop2 mr-1 rotating"></span><strong>' + t('Проверка ресурсов…') + '</strong>'
				+ '<span class="text-small fg-gray ml-2">' + t('Сравнение локальных файлов с сервером разработчика') + '</span>';
			status.classList.remove('d-none');
			return;
		}

		if (state === 'error') {
			status.classList.add('alert');
			status.innerHTML = '<span class="mif-warning mr-1"></span><strong>' + t('Не удалось проверить ресурсы') + '</strong>'
				+ '<span class="text-small ml-2">' + (report || t('Повторите попытку позже')) + '</span>';
			status.classList.remove('d-none');
			return;
		}

		if (!report || !report.has_diff) {
			status.classList.add('success');
			status.innerHTML = '<span class="mif-checkmark mr-1"></span><strong>' + t('Все ресурсы актуальны') + '</strong>'
				+ '<span class="text-small fg-gray ml-2">' + t('На сервере: {remote}, локально: {local}', {
					remote: report ? report.remote_count : 0,
					local: report ? report.local_count : 0,
				}) + '</span>';
			status.classList.add('d-none');
			return;
		}

		status.classList.add('warning');
		status.innerHTML = '<span class="mif-notification mr-1"></span><strong>' + t('Обнаружены расхождения ресурсов') + '</strong>'
			+ '<span class="text-small ml-2">' + t('Отличается файлов: {count} (на сервере: {remote}, локально: {local})', {
				count: report.diff_count,
				remote: report.remote_count,
				local: report.local_count,
			}) + '</span>'
			+ ' <button type="button" class="button small ml-2 js-assets-status-details">' + t('Подробнее') + '</button>';
		status.classList.remove('d-none');
	}

	function showAssetsDialog(report) {
		if (!DevCraft.Metro.lib() || !DevCraft.Metro.dialogApi() || !DevCraft.Metro.dialogApi().create) {
			DevCraftMetro.notify(t('Ресурсы'), report.has_diff
				? t('Отличается файлов: {count}', { count: report.diff_count })
				: t('Обновлений нет'), report.has_diff ? 'warning' : 'info');
			return;
		}

		let content = '<div class="dc-assets-dialog">';

		if (!report.has_diff) {
			content += '<p>' + t('Все файлы ресурсов совпадают с сервером разработчика.') + '</p>';
		} else {
			content += '<p class="mb-2">' + t('На сервере: {remote}, локально: {local}', {
				remote: report.remote_count,
				local: report.local_count,
			}) + '</p>';
			content += buildFileListHtml(t('Требуют установки'), report.missing);
			content += buildFileListHtml(t('Требуют обновления'), report.outdated);
		}

		content += '</div>';

		const buttons = [
			{
				text: t('Закрыть'),
				cls: 'js-dialog-close',
				onclick: function () {},
			},
		];

		if (report.has_diff) {
			buttons.unshift({
				text: t('Заменить все'),
				cls: 'warning',
				onclick: function () {
					syncAssets('all', report);
				},
			});
			buttons.unshift({
				text: t('Заменить изменённые'),
				cls: 'success',
				onclick: function () {
					syncAssets('changed', report);
				},
			});
		}

		DevCraftMetro.dialogCreate({
			title: report.has_diff ? t('Расхождения ресурсов') : t('Проверка ресурсов'),
			content: content,
			customButtons: buttons,
		});
	}

	function syncAssets(mode, report) {
		setAssetsLoading(true);
		DevCraftMetro.notify(t('Синхронизация'), t('Загрузка файлов ресурсов…'), 'info');

		DevCraftAjax.post('sync_assets', { mode: mode })
			.then(function (payload) {
				setAssetsLoading(false);
				DevCraftAjax.handleNotice(payload);

				if (payload.success && payload.data && payload.data.report) {
					DevCraft.Assets.lastReport = payload.data.report;
					renderAssetsStatus(payload.data.report, 'ok');
					updateAssetsBadge(payload.data.report.diff_count || 0);
					persistAssetsReport(payload.data.report);
				} else if (payload.success && payload.data) {
					return runAssetsCheck({ openDialog: false });
				}
			})
			.catch(function (err) {
				setAssetsLoading(false);
				DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось синхронизировать ресурсы'), err);
			});
	}

	function runAssetsCheck(options) {
		const opts = options || {};
		const openDialog = opts.openDialog !== false;

		setAssetsLoading(true);
		renderAssetsStatus(null, 'loading');

		return DevCraftAjax.post('check_assets', {})
			.then(function (payload) {
				setAssetsLoading(false);

				if (!payload.success) {
					renderAssetsStatus(
						payload.error && payload.error.message ? payload.error.message : null,
						'error',
					);
					DevCraftAjax.handleNotice(payload);
					return null;
				}

				const report = payload.data || {};
				DevCraft.Assets.lastReport = report;
				renderAssetsStatus(report, 'ok');
				updateAssetsBadge(report.diff_count || 0);
				persistAssetsReport(report);

				if (openDialog) {
					if (!report.has_diff) {
						DevCraftMetro.toast(t('Обновлений нет'));
					}

					showAssetsDialog(report);
				}

				return report;
			})
			.catch(function (err) {
				setAssetsLoading(false);
				renderAssetsStatus(null, 'error');
				DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось проверить ресурсы'), err);
				return null;
			});
	}

	function showUpdateDialog(data) {
		if (!DevCraft.Metro.lib() || !DevCraft.Metro.dialogApi() || !DevCraft.Metro.dialogApi().create) {
			return;
		}

		const lastUpdate = data.last_update
			? new Date(data.last_update * 1000).toLocaleString('ru-RU')
			: '—';

		const content = '<div class="dc-update-dialog">'
			+ '<p><strong>' + (data.title || t('Обновление')) + '</strong> '
			+ '<span class="badge inline bg-red fg-white">v' + (data.remote_version || data.version) + '</span></p>'
			+ '<p class="text-small fg-gray">' + t('Текущая версия: v{version}', { version: data.local_version || '—' }) + '</p>'
			+ '<p class="text-small">' + t('Обновлено: {date}', { date: lastUpdate }) + '</p>'
			+ '</div>';

		DevCraft.Metro.dialogApi().create({
			title: t('Доступна новая версия'),
			content: content,
			closeButton: true,
			defaultActions: false,
			customButtons: [
				{
					text: t('Скачать'),
					cls: 'primary',
					onclick: function () {
						if (data.download_link) {
							window.open(data.download_link, '_blank', 'noopener');
						}
					},
				},
				{
					text: t('Закрыть'),
					cls: 'js-dialog-close',
					onclick: function () {},
				},
			],
		});
	}

	function runUpdateCheck() {
		const dashboard = document.querySelector('.dc-dashboard');

		if (!dashboard) {
			return;
		}

		const siteId = parseInt(dashboard.dataset.siteId || '0', 10);
		const version = dashboard.dataset.version || '';

		if (siteId <= 0) {
			DevCraftMetro.notify(t('Версия'), t('ID ресурса не настроен в manifest.php'), 'warning');
			return;
		}

		DevCraftAjax.post('check_update', {
			resource_id: siteId,
			local_version: version,
		})
			.then(function (payload) {
				DevCraftAjax.handleNotice(payload);

				if (payload.success && payload.data && payload.data.update_available) {
					showUpdateDialog(payload.data);
				}
			})
			.catch(function (err) {
				DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось проверить версию'), err);
			});
	}

	function getComposerTablePlugin() {
		const tableEl = document.getElementById('dc-composer-table');
		if (!tableEl || !DevCraft.Metro.lib() || typeof DevCraft.Metro.lib().getPlugin !== 'function') {
			return null;
		}

		return DevCraft.Metro.getPlugin(tableEl, 'table');
	}

	function initComposerTable() {
		const tableEl = document.getElementById('dc-composer-table');
		if (!tableEl) {
			return;
		}

		const plugin = getComposerTablePlugin();
		if (!plugin) {
			DevCraft.Metro.makePlugin(tableEl, 'table');
		}
	}

	function runComposerSync() {
		DevCraftAjax.post('composer_sync', {})
			.then(function (payload) {
				DevCraftAjax.handleNotice(payload);

				if (payload.success) {
					const table = getComposerTablePlugin();
					if (table && typeof table.reload === 'function') {
						table.reload();
					} else {
						initComposerTable();
					}
				}
			})
			.catch(function (err) {
				DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось синхронизировать пакеты с БД'), err);
			});
	}

	function restoreAssetsReportFromSession() {
		const cached = sessionStorage.getItem('dc_assets_report');

		if (!cached) {
			return false;
		}

		try {
			const report = JSON.parse(cached);
			DevCraft.Assets.lastReport = report;
			renderAssetsStatus(report, 'ok');
			updateAssetsBadge(report.diff_count || 0);

			return true;
		} catch (e) {
			return false;
		}
	}

	function persistAssetsReport(report) {
		if (!report) {
			return;
		}

		try {
			sessionStorage.setItem('dc_assets_report', JSON.stringify(report));
		} catch (e) {
			// sessionStorage может быть недоступен
		}
	}

	function initDashboard() {
		const dashboard = document.querySelector('.dc-dashboard');

		if (!dashboard) {
			return;
		}

		const storageKey = 'dc_assets_checked';
		const alreadyChecked = sessionStorage.getItem(storageKey) === '1';

		if (alreadyChecked && restoreAssetsReportFromSession()) {
			return;
		}

		if (!alreadyChecked) {
			runAssetsCheck({ openDialog: false }).then(function (report) {
				sessionStorage.setItem(storageKey, '1');
				persistAssetsReport(report);

				if (report && report.has_diff) {
					showAssetsDialog(report);
				}
			});
		}
	}

	function _bindDocumentClick() {
		document.addEventListener('click', function (event) {
			const detailsBtn = event.target.closest('.js-assets-status-details');

			if (detailsBtn && DevCraft.Assets.lastReport) {
				showAssetsDialog(DevCraft.Assets.lastReport);
				return;
			}

			const checkAssetsBtn = event.target.closest('.js-check-assets');

			if (checkAssetsBtn) {
				runAssetsCheck({ openDialog: true });
				return;
			}

			const checkUpdateBtn = event.target.closest('.js-check-update');

			if (checkUpdateBtn) {
				runUpdateCheck();
				return;
			}

			const composerSyncBtn = event.target.closest('.js-composer-sync');
			if (composerSyncBtn) {
				runComposerSync();
				return;
			}

			const copyLogBtn = event.target.closest('.js-copy-log-text');
			if (copyLogBtn) {
				const targetId = copyLogBtn.dataset.target || 'dc-log-copy-text';
				const textarea = document.getElementById(targetId);
				const text = textarea ? textarea.value : '';

				if (text && navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
					navigator.clipboard.writeText(text).then(function () {
						DevCraftMetro.notifySuccess(t('Готово'), t('Текст скопирован в буфер обмена'));
					}).catch(function () {
						if (textarea) {
							textarea.focus();
							textarea.select();
							DevCraftMetro.notifySuccess(t('Готово'), t('Текст выделен — используйте Ctrl+C'));
						}
					});
				} else if (textarea) {
					textarea.focus();
					textarea.select();
					DevCraftMetro.notifySuccess(t('Готово'), t('Текст выделен — используйте Ctrl+C'));
				}
				return;
			}

			const deleteBtn = event.target.closest('.js-delete-log');

			if (deleteBtn) {
				const uuid = deleteBtn.dataset.uuid;
				const userHash = document.querySelector('input[name="user_hash"]');

				fetch(DevCraftAjax.url(DevCraftAjax.baseUrl(), {
					controller: 'admin',
					method: 'delete_log',
				}), {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: new URLSearchParams({
						user_hash: userHash ? userHash.value : '',
						data: JSON.stringify({ id: uuid }),
					}).toString(),
				})
					.then(DevCraftAjax.parseResponse)
					.then(function (payload) {
						if (payload.success) {
							const plugin = DevCraft.Metro.getLogsTablePlugin
								? DevCraft.Metro.getLogsTablePlugin()
								: null;

							if (plugin && typeof plugin.deleteItemByName === 'function') {
								plugin.deleteItemByName('uuid', uuid);

								if (typeof plugin.draw === 'function') {
									plugin.draw();
								}
							} else if (plugin && typeof plugin.reload === 'function') {
								plugin.reload();
							} else {
								const row = deleteBtn.closest('tr');

								if (row) {
									row.remove();
								}
							}
						}

						DevCraftAjax.handleNotice(payload);
					})
					.catch(function (err) {
						DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось удалить запись'), err);
					});
			}

			const newModuleBtn = event.target.closest('.js-new-module, .create');

			if (newModuleBtn && document.getElementById('new-module-form')) {
				handleNewModuleSubmit();
			}
		});
	}

	// selectVal / calendarVal / getLogsTablePlugin — в filter.js
	DevCraftAssets.buildFileListHtml = buildFileListHtml;
	DevCraftAssets.setLoading = setAssetsLoading;
	DevCraftAssets.updateBadge = updateAssetsBadge;
	DevCraftAssets.renderStatus = renderAssetsStatus;
	DevCraftAssets.showDialog = showAssetsDialog;
	DevCraftAssets.sync = syncAssets;
	DevCraftAssets.runCheck = runAssetsCheck;
	DevCraftAssets.persistReport = persistAssetsReport;
	DevCraftAssets.restoreReportFromSession = restoreAssetsReportFromSession;
	DevCraftDashboard.init = initDashboard;
	DevCraftDashboard.runUpdateCheck = runUpdateCheck;
	DevCraftDashboard.showUpdateDialog = showUpdateDialog;
	DevCraftDashboard.versionCompare = versionCompare;
	DevCraftDashboard.handleNewModuleSubmit = handleNewModuleSubmit;
	DevCraftDashboard.renderNewModuleReport = renderNewModuleReport;
	DevCraftDashboard.shiftNewModuleField = shiftNewModuleField;
	DevCraftDashboard.newModuleListItem = newModuleListItem;
	DevCraftDashboard.bindDocumentClick = _bindDocumentClick;
	DevCraftComposer.initTable = initComposerTable;
	DevCraftComposer.runSync = runComposerSync;

	const DevCraftAdmin = {
		Filter: DevCraft.Filter,
		Assets: DevCraftAssets,
		Dashboard: DevCraftDashboard,
		Composer: DevCraftComposer,
		boot() {
			DevCraftAdmin.Dashboard.init();
			DevCraftAdmin.Composer.initTable();
			if (DevCraft.Debug.isEnabled()) {
				DevCraft.Debug.log('Admin', 'boot', { message: t('Модуль Admin инициализирован') });
			}
		},
	};

	DevCraftAdmin.Dashboard.bindDocumentClick();
	global.DevCraftAdmin = DevCraftAdmin;
	// Filter UI: core filter.js
	global.DevCraft.Assets = DevCraftAdmin.Assets;
	global.DevCraft.Dashboard = DevCraftAdmin.Dashboard;
	global.DevCraft.Composer = global.DevCraft.Composer || {};
	Object.assign(global.DevCraft.Composer, DevCraftAdmin.Composer);

})(window);
