/**
 * Базовые JavaScript-ресурсы админки DevCraft
 */
(function (global) {
    'use strict';

    function t(phrase, params) {
        return typeof global.__ === 'function' ? global.__(phrase, params || {}) : phrase;
    }

    const NOTIFY_CLS = {
        success: 'bg-green fg-white',
        warning: 'bg-orange fg-white',
        error: 'alert bg-red fg-white',
        info: ''
    };


    class DevCraftMetro {
        static lib() {
            return window.Metro || null;
        }

        static getPlugin(el, name) {
            const metro = DevCraftMetro.lib();
            return metro && typeof metro.getPlugin === 'function' ? metro.getPlugin(el, name) : null;
        }

        static makePlugin(el, role, options) {
            const metro = DevCraftMetro.lib();
            if (!el || !metro || typeof metro.makePlugin !== 'function') {
                return null;
            }
            const pluginRole = role || el.getAttribute('data-role');
            if (!pluginRole) {
                return null;
            }
            return metro.makePlugin(el, pluginRole, options || {});
        }

        static get$elements(el) {
            const metro = DevCraftMetro.lib();
            return metro && typeof metro.get$elements === 'function' ? metro.get$elements(el) : null;
        }

        static dialogApi() {
            const metro = DevCraftMetro.lib();
            return metro && metro.dialog ? metro.dialog : null;
        }
    }

    class DevCraftAjax {}
    class DevCraftFilter {}
    class DevCraftAssets {}
    class DevCraftDashboard {}
    class DevCraftSettings {}
    class DevCraftLoader {}

    const DevCraftDebug = {
        FORCE: null,
        isEnabled() {
            if (this.FORCE !== null) {
                return !!this.FORCE;
            }
            const body = document.body;
            return !!(body && body.dataset && body.dataset.debug === '1');
        },
        log(channel, step, payload) {
            if (!this.isEnabled()) {
                return;
            }
            const prefix = '[DevCraft:' + channel + '] ' + step;
            const write = typeof console.debug === 'function' ? console.debug : console.info;
            if (payload !== undefined) {
                write.call(console, prefix, payload);
            } else {
                write.call(console, prefix);
            }
        },
        dumpFilterState() {
            if (global.DevCraft && global.DevCraft.Filter && typeof global.DevCraft.Filter.dumpDaterangeState === 'function') {
                return global.DevCraft.Filter.dumpDaterangeState();
            }
            return null;
        }
    };

    class DevCraft {
        static Metro = DevCraftMetro;
        static Ajax = DevCraftAjax;
        static Filter = DevCraftFilter;
        static Assets = DevCraftAssets;
        static Dashboard = DevCraftDashboard;
        static Settings = DevCraftSettings;
        static Loader = DevCraftLoader;
        static Debug = DevCraftDebug;

        static boot() {
            DevCraftSettings.bind();

            if (global.DevCraft && global.DevCraft.Filter && typeof global.DevCraft.Filter.boot === 'function') {
                global.DevCraft.Filter.boot();
            }

            if (global.DevCraftAdmin && typeof global.DevCraftAdmin.boot === 'function') {
                global.DevCraftAdmin.boot();
            }
        }

        static toast(message) { return DevCraft.Metro.toast(message); }
        static notify(title, message, type) { return DevCraft.Metro.notify(title, message, type); }
        static notifyError(title, message, details) { return DevCraft.Metro.notifyError(title, message, details); }
        static showLoader(text) { return DevCraft.Loader.show(text); }
        static hideLoader() { return DevCraft.Loader.hide(); }
        static handleApiNotice(payload) { return DevCraft.Ajax.handleNotice(payload); }
        static runAssetsCheck(options) { return DevCraft.Assets.runCheck(options); }
        static runUpdateCheck() { return DevCraft.Dashboard.runUpdateCheck(); }
        static metroDialogCreate(options) { return DevCraft.Metro.dialogCreate(options); }
        static metroDialogLoad(selector, options) { return DevCraft.Metro.dialogLoad(selector, options); }
        static metroDialogClose(selector) { return DevCraft.Metro.dialogClose(selector); }
        static openFilterRuleDialog(index) { return DevCraft.Filter.openRuleDialog(index); }
        static clearFilterRules() { return DevCraft.Filter.clearRules(); }
        static removeActiveFilterChip() { return DevCraft.Filter.removeActiveChip(); }

        static __(phrase, parameters) {
            if (typeof global.__ === 'function') {
                return global.__(phrase, parameters);
            }

            return String(phrase);
        }
    }


    function dcToast(message) {
        if (DevCraft.Metro.lib() && DevCraft.Metro.lib().toast && DevCraft.Metro.lib().toast.create) {
            DevCraft.Metro.lib().toast.create(message, {
                timeout: 3000,
                clsToast: 'bg-green fg-white'
            });
            return;
        }

        console.warn('[DevCraft]', message);
    }

    function dcNotify(title, message, type) {
        const notifyType = type || 'info';
        const timeout = notifyType === 'error' ? 8000 : 5000;

        if (DevCraft.Metro.lib() && DevCraft.Metro.lib().notify && DevCraft.Metro.lib().notify.create) {
            DevCraft.Metro.lib().notify.create(message, title, {
                timeout: timeout,
                clsNotify: NOTIFY_CLS[notifyType] || ''
            });
            return;
        }

        console.warn('[DevCraft]', title, message);
    }

    function dcNotifyError(title, message, details) {
        dcNotify(title, message, 'error');
        console.error('[DevCraft]', title, message, details);
    }

    const DC_LOADER_SNIPPETS = [
        {
            label: 'PHP',
            lines: [
                '<span class="dc-code-loader__kw">require_once</span> <span class="dc-code-loader__str">\'bootstrap.php\'</span>;',
                '$api-><span class="dc-code-loader__fn">sync</span>();',
                'status: <span class="dc-code-loader__str">processing</span> <span class="dc-code-loader__cursor"></span>',
            ],
        },
        {
            label: 'JavaScript',
            lines: [
                '<span class="dc-code-loader__kw">import</span> { <span class="dc-code-loader__fn">save</span> } <span class="dc-code-loader__kw">from</span> <span class="dc-code-loader__str">\'admin\'</span>;',
                '<span class="dc-code-loader__kw">await</span> save();',
                'status: <span class="dc-code-loader__str">please wait</span> <span class="dc-code-loader__cursor"></span>',
            ],
        },
        {
            label: 'Python',
            lines: [
                '<span class="dc-code-loader__kw">from</span> core <span class="dc-code-loader__kw">import</span> <span class="dc-code-loader__fn">sync</span>',
                '<span class="dc-code-loader__fn">sync</span>()',
                'status: <span class="dc-code-loader__str">running</span> <span class="dc-code-loader__cursor"></span>',
            ],
        },
        {
            label: 'Go',
            lines: [
                '<span class="dc-code-loader__kw">package</span> main',
                '<span class="dc-code-loader__kw">func</span> <span class="dc-code-loader__fn">process</span>() {}',
                'status: <span class="dc-code-loader__str">working</span> <span class="dc-code-loader__cursor"></span>',
            ],
        },
        {
            label: 'Rust',
            lines: [
                '<span class="dc-code-loader__kw">use</span> crate::<span class="dc-code-loader__fn">worker</span>;',
                '<span class="dc-code-loader__fn">worker</span>::run();',
                'status: <span class="dc-code-loader__str">hold on</span> <span class="dc-code-loader__cursor"></span>',
            ],
        },
    ];
    let dcLoaderDepth = 0;

    function dcLoaderNode(id) {
        return document.getElementById(id);
    }

    function dcLoaderRender() {
        const sample = DC_LOADER_SNIPPETS[Math.floor(Math.random() * DC_LOADER_SNIPPETS.length)] || DC_LOADER_SNIPPETS[0];
        const label = dcLoaderNode('dc-code-loader-label');
        const line1 = dcLoaderNode('dc-code-loader-line-1');
        const line2 = dcLoaderNode('dc-code-loader-line-2');
        const line3 = dcLoaderNode('dc-code-loader-line-3');

        if (label) {
            label.textContent = sample.label;
        }
        if (line1) {
            line1.innerHTML = sample.lines[0] || '';
        }
        if (line2) {
            line2.innerHTML = sample.lines[1] || '';
        }
        if (line3) {
            line3.innerHTML = sample.lines[2] || '';
        }
    }

    function dcLoaderShow(text) {
        const root = dcLoaderNode('dc-code-loader');
        const hint = dcLoaderNode('dc-code-loader-hint');

        dcLoaderDepth += 1;
        if (!root) {
            return;
        }

        dcLoaderRender();
        if (hint) {
            hint.textContent = text || t('Пожалуйста, подождите, идёт обработка…');
        }
        root.classList.add('is-visible');
        root.setAttribute('aria-hidden', 'false');
    }

    function dcLoaderHide() {
        const root = dcLoaderNode('dc-code-loader');

        dcLoaderDepth = Math.max(0, dcLoaderDepth - 1);
        if (dcLoaderDepth > 0 || !root) {
            return;
        }

        root.classList.remove('is-visible');
        root.setAttribute('aria-hidden', 'true');
    }

    /**
     * Metro UI Dialog — OpenDialog-Pattern (DevCraft.Metro.dialogApi().create).
     * @see metroui/examples/dialog.html — OpenDialog()
     */
    function dcMetroDialogCreate(options) {
        if (!DevCraft.Metro.lib() || !DevCraft.Metro.dialogApi() || !DevCraft.Metro.dialogApi().create) {
            dcNotify(t('Ошибка'), t('Metro UI недоступен'), 'warning');
            return null;
        }

        return DevCraft.Metro.dialogApi().create(Object.assign({
            closeButton: true,
            defaultActions: false
        }, options || {}));
    }

    function dcMetroDialogElement(selector) {
        return typeof selector === 'string' ? document.querySelector(selector) : selector;
    }

    function dcMetroDialogGetPlugin(selector) {
        const el = dcMetroDialogElement(selector);

        if (!el || !DevCraft.Metro.lib()) {
            return null;
        }

        let plugin = DevCraft.Metro.getPlugin(el, 'dialog');

        if (!plugin && DevCraft.Metro.lib() && typeof DevCraft.Metro.lib().makePlugin === 'function') {
            DevCraft.Metro.makePlugin(el, 'dialog', {
                overlay: el.dataset.overlay !== 'false',
                closeButton: el.dataset.closeButton !== 'false',
                overlayClickClose: el.dataset.overlayClickClose !== 'false',
                defaultActions: el.dataset.defaultActions === 'true',
                width: el.dataset.width || 'auto'
            });
            plugin = DevCraft.Metro.getPlugin(el, 'dialog');
        }

        return plugin;
    }

    function dcMetroDialogPluginElement(plugin, selector) {
        if (plugin && plugin.element && plugin.element[0]) {
            return plugin.element[0];
        }

        return dcMetroDialogElement(selector);
    }

    /**
     * Metro UI Dialog — LoadDialog-Pattern (getPlugin → setTitle/setContent → open).
     * @see metroui/examples/dialog.html — LoadDialog()
     */
    function dcMetroDialogLoad(selector, options) {
        options = options || {};
        const el = dcMetroDialogElement(selector);

        if (!el || !DevCraft.Metro.lib()) {
            dcNotify(t('Ошибка'), t('Metro UI недоступен'), 'warning');
            return null;
        }

        const plugin = dcMetroDialogGetPlugin(selector);

        if (!plugin) {
            dcNotify(t('Ошибка'), t('Не удалось открыть диалог'), 'warning');
            return null;
        }

        const content = options.content !== undefined ? options.content : null;
        const title = options.title || null;

        const dialog = DevCraft.Metro.dialogApi();

        if (dialog && typeof dialog.open === 'function' && dialog.isDialog(el)) {
            dialog.open(el, content, title);
        } else {
            if (title) {
                plugin.setTitle(title);
            }

            if (content !== null) {
                plugin.setContent(content);
            }

            plugin.open();
        }

        const root = dcMetroDialogPluginElement(plugin, selector);

        if (typeof options.onReady === 'function' && root) {
            options.onReady(root);
        }

        return plugin;
    }

    function dcMetroDialogClose(selector) {
        const el = dcMetroDialogElement(selector);

        const dialog = DevCraft.Metro.dialogApi();

        if (el && dialog && typeof dialog.close === 'function' && dialog.isDialog(el)) {
            dialog.close(el);
            return;
        }

        const plugin = dcMetroDialogGetPlugin(selector);

        if (plugin && typeof plugin.close === 'function') {
            plugin.close();
        }
    }

    function handleApiNotice(payload) {
        if (!payload) {
            return;
        }

        if (payload.error && payload.error.detail) {
            console.error('[DevCraft]', payload.error.code, payload.error.detail);
        } else if (!payload.success && payload.error) {
            console.error('[DevCraft]', payload.error);
        }

        const notice = payload.notice;

        if (!notice || !notice.message) {
            if (!payload.success && payload.error && payload.error.message) {
                dcNotify(payload.error.title || t('Ошибка'), payload.error.message, 'error');
            }

            return;
        }

        if (notice.channel === 'toast') {
            dcToast(notice.message);
            return;
        }

        dcNotify(notice.title || t('Уведомление'), notice.message, notice.type || 'info');
    }

    /**
     * CRUD формы настроек модуля (сериализация, валидация, сохранение).
     */
    DevCraftSettings.serialize = function (form) {
        const data = {};
        const elements = form.querySelectorAll('input, select, textarea');

        elements.forEach(function (el) {
            const name = el.name;

            if (!name || name === 'user_hash') {
                return;
            }

            if (el.type === 'checkbox') {
                data[name] = el.checked;
                return;
            }

            if (el.multiple) {
                data[name.replace(/\[\]$/, '')] = Array.from(el.selectedOptions).map(function (opt) {
                    return opt.value;
                });
                return;
            }

            data[name] = el.value;
        });

        return data;
    };

    DevCraftSettings.showFieldErrors = function (form, fields) {
        form.querySelectorAll('.invalid_feedback').forEach(function (el) {
            el.textContent = '';
        });

        if (!fields) {
            return;
        }

        Object.keys(fields).forEach(function (fieldId) {
            const target = form.querySelector('.invalid_feedback[data-field="' + fieldId + '"]');

            if (target) {
                target.textContent = fields[fieldId];
            }
        });
    };

    DevCraftSettings.resolveSaveUrl = function (form) {
        let saveUrl = form.dataset.saveUrl || '';

        if (saveUrl === '' || saveUrl.indexOf('mod=') !== -1) {
            return saveUrl;
        }

        const mod = document.body.dataset.mod;

        if (!mod) {
            return saveUrl;
        }

        return saveUrl + (saveUrl.indexOf('?') === -1 ? '?' : '&') + 'mod=' + encodeURIComponent(mod);
    };

    DevCraftSettings.save = function (form) {
        const saveUrl = DevCraftSettings.resolveSaveUrl(form);
        const data = DevCraftSettings.serialize(form);
        const userHashInput = form.querySelector('input[name="user_hash"]');
        const userHash = userHashInput ? userHashInput.value : getUserHash();

        dcLoaderShow(t('Сохраняем изменения, пожалуйста подождите…'));

        return fetch(saveUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                user_hash: userHash,
                data: JSON.stringify(data),
            }).toString(),
        })
            .then(parseJsonResponse)
            .then(function (payload) {
                DevCraftSettings.showFieldErrors(form, payload.error && payload.error.fields ? payload.error.fields : null);
                handleApiNotice(payload);

                return payload;
            }).finally(function () {
                dcLoaderHide();
            });
    };

    DevCraftSettings.bind = function () {
        if (global.__dcSettingsBound) {
            return;
        }

        global.__dcSettingsBound = true;

        document.addEventListener('click', function (event) {
            const saveBtn = event.target.closest('.js-settings-save');

            if (!saveBtn) {
                return;
            }

            const form = saveBtn.closest('.js-settings-form');

            if (!form) {
                return;
            }

            DevCraftSettings.save(form).catch(function (err) {
                dcNotifyError(t('Ошибка'), t('Сеть или сервер недоступен'), err);
            });
        });
    };

    function ajaxUrl(base, params) {
        const url = new URL(base, window.location.origin);
        Object.keys(params).forEach(function (key) {
            url.searchParams.set(key, params[key]);
        });
        return url.toString();
    }

    function ajaxBaseUrl() {
        return document.body.dataset.ajaxBase || '';
    }

    function parseJsonResponse(response) {
        const contentType = response.headers.get('content-type') || '';

        if (contentType.indexOf('json') === -1) {
            return response.text().then(function (text) {
                const err = new Error(t('Сервер вернул не-JSON ответ'));
                err.raw = text;
                throw err;
            });
        }

        return response.json().then(function (payload) {
            if (!response.ok && payload && payload.success === false) {
                handleApiNotice(payload);
            }

            return payload;
        }).catch(function () {
            throw new Error(t('Не удалось разобрать JSON-ответ'));
        });
    }

    function getUserHash() {
        const input = document.querySelector('input[name="user_hash"]');

        if (input && input.value) {
            return input.value;
        }

        return typeof window.dle_login_hash === 'string' ? window.dle_login_hash : '';
    }

    function postAjax(method, data) {
        const params = {
            controller: 'admin',
            method: method
        };
        const mod = document.body.dataset.mod;

        if (mod) {
            params.mod = mod;
        }

        dcLoaderShow(data && typeof data.__loaderText === 'string' ? data.__loaderText : t('Выполняем запрос, пожалуйста подождите…'));

        return fetch(ajaxUrl(ajaxBaseUrl(), params), {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                user_hash: getUserHash(),
                data: JSON.stringify(data || {})
            }).toString()
        }).then(parseJsonResponse).finally(function () {
            dcLoaderHide();
        });
    }

    /**
     * Multipart POST (FormData). URL задаёт вызывающий код через DevCraftAjax.url(...).
     * user_hash добавляется, если ещё нет в FormData.
     */
    function postMultipart(url, formData, onProgress) {
        if (!(formData instanceof FormData)) {
            return Promise.reject(new Error(t('Ожидался FormData')));
        }

        if (!formData.has('user_hash')) {
            formData.append('user_hash', getUserHash());
        }

        dcLoaderShow(t('Загружаем данные, пожалуйста подождите…'));

        return new Promise(function (resolve, reject) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url);

            xhr.upload.addEventListener('progress', function (event) {
                if (typeof onProgress === 'function' && event.lengthComputable) {
                    onProgress(event.loaded, event.total);
                }
            });

            xhr.addEventListener('load', function () {
                let payload;

                try {
                    payload = JSON.parse(xhr.responseText || '{}');
                } catch (e) {
                    dcLoaderHide();
                    reject(new Error(t('Не удалось разобрать JSON-ответ')));
                    return;
                }

                if (xhr.status >= 200 && xhr.status < 300) {
                    dcLoaderHide();
                    resolve(payload);
                    return;
                }

                if (payload && payload.success === false) {
                    handleApiNotice(payload);
                }

                dcLoaderHide();
                reject(payload || new Error('HTTP ' + xhr.status));
            });

            xhr.addEventListener('error', function () {
                dcLoaderHide();
                reject(new Error(t('Сетевая ошибка')));
            });

            xhr.send(formData);
        });
    }

    DevCraftMetro.toast = dcToast;
    DevCraftMetro.notify = dcNotify;
    DevCraftMetro.notifyError = dcNotifyError;
    DevCraftMetro.dialogCreate = dcMetroDialogCreate;
    DevCraftMetro.dialogLoad = dcMetroDialogLoad;
    DevCraftMetro.dialogClose = dcMetroDialogClose;
    DevCraftMetro.dialogElement = dcMetroDialogElement;
    DevCraftMetro.dialogGetPlugin = dcMetroDialogGetPlugin;
    DevCraftMetro.dialogPluginElement = dcMetroDialogPluginElement;
    DevCraftLoader.show = dcLoaderShow;
    DevCraftLoader.hide = dcLoaderHide;
    DevCraftAjax.parseResponse = parseJsonResponse;
    DevCraftAjax.post = postAjax;
    DevCraftAjax.postMultipart = postMultipart;
    DevCraftAjax.url = ajaxUrl;
    DevCraftAjax.baseUrl = ajaxBaseUrl;
    DevCraftAjax.getUserHash = getUserHash;
    DevCraftAjax.handleNotice = handleApiNotice;
    DevCraftAjax.serializeForm = function (form) {
        return DevCraftSettings.serialize(form);
    };
    DevCraftAjax.showFieldErrors = function (form, fields) {
        DevCraftSettings.showFieldErrors(form, fields);
    };
    DevCraftAjax.saveSettings = function (form) {
        return DevCraftSettings.save(form);
    };

    global.DevCraft = DevCraft;
    DevCraft.__ = typeof global.__ === 'function' ? global.__ : function (phrase) { return phrase; };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            DevCraft.boot();
        });
    } else {
        DevCraft.boot();
    }

})(window);

