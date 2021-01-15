<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/main.html */
class __TwigTemplate_9ed5a26db3b8c536a49ee3ede08d73192a08c3847e0e40ea91174b74dc0007f6 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'scripts' => [$this, 'block_scripts'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $this->parent = $this->loadTemplate("base.html", "modules/main.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    public function block_content($context, array $blocks = [])
    {
        // line 2
        echo "
<div class=\"ui segment\">
\t";
        // line 4
        echo twig_include($this->env, $context, "templateIncludes/boxes.html", ["boxes" => ["main" => ["link" => "#", "name" => "Настройки API", "icon" => "fad fa-cog"], "lang" => ["link" => "#", "name" => "Языковые настройки", "icon" => "fad fa-flag"]]]);
        // line 19
        echo "
</div>

<div class=\"ui bottom attached tab segment active\" data-tab=\"main\">
\t<h4 class=\"ui dividing header\">Настройки API</h4>
\t<div class=\"ui four column grid form\">
\t\t";
        // line 26
        echo "
\t\t";
        // line 27
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "api_url", "name" => "API-URL", "descr" => "Ссылка на API в формате http://сайт.local/api/v1 или http://api.сайт.local/v1", "type" => "input", "variable" => []]);
        // line 34
        echo "

\t\t";
        // line 36
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "api_key", "name" => "API-ключ", "descr" => "Введите ключ DLE-API, чтобы скрипт мог корректно обрабатывать запросы.", "type" => "input", "variable" => []]);
        // line 43
        echo "

\t\t";
        // line 45
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "list_count", "name" => "Количество объектов", "descr" => "Введите количество объектов, которые будут отображены в списках, таблицах и т.д.. Это глобальное значение для всех модулей автора. При пустом значении будут браться значения из настройки движка \"Количество отоброжаемых новостей на страницу\".", "type" => "number", "variable" => []]);
        // line 52
        echo "

\t\t";
        // line 55
        echo "\t</div>
</div>

<div class=\"ui bottom attached tab segment \" data-tab=\"lang\">
\t<h4 class=\"ui dividing header\">Языковые настройки</h4>
\t<div class=\"ui four column grid form\">
\t\t";
        // line 62
        echo "
\t\t";
        // line 63
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "multilang", "name" => "Включить Мультиязычность?", "descr" => "При включенном параметре, у сайта появится возможностъ переключать языки.<br><a href=\"#\" role=\"button\"
\t\t\tclass=\"ui primary button\">Список языков</a>", "type" => "checkbox", "variable" => []]);
        // line 71
        echo "

\t\t";
        // line 73
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "download_icons", "name" => "Загружать иконки флажков каждого языка?", "descr" => "Для удобства будут испольтоваться иконки сервиса <a href=\"https://www.countryflags.io/\"
\t\t\ttarget=\"_blank\">CountryFlags</a>, если не будет указана ссылка при добавлении / редактировании
\t\tязыка.<br>Если ссылка указана со стороннего сервиса, то сайт попытается её скачать.", "type" => "checkbox", "variable" => []]);
        // line 82
        echo "

\t\t";
        // line 84
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "flags_path", "name" => "Путь загрузки иконок / флажков", "descr" => "По умолчанию: uploads/maharder/flags/. Путь указываем от корня сайта без слеша в начале, но со слешем в
\t\tконце.", "type" => "input", "variable" => []]);
        // line 92
        echo "

\t\t";
        // line 94
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "unknown", "name" => "Неизвестный флаг", "descr" => "Если на сервисе CountryFlags не найдётся нужного флага, то укажите ссылку на заглужку. По умолчанию:
\t\t/uploads/maharder/flags/shiny/24/unknown.png.", "type" => "input", "variable" => []]);
        // line 102
        echo "

\t\t";
        // line 104
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "icon_size", "name" => "Размер иконок", "descr" => "По умолчанию: 24", "type" => "select", "variable" => [], "values" => [16 => "16", 24 => "24", 32 => "32", 48 => "48", 64 => "64"]]);
        // line 118
        echo "

