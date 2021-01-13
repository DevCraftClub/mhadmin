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

/* templateIncludes/segRow.html */
class __TwigTemplate_fafd476956a7bd01d23073a167e6750db1da5f6075e3de32e4e569a6796dbe0a extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<div class=\"two column row\">
\t<div class=\"four wide column\">
\t\t<label for=\"";
        // line 3
        echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "</label><br>
\t\t<small>";
        // line 4
        echo twig_escape_filter($this->env, ($context["descr"] ?? null), "html", null, true);
        echo "</small>
\t</div>
\t<div class=\"twelve wide column\">
\t\t";
        // line 7
        if (((($context["type"] ?? null) == "input") || (($context["type"] ?? null) == "tag_input"))) {
            // line 8
            echo "\t\t";
            echo twig_include($this->env, $context, "templateIncludes/addInput.html", ["type" => "text", "name" =>             // line 10
($context["id"] ?? null), "label" =>             // line 11
($context["name"] ?? null), "value" => $this->getAttribute(            // line 12
($context["variable"] ?? null), ($context["id"] ?? null), [], "array"), "chosen" => $this->getAttribute(            // line 13
($context["extra"] ?? null), "chosen", [], "array")]);
            // line 14
            echo "
\t\t";
        } elseif ((        // line 15
($context["type"] ?? null) == "number_input")) {
            // line 16
            echo "\t\t";
            echo twig_include($this->env, $context, "templateIncludes/addInput.html", ["type" => "number", "name" => ($context["id"] ?? null), "label" => ($context["name"] ?? null), "value" => $this->getAttribute(($context["variable"] ?? null), ($context["id"] ?? null), [], "array"), "chosen" => false]);
            // line 18
            echo "
\t\t";
        } elseif ((        // line 19
($context["type"] ?? null) == "hidden_input")) {
            // line 20
            echo "\t\t";
            echo twig_include($this->env, $context, "templateIncludes/addInput.html", ["type" => "hidden", "name" => ($context["id"] ?? null), "label" => ($context["name"] ?? null), "value" => $this->getAttribute(($context["variable"] ?? null), ($context["id"] ?? null), [], "array"), "chosen" => false]);
            // line 22
            echo "
\t\t";
        } elseif ((        // line 23
($context["type"] ?? null) == "input_file")) {
            // line 24
            echo "\t\t";
            echo twig_include($this->env, $context, "templateIncludes/addInput.html", ["type" => "file", "name" => ($context["id"] ?? null), "label" => ($context["name"] ?? null), "value" => $this->getAttribute(($context["variable"] ?? null), ($context["id"] ?? null), [], "array"), "chosen" => false]);
            // line 25
            echo "
\t\t";
        } elseif ((        // line 26
($context["type"] ?? null) == "select")) {
            // line 27
            echo "\t\t";
            echo twig_include($this->env, $context, "templateIncludes/addSelect.html", ["name" => ($context["id"] ?? null), "label" => ($context["name"] ?? null), "values" => ($context["variable"] ?? null), "multiple" => ($context["multiple"] ?? null), "selected" =>             // line 28
($context["selected"] ?? null)]);
            echo "
\t\t";
        } elseif ((        // line 29
($context["type"] ?? null) == "textarea")) {
            // line 30
            echo "\t\t";
            echo twig_include($this->env, $context, "templateIncludes/addTextarea.html", ["name" => ($context["id"] ?? null), "label" => ($context["name"] ?? null), "value" => $this->getAttribute(($context["variable"] ?? null), ($context["id"] ?? null), [], "array"), "editor" => false]);
            echo "
\t\t";
        } elseif ((        // line 31
($context["type"] ?? null) == "editor")) {
            // line 32
            echo "\t\t";
            echo twig_include($this->env, $context, "templateIncludes/addTextarea.html", ["name" => ($context["id"] ?? null), "label" => ($context["name"] ?? null), "value" => $this->getAttribute(($context["variable"] ?? null), ($context["id"] ?? null), [], "array"), "editor" => true]);
            echo "
\t\t";
        } elseif ((        // line 33
($context["type"] ?? null) == "checkbox")) {
            // line 34
            echo "\t\t";
            echo twig_include($this->env, $context, "templateIncludes/addCheckbox.html", ["name" => ($context["id"] ?? null), "label" => ($context["name"] ?? null), "selected" => $this->getAttribute(($context["variable"] ?? null), ($context["id"] ?? null), [], "array")]);
            echo "
\t\t";
        }
        // line 36
        echo "\t</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "templateIncludes/segRow.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  112 => 36,  106 => 34,  104 => 33,  99 => 32,  97 => 31,  92 => 30,  90 => 29,  86 => 28,  84 => 27,  82 => 26,  79 => 25,  76 => 24,  74 => 23,  71 => 22,  68 => 20,  66 => 19,  63 => 18,  60 => 16,  58 => 15,  55 => 14,  53 => 13,  52 => 12,  51 => 11,  50 => 10,  48 => 8,  46 => 7,  40 => 4,  34 => 3,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"two column row\">
\t<div class=\"four wide column\">
\t\t<label for=\"{{id}}\">{{name}}</label><br>
\t\t<small>{{descr}}</small>
\t</div>
\t<div class=\"twelve wide column\">
\t\t{% if type == 'input' or type == 'tag_input' %}
\t\t{{ include('templateIncludes/addInput.html', {
\t\ttype: 'text',
\t\tname: id,
\t\tlabel: name,
\t\tvalue: variable[id],
\t\tchosen: extra['chosen']
\t\t})}}
\t\t{% elseif type == 'number_input'%}
\t\t{{ include('templateIncludes/addInput.html', { type: 'number', name: id, label: name, value: variable[id],
\t\tchosen:
\t\tfalse})}}
\t\t{% elseif type == 'hidden_input'%}
\t\t{{ include('templateIncludes/addInput.html', { type: 'hidden', name: id, label: name, value: variable[id],
\t\tchosen:
\t\tfalse})}}
\t\t{% elseif type == 'input_file'%}
\t\t{{ include('templateIncludes/addInput.html', { type: 'file', name: id, label: name, value: variable[id], chosen:
\t\tfalse})}}
\t\t{% elseif type == 'select'%}
\t\t{{ include('templateIncludes/addSelect.html', { name: id, label: name, values: variable, multiple: multiple,
\t\tselected: selected})}}
\t\t{% elseif type == 'textarea'%}
\t\t{{ include('templateIncludes/addTextarea.html', { name: id, label: name, value: variable[id]|raw, editor: false})}}
\t\t{% elseif type == 'editor'%}
\t\t{{ include('templateIncludes/addTextarea.html', { name: id, label: name, value: variable[id]|raw, editor: true})}}
\t\t{% elseif type == 'checkbox'%}
\t\t{{ include('templateIncludes/addCheckbox.html', { name: id, label: name, selected: variable[id]})}}
\t\t{%endif%}
\t</div>
</div>
", "templateIncludes/segRow.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\templateIncludes\\segRow.html");
    }
}
