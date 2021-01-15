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

/* modules/langList.html */
class __TwigTemplate_c56ba5aa0325192c5c38bea1f3e46e63e1c77591332fb4acbce8dbd0475a833d extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $this->parent = $this->loadTemplate("base.html", "modules/langList.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    public function block_content($context, array $blocks = [])
    {
        // line 2
        echo "

<div class=\"ui  segment \">
\t<h4 class=\"ui dividing header\">Список языков</h4>
\t<table class=\"ui celled striped selectable stackable basic table\">
\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th>ID</th>
\t\t\t\t<th>Код</th>
\t\t\t\t<th>ISO-2</th>
\t\t\t\t<th>Название</th>
\t\t\t\t<th>Папка</th>
\t\t\t\t<th>Флаг</th>
\t\t\t\t<th>Доступен</th>
\t\t\t\t<th>Действие</th>
\t\t\t</tr>
\t\t</thead>
\t\t<tbody>
\t\t\t";
        // line 20
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["langlist"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["lang"]) {
            // line 21
            echo "\t\t\t<tr>
\t\t\t\t<td>";
            // line 22
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "id", []), "html", null, true);
            echo "</td>
\t\t\t\t<td>";
            // line 23
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "code", []), "html", null, true);
            echo "</td>
\t\t\t\t<td>";
            // line 24
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "iso2", []), "html", null, true);
            echo "</td>
\t\t\t\t<td>";
            // line 25
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "name", []), "html", null, true);
            echo " (";
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "int", []), "html", null, true);
            echo ")</td>
\t\t\t\t<td>";
            // line 26
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "folder", []), "html", null, true);
            echo "</td>
\t\t\t\t<td>";
            // line 27
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "flag", []), "html", null, true);
            echo "</td>
\t\t\t\t<td>";
            // line 28
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "active", []), "html", null, true);
            echo "</td>
\t\t\t\t<td>
\t\t\t\t\t<div class=\"ui icon pointing dropdown link item\">
\t\t\t\t\t\t<i class=\"wrench icon\"></i>
\t\t\t\t\t\t<span class=\"text\">Действие</span>
\t\t\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t\t\t<div class=\"menu\">
\t\t\t\t\t\t\t<a data-action='list' class=' act_btn item' role='button' data-id='";
            // line 35
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "id", []), "html", null, true);
            echo "'><i
\t\t\t\t\t\t\t\t\tclass=\"far fa-language\"></i> Перевести</a>
\t\t\t\t\t\t\t<a data-action='save' class=' act_btn item' role='button' data-id='";
            // line 37
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "id", []), "html", null, true);
            echo "'><i
\t\t\t\t\t\t\t\t\tclass='fas fa-check'></i> Сохранить</a>
\t\t\t\t\t\t\t<a data-action='delete' class='act_btn item' role='button' data-id='";
            // line 39
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "id", []), "html", null, true);
            echo "'><i
\t\t\t\t\t\t\t\t\tclass='fas fa-trash'></i> Удалить</a>
\t\t\t\t\t\t\t<a data-action='redo' data-lang='";
            // line 41
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "iso2", []), "html", null, true);
            echo "' class='act_btn item' role='button'
\t\t\t\t\t\t\t\tdata-id='";
            // line 42
            echo twig_escape_filter($this->env, $this->getAttribute($context["lang"], "id", []), "html", null, true);
            echo "'><i class='fas fa-refresh'></i> Пересоздать</a>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['lang'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 48
        echo "\t\t</tbody>
\t\t<tfoot>
\t\t\t<tr>
\t\t\t\t<th colspan=\"8\">
\t\t\t\t\t<div class=\"ui right floated pagination menu\">
\t\t\t\t\t\t<a class=\"icon item\">
\t\t\t\t\t\t\t<i class=\"left chevron icon\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t\t<a class=\"item\">1</a>
\t\t\t\t\t\t<a class=\"item\">2</a>
\t\t\t\t\t\t<a class=\"item\">3</a>
\t\t\t\t\t\t<a class=\"item\">4</a>
\t\t\t\t\t\t<a class=\"icon item\">
\t\t\t\t\t\t\t<i class=\"right chevron icon\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</div>
\t\t\t\t</th>
\t\t\t</tr>
\t\t</tfoot>
\t</table>
</div>


