/**
 * Управление Composer в админке DevCraft (install / update / remove / dump-autoload).
 * Подключается из шаблонов с Composer UI; зависит от devcraft.js.
 */
(function (global) {
  'use strict';

  let busy = false;

  function t(phrase, params) {
    return typeof global.__ === 'function' ? global.__(phrase, params || {}) : phrase;
  }

  function escapeHtml(text) {
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function ajax() {
    return global.DevCraft && global.DevCraft.Ajax ? global.DevCraft.Ajax : null;
  }

  function metro() {
    return global.DevCraft && global.DevCraft.Metro ? global.DevCraft.Metro : null;
  }

  function metroLib() {
    const m = metro();
    return m && typeof m.lib === 'function' ? m.lib() : (global.Metro || null);
  }

  function actionLabel(actionType) {
    switch (actionType) {
      case 'install':
        return t('Установка пакета…');
      case 'update':
        return t('Обновление пакета…');
      case 'remove':
        return t('Удаление пакета…');
      default:
        return t('Выполнение Composer…');
    }
  }

  function openActivity(text) {
    const lib = metroLib();
    if (!lib || !lib.activity || typeof lib.activity.open !== 'function') {
      return null;
    }

    return lib.activity.open({
      type: 'cycle',
      text: text,
      overlayClickClose: false,
    });
  }

  function closeActivity(activity) {
    const lib = metroLib();
    if (!activity || !lib || !lib.activity || typeof lib.activity.close !== 'function') {
      return;
    }

    lib.activity.close(activity);
  }

  function getComposerTablePlugin() {
    const tableEl = document.getElementById('dc-composer-table');
    const m = metro();
    if (!tableEl || !m) {
      return null;
    }
    return m.getPlugin(tableEl, 'table');
  }

  function runComposerAction(actionType, packageName, version, triggerBtn) {
    const DevCraftAjax = ajax();
    const DevCraftMetro = metro();

    if (!DevCraftAjax || typeof DevCraftAjax.post !== 'function') {
      console.error('[DevCraft] DevCraft.Ajax недоступен — сначала загрузите devcraft.js');
      return;
    }

    if (busy) {
      return;
    }

    busy = true;
    if (triggerBtn) {
      triggerBtn.disabled = true;
    }

    const activity = openActivity(actionLabel(actionType));
    const payload = {
      actionType: actionType,
      packageName: packageName,
    };

    if (version) {
      payload.version = version;
    }

    DevCraftAjax.post('composer_action', payload)
      .then(function (response) {
        DevCraftAjax.handleNotice(response);
        if (response.success) {
          const table = getComposerTablePlugin();
          if (table && typeof table.reload === 'function') {
            table.reload();
          }
        } else if (response.error && response.error.detail && response.error.detail.output) {
          if (DevCraftMetro && typeof DevCraftMetro.dialogCreate === 'function') {
            DevCraftMetro.dialogCreate({
              title: t('Ошибка Composer'),
              content: '<pre class="text-small">' + escapeHtml(String(response.error.detail.output)) + '</pre>',
              customButtons: [
                {
                  text: t('Повторить'),
                  cls: 'warning',
                  onclick: function () {
                    runComposerAction(actionType, packageName, version, triggerBtn);
                  },
                },
                { text: t('Закрыть'), cls: 'js-dialog-close', onclick: function () {} },
              ],
            });
          }
        }
      })
      .catch(function (err) {
        if (DevCraftMetro && typeof DevCraftMetro.notifyError === 'function') {
          DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось выполнить Composer-действие'), err);
        }
      })
      .finally(function () {
        closeActivity(activity);
        busy = false;
        if (triggerBtn) {
          triggerBtn.disabled = false;
        }
      });
  }

  function runDumpAutoload(triggerBtn) {
    const DevCraftAjax = ajax();
    const DevCraftMetro = metro();

    if (!DevCraftAjax || typeof DevCraftAjax.post !== 'function') {
      console.error('[DevCraft] DevCraft.Ajax недоступен — сначала загрузите devcraft.js');
      return;
    }

    if (busy) {
      return;
    }

    busy = true;
    if (triggerBtn) {
      triggerBtn.disabled = true;
    }

    const activity = openActivity(t('Выполнение dump-autoload…'));

    DevCraftAjax.post('dump_autoload', {})
      .then(function (response) {
        DevCraftAjax.handleNotice(response);
      })
      .catch(function (err) {
        if (DevCraftMetro && typeof DevCraftMetro.notifyError === 'function') {
          DevCraftMetro.notifyError(t('Ошибка'), t('Не удалось выполнить dump-autoload'), err);
        }
      })
      .finally(function () {
        closeActivity(activity);
        busy = false;
        if (triggerBtn) {
          triggerBtn.disabled = false;
        }
      });
  }

  function onDocumentClick(event) {
    const actionBtn = event.target.closest('.js-composer-action');
    if (actionBtn) {
      runComposerAction(
        actionBtn.dataset.actionType || '',
        actionBtn.dataset.package || '',
        actionBtn.dataset.version || '',
        actionBtn
      );
      return;
    }

    const dumpBtn = event.target.closest('.js-dump-autoload');
    if (dumpBtn) {
      runDumpAutoload(dumpBtn);
    }
  }

  document.addEventListener('click', onDocumentClick);

  global.DevCraft = global.DevCraft || {};
  global.DevCraft.Composer = global.DevCraft.Composer || {};
  global.DevCraft.Composer.runAction = runComposerAction;
  global.DevCraft.Composer.runDumpAutoload = runDumpAutoload;
})(window);