\t\t";
        // line 120
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "icon_style", "name" => "Тип иконок", "descr" => "По умолчанию: shiny", "type" => "select", "variable" => [], "values" => ["flat" => "Плоские", "shiny" => "Сияющие"]]);
        // line 131
        echo "

\t\t";
        // line 133
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "default_language", "name" => "Стандартный язык", "descr" => "Язык по умолчанию, пока пользователь не переключит его на свой", "type" => "select", "variable" => [], "values" => []]);
        // line 141
        echo "

\t\t";
        // line 143
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "fallback_language", "name" => "Запасной язык", "descr" => "Если у основного языка не будет перевода или произойдёт какая-либо ошибка, то на какой язык
\t\tпереключить?", "type" => "select", "variable" => [], "values" => []]);
        // line 152
        echo "

\t\t";
        // line 154
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "path", "name" => "Путь до языковых файлов", "descr" => "Стандартный путь до языковых файлов /locales.", "type" => "input", "variable" => []]);
        // line 161
        echo "

\t\t";
        // line 163
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "cookieField", "name" => "Переменная для COOKIE", "descr" => "Используемый язык будет браться из куки браузера.", "type" => "input", "variable" => []]);
        // line 170
        echo "

\t\t";
        // line 172
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "translator", "name" => "Включить машиный перевод?", "descr" => "При включенном параметре, позволяет переводить из исходного языка в нужный.", "type" => "checkbox", "variable" => []]);
        // line 179
        echo "

\t\t";
        // line 181
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "translateEngine", "name" => "Движок перевода", "descr" => "Какой сервис использовать для перевода текста?<br><br><b
\t\t\tstyle=\"color: #cc0000\">!ВНИМАНИЕ!</b><br>Следующие сервисы имеют следующие ограничения для бесплатного
\t\tиспользование и распространяются через сервис RapidApi!<br><br>
\t\t<ul>
\t\t\t<li><b>Microsoft Translate</b> - 500000 символов в месяц. Подробнее <a
\t\t\t\t\thref=\"https://rapidapi.com/microsoft-azure-org-microsoft-cognitive-services/api/microsoft-translator-text/pricing\"
\t\t\t\t\ttarget=\"_blank\" alt=\"Таблица цен\">здесь</a>.</li>
\t\t\t<li><b>Language Translation</b> - 10 запросов в день, 15000 символов в день. Подробнее <a
\t\t\t\t\thref=\"https://rapidapi.com/cloud-actions-cloud-actions-default/api/language-translation/pricing\"
\t\t\t\t\ttarget=\"_blank\" alt=\"Таблица цен\">здесь</a>.</li>
\t\t\t<li><b>Deep Translate</b> - 100 запросов в месяц, 100000 символов в месяц. Подробнее <a
\t\t\t\t\thref=\"https://rapidapi.com/gatzuma/api/deep-translate1/pricing\" target=\"_blank\"
\t\t\t\t\talt=\"Таблица цен\">здесь</a>.</li>
\t\t</ul>", "type" => "select", "variable" => [], "values" => ["rapidapi_microsoft" => "Microsoft Translate (RapidApi)", "rapidapi_language_translation" => "Language Translations (RapidApi)", "rapidapi_deep_translate" => "Deep Translate (RapidApi)"]]);
        // line 206
        echo "

\t\t";
        // line 208
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "transAPI", "name" => "API ключ движка перевода", "descr" => "API ключ для перевода через сервис. <br>
\t\t<ul class=\"ui list-circle\">
\t\t\t<li class=\"item\"><b>RapidApi</b>: Сборник различных сервисов для различных API. Приобретение ключа находятся
\t\t\t\tна <a href=\"https://rapidapi.com/developer/new\" target=\"_blank\">сайте сервиса</a>.</li>
\t\t</ul> <br>Если найдёте другие популярные и бесплатные сервисы - писать по контактам разработчику.", "type" => "input", "variable" => []]);
        // line 219
        echo "

