(function (global) {
    'use strict';

    function applyParameters(text, parameters) {
        let result = String(text);

        Object.keys(parameters || {}).forEach(function (key) {
            const value = String(parameters[key]);
            result = result
                .split(':' + key + ':').join(value)
                .split('{' + key + '}').join(value)
                .split('%' + key + '%').join(value);
        });

        return result;
    }

    function translate(phrase, parameters) {
        const dict = (global.DevCraftI18n && global.DevCraftI18n.dictionary) || {};
        const text = Object.prototype.hasOwnProperty.call(dict, phrase) ? dict[phrase] : phrase;

        return applyParameters(text, parameters || {});
    }

    function __(phrase, parameters) {
        return translate(phrase, parameters);
    }

    global.__ = __;
    global.DevCraftTranslate = translate;
})(window);
