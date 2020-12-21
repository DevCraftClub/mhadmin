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
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<!DOCTYPE html>

<html>
\t<head>
\t\t";
        // line 5
        $this->displayBlock('title', $context, $blocks);
        // line 6
        echo "\t\t<meta charset=\"UTF-8\">
\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0\">
\t\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
\t\t";
        // line 10
        echo "\t\t\t";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["css"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["style"]) {
            // line 11
            echo "\t\t\t\t";
            echo $context["style"];
            echo "
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['style'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "\t\t";
        // line 14
        echo "\t</head>
\t<body>

\t\t";
        // line 17
        $this->loadTemplate("sidebar.html", "base.html", 17)->display($context);
        // line 18
        echo "
\t\t<div class=\"ui main container\">
\t\t\t<h1 class=\"ui header\">";
        // line 20
        echo twig_escape_filter($this->env, ($context["module_name"] ?? null), "html", null, true);
        echo " v";
        echo twig_escape_filter($this->env, ($context["module_version"] ?? null), "html", null, true);
        echo "</h1>
\t\t\t<p>";
        // line 21
        echo twig_escape_filter($this->env, ($context["module_description"] ?? null), "html", null, true);
        echo "</p>
\t\t</div>

\t\t";
        // line 24
        $this->loadTemplate("menu.html", "base.html", 24)->display($context);
        // line 25
        echo "
\t\t<div class=\"ui container dimmed pusher\">
\t\t\t<div class=\"ui segment\">
\t\t\t\t";
        // line 28
        $this->loadTemplate("breadcrumb.html", "base.html", 28)->display($context);
        // line 29
        echo "\t\t\t</div>
\t\t\t<div class=\"ui piled segments\">
\t\t\t\t";
        // line 31
        $this->displayBlock('content', $context, $blocks);
        // line 32
        echo "\t\t\t</div>
\t\t</div>
\t\t";
        // line 34
        $this->loadTemplate("footer.html", "base.html", 34)->display($context);
        // line 35
        echo "\t\t";
        // line 36
        echo "\t\t\t";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["js"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["script"]) {
            // line 37
            echo "\t\t\t\t";
            echo $context["script"];
            echo "
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['script'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 39
        echo "\t\t";
        // line 40
        echo "\t</body>
</html>
";
    }

    // line 5
    public function block_title($context, array $blocks = [])
    {
        echo "<title>";
        echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
        echo "</title>";
    }

    // line 31
    public function block_content($context, array $blocks = [])
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
        return array (  137 => 31,  129 => 5,  123 => 40,  121 => 39,  112 => 37,  107 => 36,  105 => 35,  103 => 34,  99 => 32,  97 => 31,  93 => 29,  91 => 28,  86 => 25,  84 => 24,  78 => 21,  72 => 20,  68 => 18,  66 => 17,  61 => 14,  59 => 13,  50 => 11,  45 => 10,  40 => 6,  38 => 5,  32 => 1,);
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
\t<head>
\t\t{% block title %}<title>{{ title }}</title>{% endblock %}
\t\t<meta charset=\"UTF-8\">
\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0\">
\t\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
\t\t{% autoescape 'html' %}
\t\t\t{% for style in css %}
\t\t\t\t{{ style|raw }}
\t\t\t{% endfor %}
\t\t{% endautoescape %}
\t</head>
\t<body>

\t\t{% include 'sidebar.html' %}

\t\t<div class=\"ui main container\">
\t\t\t<h1 class=\"ui header\">{{ module_name }} v{{ module_version }}</h1>
\t\t\t<p>{{ module_description }}</p>
\t\t</div>

\t\t{% include 'menu.html' %}

\t\t<div class=\"ui container dimmed pusher\">
\t\t\t<div class=\"ui segment\">
\t\t\t\t{% include 'breadcrumb.html' %}
\t\t\t</div>
\t\t\t<div class=\"ui piled segments\">
\t\t\t\t{% block content %}{% endblock %}
\t\t\t</div>
\t\t</div>
\t\t{% include 'footer.html' %}
\t\t{% autoescape 'html' %}
\t\t\t{% for script in js %}
\t\t\t\t{{ script|raw }}
\t\t\t{% endfor %}
\t\t{% endautoescape %}
\t</body>
</html>
", "base.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\base.html");
    }
}
