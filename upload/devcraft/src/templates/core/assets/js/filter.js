/**
 * DevCraft Filter UI — общий для всех модулей с filter_bar.
 * Загружается из layout после devcraft.js.
 */
(function (global) {
  'use strict';

  if (!global.DevCraft) {
    console.error('[DevCraft] Core devcraft.js muss vor filter.js geladen werden.');
    return;
  }

  const DevCraft = global.DevCraft;
  const DevCraftMetro = DevCraft.Metro;
  const DevCraftAjax = DevCraft.Ajax;
  const t = function (phrase, params) {
    return DevCraft.__(phrase, params || {});
  };

  const DevCraftFilter = global.DevCraft.Filter || {};
  DevCraftFilter.activeChipIndex = null;
  DevCraftFilter._chipMenuCloseHandler = null;

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
      tableSourceUrl: hasAjaxFilterTable() ? buildTableSourceUrl() : null,
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

  function readFormFieldValue(form, name) {
    if (!form) {
      return '';
    }

    const input = form.querySelector('[name="' + name + '"]');

    return input ? String(input.value || '') : '';
  }

  function getFilterFormContext() {
    const form = document.getElementById('dc-filter-form');
    const bar = document.getElementById('dc-filter-bar');
    const pageUrl = new URL(window.location.href);
    const mod = (form && (form.dataset.mod || readFormFieldValue(form, 'mod')))
      || pageUrl.searchParams.get('mod')
      || 'devcraft';
    const action = (form && (form.dataset.action || readFormFieldValue(form, 'action')))
      || pageUrl.searchParams.get('action')
      || 'logs';

    return {
      form: form,
      bar: bar,
      mod: mod,
      action: action,
    };
  }

  function getFilterTableEl() {
    const bar = document.getElementById('dc-filter-bar');

    if (bar && bar.dataset.tableId) {
      const byId = document.getElementById(bar.dataset.tableId);

      if (byId) {
        return byId;
      }
    }

    return document.querySelector('[data-role="table"][data-dc-table-method]');
  }

  function hasAjaxFilterTable() {
    const tableEl = getFilterTableEl();

    return !!(tableEl && tableEl.getAttribute('data-dc-table-method'));
  }

  function resolveFilterOrderSort(tableEl) {
    const form = document.getElementById('dc-filter-form');
    const pageUrl = new URL(window.location.href);
    const defaultOrder = (tableEl && tableEl.dataset.defaultOrder) || 'time';
    const defaultSort = (tableEl && tableEl.dataset.defaultSort) || 'DESC';
    const order = pageUrl.searchParams.get('order')
      || readFormFieldValue(form, 'order')
      || defaultOrder;
    const sort = (pageUrl.searchParams.get('sort')
      || readFormFieldValue(form, 'sort')
      || defaultSort).toUpperCase();

    return { order: order, sort: sort };
  }

  function buildPageUrl(overrides) {
    overrides = overrides || {};
    const ctx = getFilterFormContext();
    const url = new URL(window.location.href);
    const tableEl = getFilterTableEl();
    const defaults = resolveFilterOrderSort(tableEl);

    url.searchParams.set('mod', overrides.mod || ctx.mod);
    url.searchParams.set('action', overrides.action || ctx.action);

    if (overrides.order) {
      url.searchParams.set('order', overrides.order);
    } else if (!url.searchParams.get('order') && defaults.order) {
      url.searchParams.set('order', defaults.order);
    }

    if (overrides.sort) {
      url.searchParams.set('sort', overrides.sort);
    } else if (!url.searchParams.get('sort')) {
      url.searchParams.set('sort', defaults.sort);
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

  /** @deprecated используйте buildPageUrl */
  function buildLogsPageUrl(overrides) {
    return buildPageUrl(overrides);
  }

  function buildTableSourceUrl() {
    const tableEl = getFilterTableEl();
    const method = (tableEl && tableEl.getAttribute('data-dc-table-method')) || 'logs_table';
    const controller = (tableEl && tableEl.getAttribute('data-dc-table-controller')) || 'admin';
    const base = DevCraftAjax.baseUrl();
    const url = new URL(base, window.location.origin);
    const defaults = resolveFilterOrderSort(tableEl);

    url.searchParams.set('controller', controller);
    url.searchParams.set('method', method);
    url.searchParams.set('user_hash', DevCraftAjax.getUserHash());
    url.searchParams.set('order', defaults.order);
    url.searchParams.set('sort', defaults.sort);

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

  function updateTableTotalDisplay(total) {
    const bar = document.getElementById('dc-filter-bar');
    const totalId = (bar && bar.dataset.totalId) || 'dc-logs-total';
    const el = document.getElementById(totalId);

    if (el) {
      const suffix = el.dataset.recordsSuffix || t('записей');
      el.textContent = total + ' ' + suffix;
    }
  }

  function updateLogsTotalDisplay(total) {
    updateTableTotalDisplay(total);
  }

  function getFilterTablePlugin() {
    const tableEl = getFilterTableEl();

    if (!tableEl || !DevCraft.Metro.lib() || typeof DevCraft.Metro.lib().getPlugin !== 'function') {
      return null;
    }

    return DevCraft.Metro.getPlugin(tableEl, 'table');
  }

  function getLogsTablePlugin() {
    return getFilterTablePlugin();
  }

  function reloadFilterTable() {
    const tableEl = getFilterTableEl();
    const plugin = getFilterTablePlugin();

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
          updateTableTotalDisplay(data.total);
        }
      });
    }
  }

  function reloadLogsTable() {
    reloadFilterTable();
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
        updateTableTotalDisplay(data.total);
      } else if (data && Array.isArray(data.data)) {
        updateTableTotalDisplay(data.data.length);
      }
    });

    $table.on('data-load-error', function (event) {
      const detail = getMetroEventDetail(event);
      const error = detail.error || detail;

      console.error('[DevCraft] Ошибка загрузки таблицы фильтра', error, detail.source || '');
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
    const form = document.getElementById('dc-filter-form');
    const rulesContainer = document.getElementById('dc-filter-hidden-rules');

    if (!bar || typeof index !== 'number' || Number.isNaN(index)) {
      return;
    }

    const filterRules = parseFilterJson(bar.dataset.filterRules, []);

    if (index < 0 || index >= filterRules.length) {
      return;
    }

    filterRules.splice(index, 1);
    DevCraft.Filter.activeChipIndex = null;

    if (form && rulesContainer && !hasAjaxFilterTable()) {
      submitFilterForm(form, rulesContainer, filterRules);
      return;
    }

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

    const pageUrl = buildPageUrl({
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

    if (hasAjaxFilterTable()) {
      reloadFilterTable();
    }
  }

  function submitFilterForm(form, rulesContainer, rules) {
    if (hasAjaxFilterTable()) {
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

  function initFilterTable() {
    const tableEl = getFilterTableEl();

    if (!tableEl || !tableEl.getAttribute('data-dc-table-method')) {
      return;
    }

    if (!tableEl.dataset.filterTableBound) {
      tableEl.dataset.filterTableBound = '1';
      bindLogsTableMetroEvents(tableEl);
    }

    const sourceUrl = buildTableSourceUrl();
    tableEl.setAttribute('data-source', sourceUrl);

    const plugin = getFilterTablePlugin();

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

  function initLogsTable() {
    initFilterTable();
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
      const match = key.match(/^filter_rules\[(\d+)\]\[(\w+)\](?:\[(\d*)\])?$/);

      if (!match) {
        return;
      }

      const index = match[1];
      const field = match[2];
      const valueIndex = match[3];

      if (!ruleMap[index]) {
        ruleMap[index] = {};
      }

      if (field === 'value' && valueIndex !== undefined) {
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
      if (!hasAjaxFilterTable()) {
        return;
      }

      syncFilterStateFromUrl();
      reloadFilterTable();
    });
  }


  DevCraftFilter.parseJson = parseFilterJson;
  DevCraftFilter.findCatalogField = findCatalogField;
  DevCraftFilter.rebuildHiddenInputs = rebuildFilterHiddenInputs;
  DevCraftFilter.appendHidden = appendHidden;
  DevCraftFilter.appendRulesToUrl = appendFilterRulesToUrl;
  DevCraftFilter.getFilterFormContext = getFilterFormContext;
  DevCraftFilter.getFilterTableEl = getFilterTableEl;
  DevCraftFilter.hasAjaxFilterTable = hasAjaxFilterTable;
  DevCraftFilter.buildPageUrl = buildPageUrl;
  DevCraftFilter.buildLogsPageUrl = buildLogsPageUrl;
  DevCraftFilter.buildTableSourceUrl = buildTableSourceUrl;
  DevCraftFilter.updateTableTotalDisplay = updateTableTotalDisplay;
  DevCraftFilter.updateLogsTotalDisplay = updateLogsTotalDisplay;
  DevCraftFilter.reloadFilterTable = reloadFilterTable;
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
  DevCraftFilter.initFilterTable = initFilterTable;
  DevCraftFilter.initLogsTable = initLogsTable;
  DevCraftFilter.syncStateFromUrl = syncFilterStateFromUrl;
  DevCraftFilter.initPopstate = initPopstate;

  DevCraftMetro.initFilterDialog = initFilterDialogMetro;
  DevCraftMetro.getFilterTablePlugin = getFilterTablePlugin;
  DevCraftMetro.getLogsTablePlugin = getLogsTablePlugin;
  DevCraftMetro.selectVal = readMetroSelectValue;
  DevCraftMetro.calendarVal = readCalendarValue;

  document.addEventListener('click', handleFilterBarClick, true);
  document.addEventListener('click', function (event) {
    const filterApplyBtn = event.target.closest('[data-action="filter-dialog-apply"]');
    if (filterApplyBtn && typeof DevCraft.Filter.applyDialogRule === 'function') {
      event.preventDefault();
      DevCraft.Filter.applyDialogRule();
    }
  }, true);

  global.DevCraft.Filter = DevCraftFilter;

  DevCraftFilter.boot = function () {
    DevCraftFilter.initBar();
    DevCraftFilter.initFilterTable();
    DevCraftFilter.initPopstate();
  };

})(window);