\t\t";
        // line 221
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "transWatcher", "name" => "Автоматизировать отслеживание?", "descr" => "На сервере будет создаваться файл с отчётом сколько какой сервис с каким API (ключом) отправил символов
\t\tи запросов. При достижении лимита скрипт будет переключаться на следующий сервис и на следующий ключ API. В
\t\tпротивном случае - при превышении лимита администратору нужно будет переключить вручную сервис", "type" => "checkbox", "variable" => []]);
        // line 230
        echo "
\t\t";
        // line 232
        echo "\t</div>
</div>
<div class=\"ui segment\">
\t<div class=\"ui button\" tabindex=\"0\">Сохранить</div>
</div>
";
    }

    // line 240
    public function block_scripts($context, array $blocks = [])
    {
        // line 241
        echo "
";
        // line 243
        echo "<script>
\tvar avApiServices = JSON.parse('{}'), apiID = '#transAPI', parentApiKey = \$(document).find(apiID).first().parent(), apiKeyItems = 0, apiKeyItemsCount = 0;
\tfunction createItem(id, service = '', key = '') {
\t\tlet html = '<div class=\"item apiItem\" data-id=\"' + id + '\"><div class=\"content\"><div class=\"description\"><div class=\"ui right labeled input\"><input class=\"apiKey\" type=\"text\" placeholder=\"API ключ\" name=\"apiKey-' + id + '\" id=\"apiKey-' + id + '\" value=\"' + key + '\"><div class=\"ui dropdown aksDd label\"><input data-name=\"apiKeyService\" type=\"hidden\" name=\"apiKey-' + id + '-service\" id=\"apiKey-' + id + '-service\">';
\t\tif (service != '') html += '<div class=\"text\">' + avApiServices[service] + '</div>';
\t\telse html += '<div class=\"text\">Выбрать сервис</div>';
\t\thtml += '<i class=\"dropdown icon\"></i><div class=\"menu\">';
\t\t\$.each(avApiServices, function (key, value) {
\t\t\thtml += '<div class=\"item\" data-value=\"' + key + '\">' + value + '</div>';
\t\t});
\t\thtml += '</div></div></div><div class=\"ui icon buttons\"><div role=\"button\" class=\"ui green button\" data-action=\"addNewKey\" title=\"Добавить новый ключ\"><i class=\"plus icon\"></i></div><div role=\"button\" class=\"ui red button\" data-action=\"delThisKey\" data-id=\"' + id + '\" title=\"Удалить ключ\"><i class=\"minus icon\"></i></div></div></div></div></div>';

\t\treturn html;
\t}

\tfunction createKeyInputs() {
\t\tlet html = '<div class=\"ui items\" name=\"apiKeys\">';
\t\tlet keysValue = {}, countServices = 0;
\t\ttry {
\t\t\tkeysValue = JSON.parse(atob(\$(apiID).val()));
\t\t\tcountServices = keysValue.length;
\t\t} catch (e) {
\t\t\tconsole.log('No API keys insert');
\t\t\t\$('body').toast({
\t\t\t\tclass: 'error',
\t\t\t\ttitle: `Ошибка`,
\t\t\t\tmessage: `Нет действующих ключей!`,
\t\t\t\tshowProgress: 'bottom'
\t\t\t});
;
\t\t}

\t\tif (countServices > 0) {
\t\t\tfor (let i = 0; i < countServices; i++) {
\t\t\t\tapiKeyItems++;
\t\t\t\tapiKeyItemsCount++;
\t\t\t\thtml += createItem(apiKeyItems, keysValue[i].service, keysValue[i].key);
\t\t\t}
\t\t} else {
\t\t\tapiKeyItems++;
\t\t\tapiKeyItemsCount++;
\t\t\thtml += createItem(apiKeyItems);
\t\t}

\t\thtml += '</div>';

\t\treturn html;
\t}

\tfunction modifyApiKeyVal() {
\t\tlet services = [];

\t\t\$('[name=\"apiKeys\"] .apiItem').each(function () {
\t\t\tlet thisID = \$(this).data('id');
\t\t\tlet service = {
\t\t\t\tkey: \$(document).find('#apiKey-' + thisID).first().val(),
\t\t\t\tservice: \$(document).find('#apiKey-' + thisID + '-service').first().val(),
\t\t\t}
\t\t\tservices.push(service);
\t\t});

\t\t\$(document).find('.aksDd').each(function () {
\t\t\t\$(this).dropdown();
\t\t});
\t\t\$(apiID).val(btoa(JSON.stringify(services)));
\t}

\t\$(() => {
\t\tlet inputs = createKeyInputs();
\t\t\$(parentApiKey).append(inputs);
\t\t\$('.aksDd').dropdown();
\t\t\$(apiID).hide()

\t\t\$(document).on('change', '.apiKey, [data-name=\"apiKeyService\"]', function () {
\t\t\tmodifyApiKeyVal();
\t\t});

\t\t\$(document).on('input', '.apiKey', function () {
\t\t\tmodifyApiKeyVal();
\t\t});

\t\t\$(document).on('click', '[data-action=\"addNewKey\"]', function () {
\t\t\tapiKeyItems++;
\t\t\tapiKeyItemsCount++;
\t\t\tlet item = createItem(apiKeyItems);
\t\t\t\$('[name=\"apiKeys\"]').append(item);
\t\t\tmodifyApiKeyVal();
\t\t});

\t\t\$(document).on('click', '[data-action=\"delThisKey\"]', function () {

\t\t\tif (apiKeyItemsCount > 1) {
\t\t\t\tapiKeyItemsCount--;

\t\t\t\t\$(document).find(this).first().parents('.apiItem').remove();

\t\t\t\tmodifyApiKeyVal();
\t\t\t} else \$('body').toast({
\t\t\t\tclass: 'error',
\t\t\t\ttitle: `Ошибка`,
\t\t\t\tmessage: `Нельзя удалять все поля! Хотя-бы одно да должно остаться!`,
\t\t\t\tshowProgress: 'bottom'
\t\t\t});

\t\t});

\t\t\$(document).on('click', '.save', function () {
\t\t\tmodifyApiKeyVal();
\t\t\t\$(document).find('[name=\"apiKeys\"]').remove();
\t\t\t\$(apiID).show();
\t\t\t\$.ajax({
\t\t\t\turl: 'engine/ajax/controller.php?mod=maharder',
\t\t\t\tdata: {
\t\t\t\t\tuser_hash: '{\$dle_login_hash}',
\t\t\t\t\tmodule: '{\$codename}',
\t\t\t\t\tfile: 'save',
\t\t\t\t\tmethod: 'settings',
\t\t\t\t\tdata: \$('.form').serializeArray()
\t\t\t\t},
\t\t\t\ttype: 'POST',
\t\t\t\tsuccess: function (data) {
\t\t\t\t\tconsole.log(data)
\t\t\t\t\thideLoading('');
\t\t\t\t\t\$('body').toast({
\t\t\t\t\t\tclass: 'success',
\t\t\t\t\t\ttitle: `Всё отлично!`,
\t\t\t\t\t\tmessage: `Данные были сохранены!`,
\t\t\t\t\t\tshowProgress: 'bottom'
\t\t\t\t\t});
\t\t\t\t\t\$(parentApiKey).append(createKeyInputs());
\t\t\t\t\t\$(apiID).hide();

\t\t\t\t}
\t\t\t});
\t\t});
\t});
</script>
";
    }

    public function getTemplateName()
    {
        return "modules/main.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  211 => 243,  208 => 241,  205 => 240,  196 => 232,  193 => 230,  189 => 221,  185 => 219,  179 => 208,  175 => 206,  160 => 181,  156 => 179,  154 => 172,  150 => 170,  148 => 163,  144 => 161,  142 => 154,  138 => 152,  135 => 143,  131 => 141,  129 => 133,  125 => 131,  123 => 120,  119 => 118,  117 => 104,  113 => 102,  110 => 94,  106 => 92,  103 => 84,  99 => 82,  95 => 73,  91 => 71,  88 => 63,  85 => 62,  77 => 55,  73 => 52,  71 => 45,  67 => 43,  65 => 36,  61 => 34,  59 => 27,  56 => 26,  48 => 19,  46 => 4,  42 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html' %} {% block content %}

<div class=\"ui segment\">
\t{{ include('templateIncludes/boxes.html', {
\tboxes: {
\tmain: {
\tlink: '#',
\tname:
\t'Настройки API',
\ticon: 'fad fa-cog'
\t},
\tlang: {
\tlink: '#',
\tname: 'Языковые настройки',
\ticon: 'fad fa-flag'
\t}
\t}
\t})
\t}}
</div>

<div class=\"ui bottom attached tab segment active\" data-tab=\"main\">
\t<h4 class=\"ui dividing header\">Настройки API</h4>
\t<div class=\"ui four column grid form\">
\t\t{% autoescape 'html' %}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'api_url',
\t\tname: 'API-URL',
\t\tdescr: 'Ссылка на API в формате http://сайт.local/api/v1 или http://api.сайт.local/v1',
\t\ttype: 'input',
\t\tvariable: {}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'api_key',
\t\tname: 'API-ключ',
\t\tdescr: 'Введите ключ DLE-API, чтобы скрипт мог корректно обрабатывать запросы.',
\t\ttype: 'input',
\t\tvariable: {}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'list_count',
\t\tname: 'Количество объектов',
\t\tdescr: 'Введите количество объектов, которые будут отображены в списках, таблицах и т.д.. Это глобальное значение для всех модулей автора. При пустом значении будут браться значения из настройки движка \"Количество отоброжаемых новостей на страницу\".',
\t\ttype: 'number',
\t\tvariable: {}
\t\t})
\t\t}}

