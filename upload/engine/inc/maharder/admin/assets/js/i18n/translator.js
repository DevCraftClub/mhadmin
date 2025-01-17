async function loadTranslations(langCode) {
	try {
		const module = await import(`./translation.${langCode}.js`);
		return module.default; // Экспортированный объект переводов
	} catch (error) {
		console.error(`Не удалось загрузить переводы для языка: ${langCode}`, error);
		return {}; // Возвращаем пустой объект, если файл не найден
	}
}


const translations = loadTranslations(lang_code).then((translations) => translations);


function t(phrase) {
	return translations[phrase] || phrase;
}