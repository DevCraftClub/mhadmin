/**
 * Admin-Modul DevCraft (Filter, Assets, Dashboard)
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

  class DevCraftFilter {
    static activeChipIndex = null;
    static _chipMenuCloseHandler = null;
  }

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

  function runComposerAction(actionType, packageName) {
    DevCraftAjax.post('composer_action', {
      actionType: actionType,
      packageName: packageName,
    })
      .then(function (payload) {
        DevCraftAjax.handleNotice(payload);
        if (payload.success) {
          const table = getComposerTablePlugin();
          if (table && typeof table.reload === 'function') {
            table.reload();
          }
        } else if (payload.error && payload.error.detail && payload.error.detail.output) {
          DevCraftMetro.dialogCreate({
            title: t('Ошибка Composer'),
            content: '<pre class="text-small">' + escapeHtml(String(payload.error.detail.output)) + '</pre>',
            customButtons: [
              { text: t('Повторить'), cls: 'warning', onclick: function () { runComposerAction(actionType, packageName); } },
              { text: t('Закрыть'), cls: 'js-dialog-close', onclick: function () {} },
            ],
          });
        }
      })
      .catch(function (err) {
        DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось выполнить Composer-действие'), err);
      });
  }

  function runDumpAutoload() {
    DevCraftAjax.post('dump_autoload', {})
      .then(function (payload) {
        DevCraftAjax.handleNotice(payload);
      })
      .catch(function (err) {
        DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось выполнить dump-autoload'), err);
      });
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

  function parseFilterJson(raw, fallback) {
    if (!raw) {
      return fallback;
    }

    try {
      return JSON.parse(raw);
    } catch (e) {
      return fallback;
    }
  }

  function findCatalogField(catalog, fieldId) {
    const sections = catalog.sections || [];

    for (let i = 0; i < sections.length; i++) {
      const fields = sections[i].fields || [];

      for (let j = 0; j < fields.length; j++) {
        if (fields[j].id === fieldId) {
          return fields[j];
        }
      }
    }

    return null;
  }

  function rebuildFilterHiddenInputs(container, rules) {
    container.innerHTML = '';

    rules.forEach(function (rule, index) {
      appendHidden(container, 'filter_rules[' + index + '][field]', rule.field);
      appendHidden(container, 'filter_rules[' + index + '][type]', rule.type);

      if (rule.value !== undefined && rule.value !== null) {
        if (Array.isArray(rule.value)) {
          rule.value.forEach(function (item) {
            appendHidden(container, 'filter_rules[' + index + '][value][]', item);
          });
        } else {
          appendHidden(container, 'filter_rules[' + index + '][value]', rule.value);
        }
      }

      if (rule.value_from) {
        appendHidden(container, 'filter_rules[' + index + '][value_from]', rule.value_from);
      }

      if (rule.value_to) {
        appendHidden(container, 'filter_rules[' + index + '][value_to]', rule.value_to);
      }
    });
  }

  function appendHidden(container, name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    container.appendChild(input);
  }

  function debugDaterange(step, payload) {
    DevCraft.Debug.log('Filter', step, payload);
  }

  function describeCalendarInput(input) {
    if (!input) {
      return null;
    }

    const plugin = DevCraft.Metro.lib()
      ? DevCraft.Metro.getPlugin(input, 'calendar-picker')
      : null;

    return {
      className: input.className,
      value: input.value,
      dataValue: input.getAttribute('data-value'),
      dataRole: input.getAttribute('data-role'),
      hasPlugin: !!plugin,
      pluginVal: plugin && typeof plugin.val === 'function' ? plugin.val() : null,
      elementVal: plugin && plugin.element && typeof plugin.element.val === 'function'
        ? plugin.element.val()
        : null,
    };
  }

  function dumpDaterangeState() {
    const dlgEl = document.getElementById('dc-filter-dialog');
    const scope = getFilterDialogScope(dlgEl);
    const bar = document.getElementById('dc-filter-bar');
    const catalog = bar
      ? parseFilterJson(bar.dataset.filterCatalog, { sections: [] })
      : { sections: [] };
    const rules = bar
      ? parseFilterJson(bar.dataset.filterRules, [])
      : [];
    const fieldSelect = scope.querySelector('#dc-filter-field-select');
    const selectedFieldId = readMetroSelectValue(fieldSelect);
    const selectedField = findCatalogField(catalog, selectedFieldId);

    const snapshot = {
      dialogOpen: !!(dlgEl && dlgEl.classList.contains('open')),
      scopeTag: scope ? scope.tagName + (scope.className ? '.' + scope.className : '') : null,
      selectedFieldId: selectedFieldId,
      selectedFieldType: selectedField ? selectedField.type : null,
      fromInput: describeCalendarInput(findCalendarPickerInput(scope, 'from')),
      toInput: describeCalendarInput(findCalendarPickerInput(scope, 'to')),
      collectedRule: collectFilterDialogRule(selectedField, selectedFieldId, scope),
      storedRules: rules,
      tableSourceUrl: document.getElementById('dc-logs-table') ? buildTableSourceUrl() : null,
    };

    console.group('[DevCraft Daterange] dumpState');
    console.log(snapshot);
    console.groupEnd();

    return snapshot;
  }

  function getFilterDialogScope(root) {
    if (!root) {
      root = document.getElementById('dc-filter-dialog');
    }

    if (!root) {
      return document;
    }

    return root.querySelector('.dialog-content') || root;
  }

  function resolveCalendarInput(el) {
    if (!el) {
      return null;
    }

    if (el.tagName === 'INPUT') {
      return el;
    }

    if (el.querySelector) {
      return el.querySelector('input[data-dc-filter-boundary]')
        || el.querySelector('input[data-role="calendar-picker"]')
        || el.querySelector('input.dc-filter-date-from')
        || el.querySelector('input.dc-filter-date-to')
        || el.querySelector('input');
    }

    return null;
  }

  function formatCalendarPluginValue(plugin) {
    if (!plugin) {
      debugDaterange('formatCalendarPluginValue: no plugin');
      return '';
    }

    const opts = plugin.options || plugin.o || {};
    const locale = plugin.locale;

    if (typeof plugin.val === 'function') {
      const picked = plugin.val();
      debugDaterange('formatCalendarPluginValue: plugin.val()', { picked: picked, opts: opts });

      if (picked && picked.date != null) {
        let datePart = '';

        if (typeof picked.date === 'string') {
          datePart = picked.date.split(' ')[0];
        } else if (picked.date && typeof picked.date.format === 'function') {
          datePart = picked.date.format(opts.format || 'YYYY-MM-DD', locale);
        }

        if (datePart) {
          if (opts.showTime && Array.isArray(picked.time)) {
            const hours = String(picked.time[0] ?? 0);
            const minutes = String(picked.time[1] ?? 0);
            const h = hours.length < 2 ? '0' + hours : hours;
            const m = minutes.length < 2 ? '0' + minutes : minutes;

            return datePart + ' ' + h + ':' + m + ':00';
          }

          return datePart + ' 00:00:00';
        }
      }
    }

    if (plugin.value && typeof plugin.value.format === 'function') {
      const datePart = plugin.value.format(opts.format || 'YYYY-MM-DD', locale);
      debugDaterange('formatCalendarPluginValue: plugin.value.format()', { datePart: datePart });

      if (datePart) {
        if (opts.showTime && Array.isArray(plugin.time)) {
          const hours = String(plugin.time[0] ?? 0);
          const minutes = String(plugin.time[1] ?? 0);
          const h = hours.length < 2 ? '0' + hours : hours;
          const m = minutes.length < 2 ? '0' + minutes : minutes;

          return datePart + ' ' + h + ':' + m + ':00';
        }

        return datePart + ' 00:00:00';
      }
    }

    debugDaterange('formatCalendarPluginValue: empty result');
    return '';
  }

  function findCalendarPickerInput(scope, boundaryOrClass) {
    if (!scope) {
      return null;
    }

    const boundary = boundaryOrClass === 'dc-filter-date-from' || boundaryOrClass === 'from'
      ? 'from'
      : (boundaryOrClass === 'dc-filter-date-to' || boundaryOrClass === 'to' ? 'to' : '');
    const className = boundary === 'from'
      ? 'dc-filter-date-from'
      : (boundary === 'to' ? 'dc-filter-date-to' : boundaryOrClass);

    if (boundary) {
      const byData = scope.querySelector('input[data-dc-filter-boundary="' + boundary + '"]');

      if (byData) {
        return byData;
      }
    }

    return scope.querySelector('input.' + className)
      || scope.querySelector('.' + className + ' input[data-role="calendar-picker"]')
      || scope.querySelector('.' + className + ' input')
      || scope.querySelector('.calendar-picker.' + className + ' input');
  }

  function syncCalendarPickerValue(input) {
    if (!input || !DevCraft.Metro.lib()) {
      debugDaterange('syncCalendarPickerValue: skip', { hasInput: !!input, hasMetro: !!DevCraft.Metro.lib() });
      return;
    }

    const initial = input.getAttribute('data-value') || input.value || '';

    if (!initial) {
      debugDaterange('syncCalendarPickerValue: no initial', describeCalendarInput(input));
      return;
    }

    const plugin = DevCraft.Metro.getPlugin(input, 'calendar-picker');

    if (plugin && typeof plugin.val === 'function') {
      plugin.val(initial);
      debugDaterange('syncCalendarPickerValue: applied', {
        initial: initial,
        after: describeCalendarInput(input),
      });
    } else {
      debugDaterange('syncCalendarPickerValue: no plugin', { initial: initial });
    }
  }

  function normalizeFilterDateTime(raw, boundary) {
    if (!raw) {
      return '';
    }

    raw = String(raw).trim();

    if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(raw)) {
      if (boundary === 'to' && raw.endsWith(' 00:00:00')) {
        return raw.replace(' 00:00:00', ' 23:59:59');
      }

      return raw;
    }

    if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/.test(raw)) {
      raw = raw + ':00';
    } else if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
      raw = raw + ' 00:00:00';
    }

    if (boundary === 'to' && /^\d{4}-\d{2}-\d{2} 00:00:00$/.test(raw)) {
      return raw.replace(' 00:00:00', ' 23:59:59');
    }

    return raw;
  }

  function readCalendarValue(el, boundary) {
    const input = resolveCalendarInput(el);

    if (!input) {
      debugDaterange('readCalendarValue: input not found', { boundary: boundary, el: el });
      return '';
    }

    let raw = '';
    let source = 'none';

    if (DevCraft.Metro.lib()) {
      const plugin = DevCraft.Metro.getPlugin(input, 'calendar-picker');

      if (plugin) {
        raw = formatCalendarPluginValue(plugin);
        source = 'plugin';

        if (!raw && plugin.element && typeof plugin.element.val === 'function') {
          raw = String(plugin.element.val() || '').trim();
          source = 'plugin.element.val';
        }
      } else {
        debugDaterange('readCalendarValue: no calendar plugin', describeCalendarInput(input));
      }
    }

    if (!raw && input.value) {
      raw = String(input.value).trim();
      source = 'input.value';
    }

    const normalized = normalizeFilterDateTime(raw, boundary);
    debugDaterange('readCalendarValue', {
      boundary: boundary,
      source: source,
      raw: raw,
      normalized: normalized,
      input: describeCalendarInput(input),
    });

    return normalized;
  }

  function appendFilterRulesToUrl(url, rules) {
    const keys = Array.from(url.searchParams.keys());

    keys.forEach(function (key) {
      if (key.indexOf('filter_rules') === 0) {
        url.searchParams.delete(key);
      }
    });

    rules.forEach(function (rule, index) {
      url.searchParams.set('filter_rules[' + index + '][field]', rule.field);
      url.searchParams.set('filter_rules[' + index + '][type]', rule.type);

      if (rule.type === 'daterange') {
        debugDaterange('appendFilterRulesToUrl: daterange rule', { index: index, rule: rule });
      }

      if (rule.value !== undefined && rule.value !== null) {
        if (Array.isArray(rule.value)) {
          rule.value.forEach(function (item) {
            url.searchParams.append('filter_rules[' + index + '][value][]', item);
          });
        } else {
          url.searchParams.set('filter_rules[' + index + '][value]', rule.value);
        }
      }

      if (rule.value_from) {
        url.searchParams.set('filter_rules[' + index + '][value_from]', rule.value_from);
      }

      if (rule.value_to) {
        url.searchParams.set('filter_rules[' + index + '][value_to]', rule.value_to);
      }
    });
  }

  function buildLogsPageUrl(overrides) {
    overrides = overrides || {};
    const url = new URL(window.location.href);
    url.searchParams.set('mod', 'devcraft');
    url.searchParams.set('action', 'logs');

    if (overrides.order) {
      url.searchParams.set('order', overrides.order);
    }

    if (overrides.sort) {
      url.searchParams.set('sort', overrides.sort);
    } else if (!url.searchParams.get('sort')) {
      url.searchParams.set('sort', 'DESC');
    }

    if (overrides.page === null) {
      url.searchParams.delete('page');
    } else if (overrides.page) {
      url.searchParams.set('page', String(overrides.page));
    }

    if (overrides.filter_rules) {
      appendFilterRulesToUrl(url, overrides.filter_rules);
    }

    return url.pathname + '?' + url.searchParams.toString();
  }

  function buildTableSourceUrl() {
    const base = DevCraftAjax.baseUrl();
    const url = new URL(base, window.location.origin);
    url.searchParams.set('controller', 'admin');
    url.searchParams.set('method', 'logs_table');
    url.searchParams.set('user_hash', DevCraftAjax.getUserHash());

    const pageUrl = new URL(window.location.href);
    const order = pageUrl.searchParams.get('order') || 'time';
    const sort = (pageUrl.searchParams.get('sort') || 'DESC').toUpperCase();

    url.searchParams.set('order', order);
    url.searchParams.set('sort', sort);

    const rules = parseFilterJson(
      document.getElementById('dc-filter-bar')?.dataset.filterRules,
      [],
    );

    appendFilterRulesToUrl(url, rules);

    if (rules.some(function (rule) { return rule.type === 'daterange'; })) {
      debugDaterange('buildTableSourceUrl', { url: url.toString(), rules: rules });
    }

    return url.toString();
  }

  function updateLogsTotalDisplay(total) {
    const el = document.getElementById('dc-logs-total');

    if (el) {
      const suffix = el.dataset.recordsSuffix || t('записей');
      el.textContent = total + ' ' + suffix;
    }
  }

  function getLogsTablePlugin() {
    const tableEl = document.getElementById('dc-logs-table');

    if (!tableEl || !DevCraft.Metro.lib() || typeof DevCraft.Metro.lib().getPlugin !== 'function') {
      return null;
    }

    return DevCraft.Metro.getPlugin(tableEl, 'table');
  }

  function reloadLogsTable() {
    const tableEl = document.getElementById('dc-logs-table');
    const plugin = getLogsTablePlugin();

    if (!tableEl || !plugin) {
      return;
    }

    const sourceUrl = buildTableSourceUrl();
    tableEl.setAttribute('data-source', sourceUrl);

    if (typeof plugin.setDataSourceUrl === 'function') {
      plugin.setDataSourceUrl(sourceUrl);
    }

    if (typeof plugin.loadData === 'function') {
      plugin.loadData(sourceUrl);
    } else if (typeof plugin.reload === 'function') {
      plugin.reload(function (data) {
        if (data && typeof data.total !== 'undefined') {
          updateLogsTotalDisplay(data.total);
        }
      });
    }
  }

  function escapeHtml(text) {
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function getMetroEventDetail(event) {
    return event && event.detail ? event.detail : {};
  }

  function normalizeLogsTableSourceUrl(url) {
    if (!url) {
      return '';
    }

    try {
      const parsed = new URL(url, window.location.origin);
      const params = new URLSearchParams(parsed.search);
      const sorted = Array.from(params.entries()).sort(function (left, right) {
        const keyCompare = left[0].localeCompare(right[0]);

        if (keyCompare !== 0) {
          return keyCompare;
        }

        return left[1].localeCompare(right[1]);
      });
      const normalized = new URLSearchParams();

      sorted.forEach(function (entry) {
        normalized.append(entry[0], entry[1]);
      });

      parsed.search = normalized.toString();

      return parsed.toString();
    } catch (error) {
      return String(url);
    }
  }

  function logsTableSourcesMatch(left, right) {
    return normalizeLogsTableSourceUrl(left) === normalizeLogsTableSourceUrl(right);
  }

  function bindLogsTableMetroEvents(tableEl) {
    const $table = DevCraftMetro.get$elements(tableEl);

    if (!$table || tableEl.dataset.logsMetroEventsBound === '1') {
      return;
    }

    tableEl.dataset.logsMetroEventsBound = '1';

    $table.on('data-loaded', function (event) {
      const detail = getMetroEventDetail(event);
      const data = detail.data || detail;

      if (data && typeof data.total !== 'undefined') {
        updateLogsTotalDisplay(data.total);
      } else if (data && Array.isArray(data.data)) {
        updateLogsTotalDisplay(data.data.length);
      }
    });

    $table.on('data-load-error', function (event) {
      const detail = getMetroEventDetail(event);
      const error = detail.error || detail;

      console.error('[DevCraft] Ошибка загрузки таблицы логов', error, detail.source || '');
    });
  }

  function formatFilterRuleSummary(rule, catalog) {
    const field = findCatalogField(catalog, rule.field);
    const label = field ? (field.label || rule.field) : rule.field;

    if (rule.type === 'daterange') {
      return label + ': ' + (rule.value_from || '') + ' — ' + (rule.value_to || '');
    }

    if (rule.type === 'range') {
      return label + ': ' + (rule.value_from || '') + ' — ' + (rule.value_to || '');
    }

    if (Array.isArray(rule.value)) {
      return label + ': ' + rule.value.join(', ');
    }

    return label + ': ' + (rule.value || '');
  }

  function rebuildFilterChipsDom(rules) {
    const bar = document.getElementById('dc-filter-bar');

    if (!bar) {
      return;
    }

    const catalog = parseFilterJson(bar.dataset.filterCatalog, { sections: [] });
    let chipsWrap = bar.querySelector('.dc-filter-chips');

    if (!chipsWrap) {
      chipsWrap = document.createElement('div');
      chipsWrap.className = 'dc-filter-chips mt-2 d-flex flex-wrap';
      bar.appendChild(chipsWrap);
    }

    chipsWrap.innerHTML = '';

    rules.forEach(function (rule, index) {
      const chip = document.createElement('div');
      chip.className = 'chip pill-chip m-1 dc-filter-chip';
      chip.setAttribute('data-filter-index', String(index));
      chip.setAttribute('data-filter-field', rule.field);
      chip.setAttribute('data-filter-type', rule.type);
      chip.tabIndex = 0;
      chip.innerHTML = '<div class="label">' + escapeHtml(formatFilterRuleSummary(rule, catalog)) + '</div>'
        + '<div class="action" role="button" tabindex="-1" title="' + t('Удалить') + '" aria-label="' + t('Удалить') + '"></div>';
      chipsWrap.appendChild(chip);
    });
  }

  function removeFilterChipAtIndex(index) {
    const bar = document.getElementById('dc-filter-bar');

    if (!bar || typeof index !== 'number' || Number.isNaN(index)) {
      return;
    }

    const filterRules = parseFilterJson(bar.dataset.filterRules, []);

    if (index < 0 || index >= filterRules.length) {
      return;
    }

    filterRules.splice(index, 1);
    DevCraft.Filter.activeChipIndex = null;
    applyFilterRules(filterRules);
  }

  function applyFilterRules(rules, options) {
    options = options || {};
    const bar = document.getElementById('dc-filter-bar');
    const rulesContainer = document.getElementById('dc-filter-hidden-rules');

    if (!bar) {
      return;
    }

    bar.dataset.filterRules = JSON.stringify(rules);

    if (rulesContainer) {
      rebuildFilterHiddenInputs(rulesContainer, rules);
    }

    rebuildFilterChipsDom(rules);

    if (rules.some(function (rule) { return rule.type === 'daterange'; })) {
      debugDaterange('applyFilterRules', { rules: rules });
    }

    const pageUrl = buildLogsPageUrl({
      filter_rules: rules,
      order: options.order,
      sort: options.sort,
      page: null,
    });

    if (options.replaceState) {
      history.replaceState({ filterRules: rules }, '', pageUrl);
    } else {
      history.pushState({ filterRules: rules }, '', pageUrl);
    }

    if (document.getElementById('dc-logs-table')) {
      reloadLogsTable();
    }
  }

  function submitFilterForm(form, rulesContainer, rules) {
    if (document.getElementById('dc-logs-table')) {
      applyFilterRules(rules);
      return;
    }

    rebuildFilterHiddenInputs(rulesContainer, rules);
    form.submit();
  }

  function readMetroSelectValue(selectEl) {
    if (!selectEl) {
      return '';
    }

    if (DevCraft.Metro.lib() && typeof DevCraft.Metro.lib().getPlugin === 'function') {
      const plugin = DevCraft.Metro.getPlugin(selectEl, 'select');

      if (plugin && typeof plugin.val === 'function') {
        const val = plugin.val();

        if (val !== undefined && val !== null && val !== '') {
          return Array.isArray(val) ? String(val[0] || '') : String(val);
        }
      }
    }

    return selectEl.value || '';
  }

  function initFilterDialogMetro(root) {
    if (!root || !DevCraft.Metro.lib()) {
      debugDaterange('initFilterDialogMetro: skip', { hasRoot: !!root, hasMetro: !!DevCraft.Metro.lib() });
      return;
    }

    const calendarInputs = root.querySelectorAll('input[data-role="calendar-picker"]');
    debugDaterange('initFilterDialogMetro', { root: root, calendarCount: calendarInputs.length });

    root.querySelectorAll('select[data-role="select"]').forEach(function (el) {
      if (!DevCraft.Metro.getPlugin(el, 'select')) {
        DevCraft.Metro.makePlugin(el, 'select');
      }
    });

    root.querySelectorAll('input[data-role="calendar-picker"]').forEach(function (el) {
      if (!DevCraft.Metro.getPlugin(el, 'calendar-picker')) {
        DevCraft.Metro.makePlugin(el, 'calendar-picker');
      }

      syncCalendarPickerValue(el);
    });

    root.querySelectorAll('input[data-role="input"]').forEach(function (el) {
      if (!DevCraft.Metro.getPlugin(el, 'input')) {
        DevCraft.Metro.makePlugin(el, 'input');
      }
    });
  }

  function appendFilterFieldLabel(container, text) {
    const label = document.createElement('label');
    label.className = 'form-label mb-1';
    label.textContent = text;
    container.appendChild(label);
  }

  function createFilterDateTimePicker(className, value, minDate, maxDate) {
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'metro-input w-100 ' + className;
    input.value = value || '';
    input.setAttribute('data-role', 'calendar-picker');
    input.setAttribute('data-show-time', 'true');
    input.setAttribute('data-dialog-mode', 'true');
    input.setAttribute('data-format', 'YYYY-MM-DD');
    input.setAttribute('data-input-format', 'YYYY-MM-DD');
    input.setAttribute('data-locale', 'en');
    input.setAttribute(
      'data-dc-filter-boundary',
      className.indexOf('-from') !== -1 ? 'from' : 'to',
    );

    if (value) {
      input.setAttribute('data-value', value);
    }

    if (minDate) {
      input.setAttribute('data-min-date', minDate);
    }

    if (maxDate) {
      input.setAttribute('data-max-date', maxDate);
    }

    debugDaterange('createFilterDateTimePicker', {
      className: className,
      value: value,
      minDate: minDate,
      maxDate: maxDate,
    });

    return input;
  }

  function renderFilterDialogFields(container, field, draft) {
    container.innerHTML = '';
    draft = draft || {};

    if (!field) {
      return;
    }

    const fieldLabel = field.label || field.id || '';

    if (field.type === 'multi') {
      const options = field.options || {};
      const selected = Array.isArray(draft.value) ? draft.value : [];
      const wrap = document.createElement('div');
      wrap.className = 'form-group';
      let optionCount = 0;

      appendFilterFieldLabel(wrap, fieldLabel);

      const select = document.createElement('select');
      select.className = 'dc-filter-multi-select w-100';
      select.multiple = true;
      select.setAttribute('data-role', 'select');
      select.setAttribute('data-filter', 'true');
      select.setAttribute('data-clear-button', 'true');
      select.setAttribute('data-drop-height', '280');

      Object.keys(options).forEach(function (key) {
        if (key === '') {
          return;
        }

        optionCount++;
        const option = document.createElement('option');
        option.value = key;
        option.textContent = options[key];

        if (selected.indexOf(key) !== -1) {
          option.selected = true;
        }

        select.appendChild(option);
      });

      if (optionCount === 0) {
        const empty = document.createElement('p');
        empty.className = 'text-small fg-gray';
        empty.textContent = t('Нет доступных значений для этого поля.');
        wrap.appendChild(empty);
      } else {
        wrap.appendChild(select);
      }

      container.appendChild(wrap);
      return;
    }

    if (field.type === 'text') {
      const wrap = document.createElement('div');
      wrap.className = 'form-group';
      appendFilterFieldLabel(wrap, fieldLabel);

      const input = document.createElement('input');
      input.type = 'text';
      input.className = 'metro-input w-100 dc-filter-text-value';
      input.value = draft.value || '';
      input.setAttribute('data-role', 'input');
      wrap.appendChild(input);
      container.appendChild(wrap);
      return;
    }

    if (field.type === 'daterange') {
      const wrap = document.createElement('div');
      wrap.className = 'form-group';
      const minDate = field.min || '';
      const maxDate = field.max || '';

      debugDaterange('renderFilterDialogFields: daterange', {
        fieldId: field.id,
        minDate: minDate,
        maxDate: maxDate,
        draft: draft,
      });

      const fromGroup = document.createElement('div');
      fromGroup.className = 'mb-2';
      appendFilterFieldLabel(fromGroup, fieldLabel + ' (от)');
      fromGroup.appendChild(createFilterDateTimePicker(
        'dc-filter-date-from',
        draft.value_from || minDate,
        minDate,
        maxDate,
      ));
      wrap.appendChild(fromGroup);

      const toGroup = document.createElement('div');
      appendFilterFieldLabel(toGroup, fieldLabel + ' (до)');
      toGroup.appendChild(createFilterDateTimePicker(
        'dc-filter-date-to',
        draft.value_to || maxDate,
        minDate,
        maxDate,
      ));
      wrap.appendChild(toGroup);

      container.appendChild(wrap);
      return;
    }

    if (field.type === 'range') {
      const wrap = document.createElement('div');
      wrap.className = 'form-group';

      const fromGroup = document.createElement('div');
      fromGroup.className = 'mb-2';
      appendFilterFieldLabel(fromGroup, fieldLabel + ' (от)');

      const from = document.createElement('input');
      from.type = 'number';
      from.className = 'metro-input w-100 dc-filter-range-from';
      from.value = draft.value_from || field.from || '';
      fromGroup.appendChild(from);
      wrap.appendChild(fromGroup);

      const toGroup = document.createElement('div');
      appendFilterFieldLabel(toGroup, fieldLabel + ' (до)');

      const to = document.createElement('input');
      to.type = 'number';
      to.className = 'metro-input w-100 dc-filter-range-to';
      to.value = draft.value_to || field.to || '';
      toGroup.appendChild(to);
      wrap.appendChild(toGroup);

      container.appendChild(wrap);
    }
  }

  function readScopedMultiSelectValues(scope) {
    const select = scope.querySelector('select.dc-filter-multi-select');

    if (!select) {
      return [];
    }

    if (DevCraft.Metro.lib() && typeof DevCraft.Metro.lib().getPlugin === 'function') {
      const plugin = DevCraft.Metro.getPlugin(select, 'select');

      if (plugin && typeof plugin.getSelected === 'function') {
        const selected = plugin.getSelected();

        if (Array.isArray(selected)) {
          return selected.filter(function (value) {
            return value !== '' && value != null;
          });
        }
      }
    }

    return Array.from(select.selectedOptions)
      .map(function (option) { return option.value; })
      .filter(function (value) { return value !== ''; });
  }

  function readScopedInputValue(scope, selector) {
    const input = scope.querySelector(selector);

    if (!input || input.value == null) {
      return '';
    }

    return String(input.value).trim();
  }

  function collectFilterDialogRule(field, fieldId, root) {
    if (!field) {
      return null;
    }

    const scope = root || document;

    if (field.type === 'multi') {
      const values = readScopedMultiSelectValues(scope);

      if (!values.length) {
        return null;
      }

      return { field: fieldId, type: 'multi', value: values };
    }

    if (field.type === 'text') {
      const value = readScopedInputValue(scope, 'input.dc-filter-text-value');

      if (!value) {
        return null;
      }

      return { field: fieldId, type: 'text', value: value };
    }

    if (field.type === 'daterange') {
      const fromEl = findCalendarPickerInput(scope, 'from');
      const toEl = findCalendarPickerInput(scope, 'to');
      const valueFrom = readCalendarValue(fromEl, 'from');
      const valueTo = readCalendarValue(toEl, 'to');

      debugDaterange('collectFilterDialogRule: daterange', {
        fieldId: fieldId,
        scope: scope,
        fromFound: !!fromEl,
        toFound: !!toEl,
        valueFrom: valueFrom,
        valueTo: valueTo,
      });

      if (!valueFrom || !valueTo) {
        debugDaterange('collectFilterDialogRule: FAIL empty values', {
          valueFrom: valueFrom,
          valueTo: valueTo,
        });
        return null;
      }

      if (valueFrom > valueTo) {
        debugDaterange('collectFilterDialogRule: FAIL from > to', {
          valueFrom: valueFrom,
          valueTo: valueTo,
        });
        return null;
      }

      const rule = { field: fieldId, type: 'daterange', value_from: valueFrom, value_to: valueTo };
      debugDaterange('collectFilterDialogRule: OK', rule);
      return rule;
    }

    if (field.type === 'range') {
      const valueFrom = readScopedInputValue(scope, 'input.dc-filter-range-from');
      const valueTo = readScopedInputValue(scope, 'input.dc-filter-range-to');

      if (!valueFrom || !valueTo) {
        return null;
      }

      return { field: fieldId, type: 'range', value_from: valueFrom, value_to: valueTo };
    }

    return null;
  }

  function defaultCatalogFieldId(catalog) {
    const sections = catalog.sections || [];

    for (let i = 0; i < sections.length; i++) {
      const fields = sections[i].fields || [];

      if (fields.length > 0 && fields[0].id) {
        return fields[0].id;
      }
    }

    return '';
  }

  function buildFilterFieldSelectHtml(catalog, selectedId) {
    let html = '<option value="">— выберите поле —</option>';
    const sections = catalog.sections || [];

    sections.forEach(function (section) {
      (section.fields || []).forEach(function (field) {
        const selected = field.id === selectedId ? ' selected' : '';
        html += '<option value="' + field.id + '"' + selected + '>' + field.label + '</option>';
      });
    });

    return html;
  }

  function buildFilterDialogContent(catalog, selectedId) {
    return '<div class="dc-filter-dialog-body">'
      + '<div class="form-group">'
      + '<label for="dc-filter-field-select">Поле</label>'
      + '<select id="dc-filter-field-select" data-role="select" data-filter="false">'
      + buildFilterFieldSelectHtml(catalog, selectedId)
      + '</select></div>'
      + '<div class="dc-filter-dialog-fields mt-3"></div>'
      + '<div class="dialog-actions text-right">'
      + '<button type="button" class="button js-dialog-close">Отмена</button>'
      + '<button type="button" class="button primary ml-2" data-action="filter-dialog-apply">Применить</button>'
      + '</div>'
      + '</div>';
  }

  function wireFilterDialogBody(el, catalog, field, draft) {
    const scope = getFilterDialogScope(el);
    const fieldsContainer = scope.querySelector('.dc-filter-dialog-fields');
    const fieldSelect = scope.querySelector('#dc-filter-field-select');

    renderFilterDialogFields(fieldsContainer, field, draft);
    initFilterDialogMetro(scope);

    if (!fieldSelect) {
      return;
    }

    fieldSelect.addEventListener('change', function () {
      const selectedId = readMetroSelectValue(fieldSelect);
      const nextField = findCatalogField(catalog, selectedId);
      renderFilterDialogFields(fieldsContainer, nextField, {});

      if (fieldsContainer) {
        initFilterDialogMetro(fieldsContainer);
      }
    });
  }

  function showFilterRuleDialog(catalog, draft, editIndex, onApply) {
    draft = draft || {};
    const fieldId = draft.field || defaultCatalogFieldId(catalog);
    const field = findCatalogField(catalog, fieldId);
    const el = document.getElementById('dc-filter-dialog');

    if (!el || !DevCraft.Metro.lib()) {
      DevCraftMetro.notify(t('Фильтр'), t('Metro UI или #dc-filter-dialog недоступен'), 'warning');
      return;
    }

    DevCraft.Filter.applyDialogRule = function () {
      const dlgEl = document.getElementById('dc-filter-dialog');

      if (!dlgEl) {
        debugDaterange('applyDialogRule: dialog not found');
        return;
      }

      const scope = getFilterDialogScope(dlgEl);
      const fieldSelect = dlgEl.querySelector('#dc-filter-field-select');
      const selectedFieldId = readMetroSelectValue(fieldSelect);
      const selectedField = findCatalogField(catalog, selectedFieldId);

      debugDaterange('applyDialogRule: start', {
        selectedFieldId: selectedFieldId,
        selectedFieldType: selectedField ? selectedField.type : null,
        scope: scope,
      });

      const rule = collectFilterDialogRule(selectedField, selectedFieldId, scope);

      if (!rule) {
        debugDaterange('applyDialogRule: FAIL no rule', dumpDaterangeState());
        const msg = selectedField && selectedField.type === 'daterange'
          ? t('Укажите корректный диапазон дат (от и до)')
          : t('Выберите значение для правила фильтра');
        DevCraftMetro.notify(t('Фильтр'), msg, 'warning');
        return;
      }

      debugDaterange('applyDialogRule: OK', rule);
      onApply(rule, editIndex);
      DevCraftMetro.dialogClose('#dc-filter-dialog');
    };

    const plugin = DevCraftMetro.dialogGetPlugin(el);

    if (!plugin) {
      DevCraftMetro.notify(t('Фильтр'), t('Плагин dialog не инициализирован'), 'warning');
      return;
    }

    const content = buildFilterDialogContent(catalog, fieldId);

    plugin.setTitle(t('Правило фильтра'));
    plugin.setContent(content);
    wireFilterDialogBody(el, catalog, field, draft);
    plugin.open();
  }

  function openFilterRuleDialogFromDom(index) {
    const bar = document.getElementById('dc-filter-bar');
    const form = document.getElementById('dc-filter-form');
    const rulesContainer = document.getElementById('dc-filter-hidden-rules');

    if (!bar || !form || !rulesContainer) {
      DevCraftMetro.notify(t('Фильтр'), t('Панель фильтра не найдена'), 'warning');
      return;
    }

    const catalog = parseFilterJson(bar.dataset.filterCatalog, { sections: [] });
    const filterRules = parseFilterJson(bar.dataset.filterRules, []);
    const editIndex = typeof index === 'number' ? index : null;
    const draft = editIndex !== null ? (filterRules[editIndex] || {}) : {};

    showFilterRuleDialog(catalog, draft, editIndex, function (rule, idx) {
      if (typeof idx === 'number') {
        filterRules[idx] = rule;
      } else {
        filterRules.push(rule);
      }

      submitFilterForm(form, rulesContainer, filterRules);
    });
  }

  function clearFilterRulesFromDom() {
    const form = document.getElementById('dc-filter-form');
    const rulesContainer = document.getElementById('dc-filter-hidden-rules');

    if (!form || !rulesContainer) {
      DevCraftMetro.notify(t('Фильтр'), t('Панель фильтра не найдена'), 'warning');
      return;
    }

    submitFilterForm(form, rulesContainer, []);
  }

  function removeActiveFilterChipFromDom() {
    const index = DevCraft.Filter.activeChipIndex;

    if (typeof index !== 'number') {
      return;
    }

    removeFilterChipAtIndex(index);
  }

  DevCraft.Filter.activeChipIndex = null;

  function handleFilterBarClick(event) {
    const actionEl = event.target.closest('[data-action]');

    if (!actionEl) {
      return;
    }

    const action = actionEl.getAttribute('data-action');

    if (action === 'filter-create') {
      event.preventDefault();
      event.stopPropagation();
      openFilterRuleDialogFromDom(null);
      return;
    }

    if (action === 'filter-clear-all') {
      event.preventDefault();
      event.stopPropagation();
      clearFilterRulesFromDom();
      return;
    }

    if (action === 'filter-chip-edit') {
      event.preventDefault();
      event.stopPropagation();
      closeChipMenu();

      if (typeof DevCraft.Filter.activeChipIndex === 'number') {
        openFilterRuleDialogFromDom(DevCraft.Filter.activeChipIndex);
      }

      return;
    }

    if (action === 'filter-chip-remove') {
      event.preventDefault();
      event.stopPropagation();
      closeChipMenu();
      removeActiveFilterChipFromDom();
    }
  }

  function openChipMenu(chip, chipMenu) {
    const index = parseInt(chip.getAttribute('data-filter-index'), 10);

    if (!chipMenu || Number.isNaN(index)) {
      return;
    }

    DevCraft.Filter.activeChipIndex = index;
    chipMenu.classList.remove('d-none');

    if (!DevCraft.Metro.lib() || typeof DevCraft.Metro.lib().makePlugin !== 'function') {
      openChipMenuFallback(chip, chipMenu);
      return;
    }

    let plugin = DevCraft.Metro.getPlugin(chipMenu, 'dropdown');

    if (!plugin) {
      chipMenu.setAttribute('data-role', 'dropdown');
      plugin = DevCraft.Metro.makePlugin(chipMenu, 'dropdown', {
        toggleElement: chip,
        stayOnClick: true,
      });
    } else {
      const $toggle = typeof DevCraft.Metro.lib().get$elements === 'function'
        ? DevCraft.Metro.get$elements(chip)
        : null;

      if ($toggle && $toggle.length) {
        plugin.toggle = $toggle;
      }

      if (plugin.options) {
        plugin.options.toggleElement = chip;
      }
    }

    if (plugin && typeof plugin.open === 'function') {
      plugin.open(true);
      return;
    }

    openChipMenuFallback(chip, chipMenu);
  }

  function openChipMenuFallback(chip, chipMenu) {
    const rect = chip.getBoundingClientRect();

    chipMenu.style.position = 'fixed';
    chipMenu.style.left = Math.round(rect.left) + 'px';
    chipMenu.style.top = Math.round(rect.bottom + 4) + 'px';
    chipMenu.style.display = 'block';
    chipMenu.classList.add('keep-open');

    if (DevCraft.Filter._chipMenuCloseHandler) {
      document.removeEventListener('click', DevCraft.Filter._chipMenuCloseHandler, true);
    }

    DevCraft.Filter._chipMenuCloseHandler = function (event) {
      if (chipMenu.contains(event.target) || chip.contains(event.target)) {
        return;
      }

      chipMenu.style.display = 'none';
      chipMenu.classList.remove('keep-open');
      document.removeEventListener('click', DevCraft.Filter._chipMenuCloseHandler, true);
      DevCraft.Filter._chipMenuCloseHandler = null;
    };

    setTimeout(function () {
      document.addEventListener('click', DevCraft.Filter._chipMenuCloseHandler, true);
    }, 0);
  }

  function closeChipMenu() {
    const chipMenu = document.getElementById('dc-filter-chip-menu');

    if (!chipMenu) {
      return;
    }

    const plugin = DevCraft.Metro.lib() && typeof DevCraft.Metro.lib().getPlugin === 'function'
      ? DevCraft.Metro.getPlugin(chipMenu, 'dropdown')
      : null;

    if (plugin && typeof plugin.close === 'function') {
      plugin.close(true);
    }

    chipMenu.style.display = 'none';
    chipMenu.classList.remove('keep-open');

    if (DevCraft.Filter._chipMenuCloseHandler) {
      document.removeEventListener('click', DevCraft.Filter._chipMenuCloseHandler, true);
      DevCraft.Filter._chipMenuCloseHandler = null;
    }
  }

  function initFilterBar() {
    const bar = document.getElementById('dc-filter-bar');

    if (!bar) {
      return;
    }

    const filterToggle = document.getElementById('dc-filter-toggle');

    if (filterToggle) {
      filterToggle.querySelectorAll('.dropdown-caret').forEach(function (caret, index) {
        if (index > 0) {
          caret.remove();
        }
      });
    }

    const chipMenu = document.getElementById('dc-filter-chip-menu');

    DevCraft.Filter.activeChipIndex = null;

    bar.addEventListener('contextmenu', function (event) {
      const chip = event.target.closest('.dc-filter-chip');

      if (!chip) {
        return;
      }

      event.preventDefault();
      openChipMenu(chip, chipMenu);
    });

    bar.addEventListener('click', function (event) {
      const actionBtn = event.target.closest('.dc-filter-chip .action');

      if (actionBtn) {
        event.preventDefault();
        event.stopPropagation();

        const chip = actionBtn.closest('.dc-filter-chip');

        if (!chip) {
          return;
        }

        const index = parseInt(chip.getAttribute('data-filter-index'), 10);

        if (!Number.isNaN(index)) {
          removeFilterChipAtIndex(index);
        }

        return;
      }

      const chip = event.target.closest('.dc-filter-chip');

      if (!chip) {
        return;
      }

      event.preventDefault();

      const index = parseInt(chip.getAttribute('data-filter-index'), 10);

      if (!Number.isNaN(index)) {
        openFilterRuleDialogFromDom(index);
      }
    });
  }

  function initLogsTable() {
    const tableEl = document.getElementById('dc-logs-table');

    if (!tableEl) {
      return;
    }

    if (!tableEl.dataset.logsTableBound) {
      tableEl.dataset.logsTableBound = '1';
      bindLogsTableMetroEvents(tableEl);
    }

    const sourceUrl = buildTableSourceUrl();
    tableEl.setAttribute('data-source', sourceUrl);

    const plugin = getLogsTablePlugin();

    if (!plugin) {
      DevCraft.Metro.makePlugin(tableEl, 'table');
      return;
    }

    const existingSource = (plugin.options && plugin.options.source) || tableEl.getAttribute('data-source') || '';

    if (plugin.options) {
      plugin.options.source = sourceUrl;
    }

    if (!logsTableSourcesMatch(existingSource, sourceUrl) && typeof plugin.loadData === 'function') {
      plugin.loadData(sourceUrl);
    }
  }

  function syncFilterStateFromUrl() {
    const bar = document.getElementById('dc-filter-bar');

    if (!bar) {
      return;
    }

    const pageUrl = new URL(window.location.href);
    const rules = [];
    const ruleMap = {};

    pageUrl.searchParams.forEach(function (value, key) {
      const match = key.match(/^filter_rules\[(\d+)\]\[(\w+)\](\[\])?$/);

      if (!match) {
        return;
      }

      const index = match[1];
      const field = match[2];

      if (!ruleMap[index]) {
        ruleMap[index] = {};
      }

      if (field === 'value' && match[3]) {
        if (!Array.isArray(ruleMap[index].value)) {
          ruleMap[index].value = [];
        }

        ruleMap[index].value.push(value);
      } else {
        ruleMap[index][field] = value;
      }
    });

    Object.keys(ruleMap).sort(function (a, b) {
      return parseInt(a, 10) - parseInt(b, 10);
    }).forEach(function (key) {
      rules.push(ruleMap[key]);
    });

    bar.dataset.filterRules = JSON.stringify(rules);
    rebuildFilterChipsDom(rules);

    const rulesContainer = document.getElementById('dc-filter-hidden-rules');

    if (rulesContainer) {
      rebuildFilterHiddenInputs(rulesContainer, rules);
    }
  }

  function initPopstate() {
    window.addEventListener('popstate', function () {
      if (!document.getElementById('dc-logs-table')) {
        return;
      }

      syncFilterStateFromUrl();
      reloadLogsTable();
    });
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

  document.addEventListener('click', handleFilterBarClick, true);

  function _bindDocumentClick() {
    document.addEventListener('click', function (event) {
      const filterApplyBtn = event.target.closest('[data-action="filter-dialog-apply"]');

      if (filterApplyBtn && typeof DevCraft.Filter.applyDialogRule === 'function') {
        event.preventDefault();
        DevCraft.Filter.applyDialogRule();
        return;
      }

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

      const composerActionBtn = event.target.closest('.js-composer-action');
      if (composerActionBtn) {
        runComposerAction(composerActionBtn.dataset.actionType || '', composerActionBtn.dataset.package || '');
        return;
      }

      const dumpAutoloadBtn = event.target.closest('.js-dump-autoload');
      if (dumpAutoloadBtn) {
        runDumpAutoload();
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
              const plugin = getLogsTablePlugin();

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

  DevCraftMetro.selectVal = readMetroSelectValue;
  DevCraftMetro.calendarVal = readCalendarValue;
  DevCraftMetro.initFilterDialog = initFilterDialogMetro;
  DevCraftMetro.getLogsTablePlugin = getLogsTablePlugin;
  DevCraftFilter.parseJson = parseFilterJson;
  DevCraftFilter.findCatalogField = findCatalogField;
  DevCraftFilter.rebuildHiddenInputs = rebuildFilterHiddenInputs;
  DevCraftFilter.appendHidden = appendHidden;
  DevCraftFilter.appendRulesToUrl = appendFilterRulesToUrl;
  DevCraftFilter.buildLogsPageUrl = buildLogsPageUrl;
  DevCraftFilter.buildTableSourceUrl = buildTableSourceUrl;
  DevCraftFilter.updateLogsTotalDisplay = updateLogsTotalDisplay;
  DevCraftFilter.reloadLogsTable = reloadLogsTable;
  DevCraftFilter.escapeHtml = escapeHtml;
  DevCraftFilter.formatRuleSummary = formatFilterRuleSummary;
  DevCraftFilter.rebuildChipsDom = rebuildFilterChipsDom;
  DevCraftFilter.removeChipAtIndex = removeFilterChipAtIndex;
  DevCraftFilter.applyRules = applyFilterRules;
  DevCraftFilter.submitForm = submitFilterForm;
  DevCraftFilter.appendFieldLabel = appendFilterFieldLabel;
  DevCraftFilter.createDateTimePicker = createFilterDateTimePicker;
  DevCraftFilter.renderDialogFields = renderFilterDialogFields;
  DevCraftFilter.readScopedMultiSelectValues = readScopedMultiSelectValues;
  DevCraftFilter.readScopedInputValue = readScopedInputValue;
  DevCraftFilter.dumpDaterangeState = dumpDaterangeState;
  DevCraftFilter.debugDaterange = debugDaterange;
  DevCraftFilter.isDaterangeDebugEnabled = function () { return DevCraft.Debug.isEnabled(); };
  DevCraftFilter.collectDialogRule = collectFilterDialogRule;
  DevCraftFilter.defaultCatalogFieldId = defaultCatalogFieldId;
  DevCraftFilter.buildFieldSelectHtml = buildFilterFieldSelectHtml;
  DevCraftFilter.buildDialogContent = buildFilterDialogContent;
  DevCraftFilter.wireDialogBody = wireFilterDialogBody;
  DevCraftFilter.showRuleDialog = showFilterRuleDialog;
  DevCraftFilter.openRuleDialog = openFilterRuleDialogFromDom;
  DevCraftFilter.clearRules = clearFilterRulesFromDom;
  DevCraftFilter.removeActiveChip = removeActiveFilterChipFromDom;
  DevCraftFilter.handleBarClick = handleFilterBarClick;
  DevCraftFilter.openChipMenu = openChipMenu;
  DevCraftFilter.openChipMenuFallback = openChipMenuFallback;
  DevCraftFilter.closeChipMenu = closeChipMenu;
  DevCraftFilter.initBar = initFilterBar;
  DevCraftFilter.initLogsTable = initLogsTable;
  DevCraftFilter.syncStateFromUrl = syncFilterStateFromUrl;
  DevCraftFilter.initPopstate = initPopstate;
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
  DevCraftComposer.runAction = runComposerAction;
  DevCraftComposer.runDumpAutoload = runDumpAutoload;
  DevCraftComposer.runSync = runComposerSync;

  const DevCraftAdmin = {
    Filter: DevCraftFilter,
    Assets: DevCraftAssets,
    Dashboard: DevCraftDashboard,
    Composer: DevCraftComposer,
    boot() {
      DevCraftAdmin.Dashboard.init();
      DevCraftAdmin.Filter.initBar();
      DevCraftAdmin.Filter.initLogsTable();
      DevCraftAdmin.Composer.initTable();
      DevCraftAdmin.Filter.initPopstate();
      if (DevCraft.Debug.isEnabled()) {
        DevCraft.Debug.log('Filter', 'boot', { message: t('Модуль Admin инициализирован') });
      }
    },
  };

  DevCraftAdmin.Dashboard.bindDocumentClick();
  global.DevCraftAdmin = DevCraftAdmin;
  global.DevCraft.Filter = DevCraftAdmin.Filter;
  global.DevCraft.Assets = DevCraftAdmin.Assets;
  global.DevCraft.Dashboard = DevCraftAdmin.Dashboard;
  global.DevCraft.Composer = DevCraftAdmin.Composer;

})(window);