\t\t{% endautoescape %}
\t</div>
</div>

<div class=\"ui bottom attached tab segment \" data-tab=\"lang\">
\t<h4 class=\"ui dividing header\">Языковые настройки</h4>
\t<div class=\"ui four column grid form\">
\t\t{% autoescape 'html' %}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'multilang',
\t\tname: 'Включить Мультиязычность?',
\t\tdescr: 'При включенном параметре, у сайта появится возможностъ переключать языки.<br><a href=\"#\" role=\"button\"
\t\t\tclass=\"ui primary button\">Список языков</a>',
\t\ttype: 'checkbox',
\t\tvariable: {}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'download_icons',
\t\tname: 'Загружать иконки флажков каждого языка?',
\t\tdescr: 'Для удобства будут испольтоваться иконки сервиса <a href=\"https://www.countryflags.io/\"
\t\t\ttarget=\"_blank\">CountryFlags</a>, если не будет указана ссылка при добавлении / редактировании
\t\tязыка.<br>Если ссылка указана со стороннего сервиса, то сайт попытается её скачать.',
\t\ttype: 'checkbox',
\t\tvariable: {}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'flags_path',
\t\tname: 'Путь загрузки иконок / флажков',
\t\tdescr: 'По умолчанию: uploads/maharder/flags/. Путь указываем от корня сайта без слеша в начале, но со слешем в
\t\tконце.',
\t\ttype: 'input',
\t\tvariable: {}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'unknown',
\t\tname: 'Неизвестный флаг',
\t\tdescr: 'Если на сервисе CountryFlags не найдётся нужного флага, то укажите ссылку на заглужку. По умолчанию:
\t\t/uploads/maharder/flags/shiny/24/unknown.png.',
\t\ttype: 'input',
\t\tvariable: {}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'icon_size',
\t\tname: 'Размер иконок',
\t\tdescr: 'По умолчанию: 24',
\t\ttype: 'select',
\t\tvariable: {},
\t\tvalues: {
\t\t16: '16',
\t\t24: '24',
\t\t32: '32',
\t\t48: '48',
\t\t64: '64'
\t\t}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'icon_style',
\t\tname: 'Тип иконок',
\t\tdescr: 'По умолчанию: shiny',
\t\ttype: 'select',
\t\tvariable: {},
\t\tvalues: {
\t\tflat: 'Плоские',
\t\tshiny: 'Сияющие',
\t\t}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'default_language',
\t\tname: 'Стандартный язык',
\t\tdescr: 'Язык по умолчанию, пока пользователь не переключит его на свой',
\t\ttype: 'select',
\t\tvariable: {},
\t\tvalues: {}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'fallback_language',
\t\tname: 'Запасной язык',
\t\tdescr: 'Если у основного языка не будет перевода или произойдёт какая-либо ошибка, то на какой язык
\t\tпереключить?',
\t\ttype: 'select',
\t\tvariable: {},
\t\tvalues: {}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'path',
\t\tname: 'Путь до языковых файлов',
\t\tdescr: 'Стандартный путь до языковых файлов /locales.',
\t\ttype: 'input',
\t\tvariable: {},
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'cookieField',
\t\tname: 'Переменная для COOKIE',
\t\tdescr: 'Используемый язык будет браться из куки браузера.',
\t\ttype: 'input',
\t\tvariable: {},
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'translator',
\t\tname: 'Включить машиный перевод?',
\t\tdescr: 'При включенном параметре, позволяет переводить из исходного языка в нужный.',
\t\ttype: 'checkbox',
\t\tvariable: {},
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'translateEngine',
\t\tname: 'Движок перевода',
\t\tdescr: 'Какой сервис использовать для перевода текста?<br><br><b
\t\t\tstyle=\"color: #cc0000\">!ВНИМАНИЕ!</b><br>Следующие сервисы имеют следующие ограничения для бесплатного
\t\tиспользование и распространяются через сервис RapidApi!<br><br>
\t\t<ul>
\t\t\t<li><b>Microsoft Translate</b> - 500000 символов в месяц. Подробнее <a
\t\t\t\t\thref=\"https://rapidapi.com/microsoft-azure-org-microsoft-cognitive-services/api/microsoft-translator-text/pricing\"
\t\t\t\t\ttarget=\"_blank\" alt=\"Таблица цен\">здесь</a>.</li>
\t\t\t<li><b>Language Translation</b> - 10 запросов в день, 15000 символов в день. Подробнее <a
\t\t\t\t\thref=\"https://rapidapi.com/cloud-actions-cloud-actions-default/api/language-translation/pricing\"
\t\t\t\t\ttarget=\"_blank\" alt=\"Таблица цен\">здесь</a>.</li>
\t\t\t<li><b>Deep Translate</b> - 100 запросов в месяц, 100000 символов в месяц. Подробнее <a
\t\t\t\t\thref=\"https://rapidapi.com/gatzuma/api/deep-translate1/pricing\" target=\"_blank\"
\t\t\t\t\talt=\"Таблица цен\">здесь</a>.</li>
\t\t</ul>',
\t\ttype: 'select',
\t\tvariable: {},
\t\tvalues: {
\t\trapidapi_microsoft: 'Microsoft Translate (RapidApi)',
\t\trapidapi_language_translation: 'Language Translations (RapidApi)',
\t\trapidapi_deep_translate: 'Deep Translate (RapidApi)'
\t\t}
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'transAPI',
\t\tname: 'API ключ движка перевода',
\t\tdescr: 'API ключ для перевода через сервис. <br>
\t\t<ul class=\"ui list-circle\">
\t\t\t<li class=\"item\"><b>RapidApi</b>: Сборник различных сервисов для различных API. Приобретение ключа находятся
\t\t\t\tна <a href=\"https://rapidapi.com/developer/new\" target=\"_blank\">сайте сервиса</a>.</li>
\t\t</ul> <br>Если найдёте другие популярные и бесплатные сервисы - писать по контактам разработчику.',
\t\ttype: 'input',
\t\tvariable: {},
\t\t})
\t\t}}

