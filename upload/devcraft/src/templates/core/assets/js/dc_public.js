/**
 * Публичный JS-клиент DevCraft для шаблонов DLE (не админка).
 * Запросы идут на /devcraft/ajax.php?mod=…&controller=public&method=…
 */
(function (global) {
	'use strict';

	const NOTIFY_CLS = {
		success: 'bg-green fg-white',
		warning: 'bg-orange fg-white',
		error: 'alert bg-red fg-white',
		info: ''
	};

	function t(phrase) {
		return typeof global.__ === 'function' ? global.__(phrase) : phrase;
	}

	function ajaxBaseUrl() {
		if (document.body && document.body.dataset && document.body.dataset.dcAjaxBase) {
			return document.body.dataset.dcAjaxBase;
		}

		const meta = document.querySelector('meta[name="dc-ajax-base"]');
		if (meta && meta.content) {
			return meta.content;
		}

		if (typeof global.dle_root === 'string' && global.dle_root !== '') {
			return global.dle_root.replace(/\/?$/, '/') + 'devcraft/ajax.php';
		}

		return '/devcraft/ajax.php';
	}

	function buildUrl(base, params) {
		const url = new URL(base, window.location.origin);
		Object.keys(params || {}).forEach(function (key) {
			url.searchParams.set(key, params[key]);
		});
		return url.toString();
	}

	function getUserHash() {
		if (typeof global.dle_login_hash === 'string' && global.dle_login_hash !== '') {
			return global.dle_login_hash;
		}

		const input = document.querySelector('input[name="user_hash"], input[name="dle_login_hash"]');
		return input && input.value ? input.value : '';
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

		return response.json();
	}

	function dlePush(type, message, title, life) {
		if (!global.DLEPush) {
			return false;
		}

		if (type === 'error' && typeof global.DLEPush.error === 'function') {
			global.DLEPush.error(message, title || '', life);
			return true;
		}

		if (type === 'warning' && typeof global.DLEPush.warning === 'function') {
			global.DLEPush.warning(message, title || '', life);
			return true;
		}

		if (typeof global.DLEPush.info === 'function') {
			global.DLEPush.info(message, title || '', life);
			return true;
		}

		return false;
	}

	function toast(message) {
		if (global.Metro && Metro.toast && typeof Metro.toast.create === 'function') {
			Metro.toast.create(message, { timeout: 3000, clsToast: 'bg-green fg-white' });
			return;
		}

		if (dlePush('info', message, t('Уведомление'), 3500)) {
			return;
		}

		if (typeof global.DLEalert === 'function') {
			global.DLEalert(message, t('Уведомление'));
			return;
		}

		console.info('[DevCraftPublic]', message);
	}

	function notify(title, message, type) {
		const notifyType = type || 'info';
		const timeout = notifyType === 'error' ? 8000 : 5000;

		if (global.Metro && Metro.notify && typeof Metro.notify.create === 'function') {
			Metro.notify.create(message, title, {
				timeout: timeout,
				clsNotify: NOTIFY_CLS[notifyType] || ''
			});
			return;
		}

		if (dlePush(notifyType, message, title || t('Уведомление'), timeout)) {
			return;
		}

		if (typeof global.DLEalert === 'function') {
			global.DLEalert((title ? title + ': ' : '') + message, title || t('Уведомление'));
			return;
		}

		console.warn('[DevCraftPublic]', title, message);
	}

	function handleNotice(payload) {
		if (!payload) {
			return;
		}

		const notice = payload.notice;

		if (!notice || !notice.message) {
			if (!payload.success && payload.error && payload.error.message) {
				notify(payload.error.title || t('Ошибка'), payload.error.message, 'error');
			}
			return;
		}

		if (notice.channel === 'toast') {
			toast(notice.message);
			return;
		}

		notify(notice.title || t('Уведомление'), notice.message, notice.type || 'info');
	}

	function post(mod, method, data) {
		return fetch(buildUrl(ajaxBaseUrl(), {
			mod: mod,
			controller: 'public',
			method: method
		}), {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({
				user_hash: getUserHash(),
				data: JSON.stringify(data || {})
			}).toString()
		}).then(parseJsonResponse).then(function (payload) {
			handleNotice(payload);
			return payload;
		});
	}

	function postMultipart(mod, method, formData) {
		if (!(formData instanceof FormData)) {
			return Promise.reject(new Error(t('Ожидался FormData')));
		}

		if (!formData.has('user_hash')) {
			formData.append('user_hash', getUserHash());
		}

		return fetch(buildUrl(ajaxBaseUrl(), {
			mod: mod,
			controller: 'public',
			method: method
		}), {
			method: 'POST',
			body: formData
		}).then(parseJsonResponse).then(function (payload) {
			handleNotice(payload);
			return payload;
		});
	}

	const DevCraftPublic = {
		Ajax: {
			baseUrl: ajaxBaseUrl,
			url: buildUrl,
			getUserHash: getUserHash,
			parseResponse: parseJsonResponse,
			post: post,
			postMultipart: postMultipart,
			handleNotice: handleNotice,
			toast: toast,
			notify: notify
		},
		boot: function () {
			document.dispatchEvent(new CustomEvent('dc:public:ready', { detail: DevCraftPublic }));
		}
	};

	global.DevCraftPublic = DevCraftPublic;

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			DevCraftPublic.boot();
		});
	} else {
		DevCraftPublic.boot();
	}
})(window);
