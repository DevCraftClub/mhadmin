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

/* base.html */
class __TwigTemplate_b3ad63568c3d1fe4aef587bfb757ac47c6c023d6b33639535462b71dc23ac3d4 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
            'scripts' => [$this, 'block_scripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<!DOCTYPE html>

<html>

<head>
\t";
        // line 6
        $this->displayBlock('title', $context, $blocks);
        // line 7
        echo "\t<meta charset=\"UTF-8\">
\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0\">
\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
\t";
        // line 11
        echo "\t";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["css"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["style"]) {
            // line 12
            echo "\t";
            echo $context["style"];
            echo "
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['style'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 14
        echo "\t";
        // line 15
        echo "</head>

<body>

\t";
        // line 19
        $this->loadTemplate("sidebar.html", "base.html", 19)->display($context);
        // line 20
        echo "
\t<div class=\"ui main container\">
\t\t<h1 class=\"ui header\">";
        // line 22
        echo twig_escape_filter($this->env, ($context["module_name"] ?? null), "html", null, true);
        echo " v";
        echo twig_escape_filter($this->env, ($context["module_version"] ?? null), "html", null, true);
        echo "</h1>
\t\t<p>";
        // line 23
        echo twig_escape_filter($this->env, ($context["module_description"] ?? null), "html", null, true);
        echo "</p>
\t</div>

\t";
        // line 26
        $this->loadTemplate("menu.html", "base.html", 26)->display($context);
        // line 27
        echo "
\t<div class=\"ui container dimmed pusher\">
\t\t<div class=\"ui segment\">
\t\t\t";
        // line 30
        $this->loadTemplate("breadcrumb.html", "base.html", 30)->display($context);
        // line 31
        echo "\t\t</div>
\t\t<div class=\"ui piled segments\">
\t\t\t";
        // line 33
        $this->displayBlock('content', $context, $blocks);
        // line 34
        echo "\t\t</div>

\t\t";
        // line 36
        echo twig_include($this->env, $context, "templateIncludes/loader.html", ["text" => "Подождите, идёт загрузка..."]);
        echo "
\t</div>
\t";
        // line 38
        $this->loadTemplate("footer.html", "base.html", 38)->display($context);
        // line 39
        echo "\t";
        // line 40
        echo "\t";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["js"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["script"]) {
            // line 41
            echo "\t";
            echo $context["script"];
            echo "
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['script'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 43
        echo "\t";
        // line 44
        echo "\t";
        $this->displayBlock('scripts', $context, $blocks);
        // line 45
        echo "</body>

</html>
";
    }

    // line 6
    public function block_title($context, array $blocks = [])
    {
        echo "<title>";
        echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
        echo "</title>";
    }

    // line 33
    public function block_content($context, array $blocks = [])
    {
    }

    // line 44
    public function block_scripts($context, array $blocks = [])
    {
    }

    public function getTemplateName()
    {
        return "base.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  154 => 44,  149 => 33,  141 => 6,  134 => 45,  131 => 44,  129 => 43,  120 => 41,  115 => 40,  113 => 39,  111 => 38,  106 => 36,  102 => 34,  100 => 33,  96 => 31,  94 => 30,  89 => 27,  87 => 26,  81 => 23,  75 => 22,  71 => 20,  69 => 19,  63 => 15,  61 => 14,  52 => 12,  47 => 11,  42 => 7,  40 => 6,  33 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>

<html>

<head>
\t{% block title %}<title>{{ title }}</title>{% endblock %}
\t<meta charset=\"UTF-8\">
\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0\">
\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
\t{% autoescape 'html' %}
\t{% for style in css %}
\t{{ style|raw }}
\t{% endfor %}
\t{% endautoescape %}
</head>

<body>

\t{% include 'sidebar.html' %}

\t<div class=\"ui main container\">
\t\t<h1 class=\"ui header\">{{ module_name }} v{{ module_version }}</h1>
\t\t<p>{{ module_description }}</p>
\t</div>

\t{% include 'menu.html' %}

\t<div class=\"ui container dimmed pusher\">
\t\t<div class=\"ui segment\">
\t\t\t{% include 'breadcrumb.html' %}
\t\t</div>
\t\t<div class=\"ui piled segments\">
\t\t\t{% block content %}{% endblock %}
\t\t</div>

\t\t{{ include('templateIncludes/loader.html', {text: 'Подождите, идёт загрузка...'})}}
\t</div>
\t{% include 'footer.html' %}
\t{% autoescape 'html' %}
\t{% for script in js %}
\t{{ script|raw }}
\t{% endfor %}
\t{% endautoescape %}
\t{% block scripts %}{% endblock %}
</body>

</html>
", "base.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\base.html");
    }
}