\t\t{{ include('templateIncludes/segRow.html', {
\t\tid: 'transWatcher',
\t\tname: 'Автоматизировать отслеживание?',
\t\tdescr: 'На сервере будет создаваться файл с отчётом сколько какой сервис с каким API (ключом) отправил символов
\t\tи запросов. При достижении лимита скрипт будет переключаться на следующий сервис и на следующий ключ API. В
\t\tпротивном случае - при превышении лимита администратору нужно будет переключить вручную сервис',
\t\ttype: 'checkbox',
\t\tvariable: {},
\t\t})
\t\t}}
\t\t{% endautoescape %}
\t</div>
</div>
<div class=\"ui segment\">
\t<div class=\"ui button\" tabindex=\"0\">Сохранить</div>
</div>
{% endblock %}


{% block scripts %}

{% autoescape 'js' %}
<script>
\tvar avApiServices = JSON.parse('{}'), apiID = '#transAPI', parentApiKey = \$(document).find(apiID).first().parent(), apiKeyItems = 0, apiKeyItemsCount = 0;
\tfunction createItem(id, service = '', key = '') {
\t\tlet html = '<div class=\"item apiItem\" data-id=\"' + id + '\"><div class=\"content\"><div class=\"description\"><div class=\"ui right labeled input\"><input class=\"apiKey\" type=\"text\" placeholder=\"API ключ\" name=\"apiKey-' + id + '\" id=\"apiKey-' + id + '\" value=\"' + key + '\"><div class=\"ui dropdown aksDd label\"><input data-name=\"apiKeyService\" type=\"hidden\" name=\"apiKey-' + id + '-service\" id=\"apiKey-' + id + '-service\">';
\t\tif (service != '') html += '<div class=\"text\">' + avApiServices[service] + '</div>';
\t\telse html += '<div class=\"text\">Выбрать сервис</div>';
\t\thtml += '<i class=\"dropdown icon\"></i><div class=\"menu\">';
\t\t\$.each(avApiServices, function (key, value) {
\t\t\thtml += '<div class=\"item\" data-value=\"' + key + '\">' + value + '</div>';
\t\t});
\t\thtml += '</div></div></div><div class=\"ui icon buttons\"><div role=\"button\" class=\"ui green button\" data-action=\"addNewKey\" title=\"Добавить новый ключ\"><i class=\"plus icon\"></i></div><div role=\"button\" class=\"ui red button\" data-action=\"delThisKey\" data-id=\"' + id + '\" title=\"Удалить ключ\"><i class=\"minus icon\"></i></div></div></div></div></div>';

\t\treturn html;
\t}