";
    }

    public function getTemplateName()
    {
        return "modules/langList.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  135 => 48,  123 => 42,  119 => 41,  114 => 39,  109 => 37,  104 => 35,  94 => 28,  90 => 27,  86 => 26,  80 => 25,  76 => 24,  72 => 23,  68 => 22,  65 => 21,  61 => 20,  41 => 2,  29 => 1,);
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


<div class=\"ui  segment \">
\t<h4 class=\"ui dividing header\">Список языков</h4>
\t<table class=\"ui celled striped selectable stackable basic table\">
\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th>ID</th>
\t\t\t\t<th>Код</th>
\t\t\t\t<th>ISO-2</th>
\t\t\t\t<th>Название</th>
\t\t\t\t<th>Папка</th>
\t\t\t\t<th>Флаг</th>
\t\t\t\t<th>Доступен</th>
\t\t\t\t<th>Действие</th>
\t\t\t</tr>
\t\t</thead>
\t\t<tbody>
\t\t\t{% for lang in langlist %}
\t\t\t<tr>
\t\t\t\t<td>{{ lang.id }}</td>
\t\t\t\t<td>{{ lang.code }}</td>
\t\t\t\t<td>{{ lang.iso2 }}</td>
\t\t\t\t<td>{{ lang.name }} ({{ lang.int }})</td>
\t\t\t\t<td>{{ lang.folder }}</td>
\t\t\t\t<td>{{ lang.flag }}</td>
\t\t\t\t<td>{{ lang.active }}</td>
\t\t\t\t<td>
\t\t\t\t\t<div class=\"ui icon pointing dropdown link item\">
\t\t\t\t\t\t<i class=\"wrench icon\"></i>
\t\t\t\t\t\t<span class=\"text\">Действие</span>
\t\t\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t\t\t<div class=\"menu\">
\t\t\t\t\t\t\t<a data-action='list' class=' act_btn item' role='button' data-id='{{lang.id}}'><i
\t\t\t\t\t\t\t\t\tclass=\"far fa-language\"></i> Перевести</a>
\t\t\t\t\t\t\t<a data-action='save' class=' act_btn item' role='button' data-id='{{lang.id}}'><i
\t\t\t\t\t\t\t\t\tclass='fas fa-check'></i> Сохранить</a>
\t\t\t\t\t\t\t<a data-action='delete' class='act_btn item' role='button' data-id='{{lang.id}}'><i
\t\t\t\t\t\t\t\t\tclass='fas fa-trash'></i> Удалить</a>
\t\t\t\t\t\t\t<a data-action='redo' data-lang='{{lang.iso2}}' class='act_btn item' role='button'
\t\t\t\t\t\t\t\tdata-id='{{lang.id}}'><i class='fas fa-refresh'></i> Пересоздать</a>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</td>
\t\t\t</tr>
\t\t\t{% endfor %}
\t\t</tbody>
\t\t<tfoot>
\t\t\t<tr>
\t\t\t\t<th colspan=\"8\">
\t\t\t\t\t<div class=\"ui right floated pagination menu\">
\t\t\t\t\t\t<a class=\"icon item\">
\t\t\t\t\t\t\t<i class=\"left chevron icon\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t\t<a class=\"item\">1</a>
\t\t\t\t\t\t<a class=\"item\">2</a>
\t\t\t\t\t\t<a class=\"item\">3</a>
\t\t\t\t\t\t<a class=\"item\">4</a>
\t\t\t\t\t\t<a class=\"icon item\">
\t\t\t\t\t\t\t<i class=\"right chevron icon\"></i>
\t\t\t\t\t\t</a>
\t\t\t\t\t</div>
\t\t\t\t</th>
\t\t\t</tr>
\t\t</tfoot>
\t</table>
</div>


{% endblock %}
", "modules/langList.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\modules\\langList.html");
    }
}
