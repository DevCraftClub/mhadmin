/**
 * Минимальная проверка ветвлений filter.js (без DOM/Metro).
 * Запуск: node filter.selfcheck.mjs
 */
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const dir = dirname(fileURLToPath(import.meta.url));
const src = readFileSync(join(dir, 'filter.js'), 'utf8');

const must = [
	'function buildPageUrl(',
	'function hasAjaxFilterTable(',
	'function getFilterTableEl(',
	'data-dc-table-method',
	'!hasAjaxFilterTable()',
	'submitFilterForm(form, rulesContainer, filterRules)',
	'reloadFilterTable()',
	'function initFilterTable(',
];

for (const needle of must) {
	if (!src.includes(needle)) {
		throw new Error('filter.selfcheck: отсутствует ' + needle);
	}
}

if (src.includes("url.searchParams.set('action', 'logs')") && !src.includes('overrides.action || ctx.action')) {
	throw new Error('filter.selfcheck: action всё ещё захардкожен в logs');
}

if (src.includes("url.searchParams.set('method', 'logs_table')") && !src.includes('data-dc-table-method')) {
	throw new Error('filter.selfcheck: method всё ещё захардкожен в logs_table');
}

console.log('filter.selfcheck: ok');