\tfunction createKeyInputs() {
\t\tlet html = '<div class=\"ui items\" name=\"apiKeys\">';
\t\tlet keysValue = {}, countServices = 0;
\t\ttry {
\t\t\tkeysValue = JSON.parse(atob(\$(apiID).val()));
\t\t\tcountServices = keysValue.length;
\t\t} catch (e) {
\t\t\tconsole.log('No API keys insert');
\t\t\t\$('body').toast({
\t\t\t\tclass: 'error',
\t\t\t\ttitle: `Ошибка`,
\t\t\t\tmessage: `Нет действующих ключей!`,
\t\t\t\tshowProgress: 'bottom'
\t\t\t});
;
\t\t}

\t\tif (countServices > 0) {
\t\t\tfor (let i = 0; i < countServices; i++) {
\t\t\t\tapiKeyItems++;
\t\t\t\tapiKeyItemsCount++;
\t\t\t\thtml += createItem(apiKeyItems, keysValue[i].service, keysValue[i].key);
\t\t\t}
\t\t} else {
\t\t\tapiKeyItems++;
\t\t\tapiKeyItemsCount++;
\t\t\thtml += createItem(apiKeyItems);
\t\t}

\t\thtml += '</div>';

\t\treturn html;
\t}

\tfunction modifyApiKeyVal() {
\t\tlet services = [];

\t\t\$('[name=\"apiKeys\"] .apiItem').each(function () {
\t\t\tlet thisID = \$(this).data('id');
\t\t\tlet service = {
\t\t\t\tkey: \$(document).find('#apiKey-' + thisID).first().val(),
\t\t\t\tservice: \$(document).find('#apiKey-' + thisID + '-service').first().val(),
\t\t\t}
\t\t\tservices.push(service);
\t\t});

\t\t\$(document).find('.aksDd').each(function () {
\t\t\t\$(this).dropdown();
\t\t});
\t\t\$(apiID).val(btoa(JSON.stringify(services)));
\t}

