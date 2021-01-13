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

/* modules/changelog.html */
class __TwigTemplate_3d6a9b5b2b5865240d89849c8582fba730a518e5e0c9e8bc976b063cc96de647 extends \Twig\Template
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
        $this->parent = $this->loadTemplate("base.html", "modules/changelog.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = [])
    {
        // line 3
        echo "<div class=\"ui segment\">
\t<div class=\"ui accordion\">
\t\t";
        // line 5
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["logs"] ?? null));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["name"] => $context["log"]) {
            // line 6
            echo "\t\t<div class=\"";
            if ($this->getAttribute($context["loop"], "first", [])) {
                echo "active ";
            }
            echo "title\">
\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t";
            // line 8
            echo twig_escape_filter($this->env, $context["name"], "html", null, true);
            echo "
\t\t</div>
\t\t<div class=\"";
            // line 10
            if ($this->getAttribute($context["loop"], "first", [])) {
                echo "active ";
            }
            echo "content\">
\t\t\t<div class=\"ui divided list\">
\t\t\t\t";
            // line 12
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($context["log"]);
            foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
                // line 13
                echo "\t\t\t\t<div class=\"item\">";
                echo twig_escape_filter($this->env, $context["l"], "html", null, true);
                echo "</div>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 15
            echo "\t\t\t</div>
\t\t</div>
\t\t";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['name'], $context['log'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 18
        echo "\t</div>

\t<ul>
\t\t";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["bsp"] ?? null));
        foreach ($context['_seq'] as $context["i"] => $context["k"]) {
            // line 22
            echo "\t\t<li>";
            if (twig_test_iterable($context["k"])) {
                // line 23
                echo "\t\t\t";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($context["k"]);
                foreach ($context['_seq'] as $context["ki"] => $context["kk"]) {
                    // line 24
                    echo "\t\t\t-- (";
                    echo twig_escape_filter($this->env, $context["kk"], "html", null, true);
                    echo ")
\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['ki'], $context['kk'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 26
                echo "\t\t\t";
            } else {
                // line 27
                echo "\t\t\t";
                echo twig_escape_filter($this->env, $context["k"], "html", null, true);
                echo "
\t\t\t";
            }
            // line 29
            echo "\t\t</li>
\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['i'], $context['k'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        echo "\t</ul>
\t<div class=\"ui form\">
\t";
        // line 34
        echo "\t";
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "test", "name" => "Testinput", "descr" => "Testdescr", "type" => "input", "extra" => ["chosen" => true], "variable" => ["test" => "Testvalue"]]);
        // line 46
        echo "
\t";
        // line 47
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "testSelect", "name" => "Test Selector", "type" => "select", "variable" => ["test" => "test", "test2" => ["test3" => "Test 3", "tdes" => "ASdd"]], "selected" => "test", "multiple" => true]);
        // line 55
        echo "
\t";
        // line 56
        echo twig_include($this->env, $context, "templateIncludes/segRow.html", ["id" => "testCheckBox", "name" => "Test Selector", "type" => "checkbox", "variable" => ["test" => "test", "test2" => ["test3" => "Test 3", "tdes" => "ASdd"]], "selected" => "test"]);
        // line 63
        echo "

\t";
        // line 66
        echo "\t</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/changelog.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  175 => 66,  171 => 63,  169 => 56,  166 => 55,  164 => 47,  161 => 46,  158 => 34,  154 => 31,  147 => 29,  141 => 27,  138 => 26,  129 => 24,  124 => 23,  121 => 22,  117 => 21,  112 => 18,  96 => 15,  87 => 13,  83 => 12,  76 => 10,  71 => 8,  63 => 6,  46 => 5,  42 => 3,  39 => 2,  29 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html' %}
{% block content %}
<div class=\"ui segment\">
\t<div class=\"ui accordion\">
\t\t{% for name, log in logs %}
\t\t<div class=\"{% if loop.first %}active {% endif %}title\">
\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t{{name}}
\t\t</div>
\t\t<div class=\"{% if loop.first %}active {% endif %}content\">
\t\t\t<div class=\"ui divided list\">
\t\t\t\t{% for l in log %}
\t\t\t\t<div class=\"item\">{{l}}</div>
\t\t\t\t{% endfor %}
\t\t\t</div>
\t\t</div>
\t\t{% endfor %}
\t</div>

\t<ul>
\t\t{% for i,k in bsp %}
\t\t<li>{%if k is iterable %}
\t\t\t{%for ki,kk in k%}
\t\t\t-- ({{kk}})
\t\t\t{%endfor%}
\t\t\t{%else%}
\t\t\t{{k}}
\t\t\t{%endif%}
\t\t</li>
\t\t{% endfor %}
\t</ul>
\t<div class=\"ui form\">
\t{% autoescape 'html' %}
\t{{
\tinclude('templateIncludes/segRow.html', {
\tid: 'test',
\tname: 'Testinput',
\tdescr: 'Testdescr',
\ttype: 'input',
\textra: {
\tchosen: true
\t}, variable: {
\ttest: 'Testvalue'
\t}
\t})
\t}}
\t{{ include('templateIncludes/segRow.html', {
\tid: 'testSelect',
\tname: 'Test Selector',
\ttype: 'select',
\tvariable: {test: 'test', test2: {test3:
\t'Test 3', tdes: 'ASdd'}},
\tselected: 'test',
\tmultiple: true
\t})}}
\t{{ include('templateIncludes/segRow.html', {
\tid: 'testCheckBox',
\tname: 'Test Selector',
\ttype: 'checkbox',
\tvariable: {test: 'test', test2: {test3:
\t'Test 3', tdes: 'ASdd'}},
\tselected: 'test',
\t})}}

\t{% endautoescape %}
\t</div>
</div>
{% endblock %}
", "modules/changelog.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\modules\\changelog.html");
    }
}