\t\$(() => {
\t\tlet inputs = createKeyInputs();
\t\t\$(parentApiKey).append(inputs);
\t\t\$('.aksDd').dropdown();
\t\t\$(apiID).hide()

\t\t\$(document).on('change', '.apiKey, [data-name=\"apiKeyService\"]', function () {
\t\t\tmodifyApiKeyVal();
\t\t});

\t\t\$(document).on('input', '.apiKey', function () {
\t\t\tmodifyApiKeyVal();
\t\t});

\t\t\$(document).on('click', '[data-action=\"addNewKey\"]', function () {
\t\t\tapiKeyItems++;
\t\t\tapiKeyItemsCount++;
\t\t\tlet item = createItem(apiKeyItems);
\t\t\t\$('[name=\"apiKeys\"]').append(item);
\t\t\tmodifyApiKeyVal();
\t\t});

\t\t\$(document).on('click', '[data-action=\"delThisKey\"]', function () {

\t\t\tif (apiKeyItemsCount > 1) {
\t\t\t\tapiKeyItemsCount--;

\t\t\t\t\$(document).find(this).first().parents('.apiItem').remove();

\t\t\t\tmodifyApiKeyVal();
\t\t\t} else \$('body').toast({
\t\t\t\tclass: 'error',
\t\t\t\ttitle: `Ошибка`,
\t\t\t\tmessage: `Нельзя удалять все поля! Хотя-бы одно да должно остаться!`,
\t\t\t\tshowProgress: 'bottom'
\t\t\t});

\t\t});

\t\t\$(document).on('click', '.save', function () {
\t\t\tmodifyApiKeyVal();
\t\t\t\$(document).find('[name=\"apiKeys\"]').remove();
\t\t\t\$(apiID).show();
\t\t\t\$.ajax({
\t\t\t\turl: 'engine/ajax/controller.php?mod=maharder',
\t\t\t\tdata: {
\t\t\t\t\tuser_hash: '{\$dle_login_hash}',
\t\t\t\t\tmodule: '{\$codename}',
\t\t\t\t\tfile: 'save',
\t\t\t\t\tmethod: 'settings',
\t\t\t\t\tdata: \$('.form').serializeArray()
\t\t\t\t},
\t\t\t\ttype: 'POST',
\t\t\t\tsuccess: function (data) {
\t\t\t\t\tconsole.log(data)
\t\t\t\t\thideLoading('');
\t\t\t\t\t\$('body').toast({
\t\t\t\t\t\tclass: 'success',
\t\t\t\t\t\ttitle: `Всё отлично!`,
\t\t\t\t\t\tmessage: `Данные были сохранены!`,
\t\t\t\t\t\tshowProgress: 'bottom'
\t\t\t\t\t});
\t\t\t\t\t\$(parentApiKey).append(createKeyInputs());
\t\t\t\t\t\$(apiID).hide();

\t\t\t\t}
\t\t\t});
\t\t});
\t});
</script>
{% endautoescape %}
{% endblock %}
", "modules/main.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\modules\\main.html");
    }
}
