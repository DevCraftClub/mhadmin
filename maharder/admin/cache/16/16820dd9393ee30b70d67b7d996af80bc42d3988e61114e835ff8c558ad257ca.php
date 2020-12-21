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

/* menu.html */
class __TwigTemplate_60f799bfac000c4220ef96e722869bbaf05073fb4c686e59660955c681496ea8 extends \Twig\Template
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
        echo "<div class=\"ui borderless main menu \">
\t<div class=\"ui container\">
\t\t<div class=\"header item firstLine\">
\t\t\t<i class=\"icon ";
        // line 4
        echo twig_escape_filter($this->env, ($context["module_icon"] ?? null), "html", null, true);
        echo "\"></i>&nbsp;&nbsp;
\t\t\t";
        // line 5
        echo twig_escape_filter($this->env, ($context["module_name"] ?? null), "html", null, true);
        echo "
\t\t</div>
\t\t";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["links"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["link"]) {
            // line 8
            echo "\t\t";
            if (($this->getAttribute($context["link"], "type", []) == "dropdown")) {
                // line 9
                echo "\t\t<div class=\"ui dropdown item firstLine\" tabindex=\"0\">
\t\t\t";
                // line 10
                echo twig_escape_filter($this->env, $this->getAttribute($context["link"], "name", []), "html", null, true);
                echo " <i class=\"dropdown icon\"></i>
\t\t\t<div class=\"menu\" tabindex=\"0\">
\t\t\t\t";
                // line 12
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["link"], "children", []));
                foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                    // line 13
                    echo "\t\t\t\t";
                    if (($this->getAttribute($context["child"], "type", []) == "dropdown")) {
                        // line 14
                        echo "\t\t\t\t<div class=\"item\">
\t\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t\t";
                        // line 16
                        echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "name", []), "html", null, true);
                        echo "
\t\t\t\t\t<div class=\"menu\">
\t\t\t\t\t\t";
                        // line 18
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["child"], "children", []));
                        foreach ($context['_seq'] as $context["_key"] => $context["subchild"]) {
                            // line 19
                            echo "\t\t\t\t\t\t<a href=\"";
                            echo twig_escape_filter($this->env, $this->getAttribute($context["subchild"], "href", []), "html", null, true);
                            echo "\" class=\"item\">";
                            echo twig_escape_filter($this->env, $this->getAttribute($context["subchild"], "name", []), "html", null, true);
                            echo "</a>
\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subchild'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 21
                        echo "\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t";
                    } elseif (($this->getAttribute(                    // line 23
$context["child"], "type", []) == "divider")) {
                        // line 24
                        echo "\t\t\t\t<div class=\"divider\"></div>
\t\t\t\t";
                    } else {
                        // line 26
                        echo "\t\t\t\t<a href=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "href", []), "html", null, true);
                        echo "\" class=\"item\">";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "name", []), "html", null, true);
                        echo "</a>
\t\t\t\t";
                    }
                    // line 28
                    echo "\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 29
                echo "\t\t\t</div>
\t\t</div>
\t\t";
            } elseif (($this->getAttribute(            // line 31
($context["child"] ?? null), "type", []) == "divider")) {
                // line 32
                echo "\t\t<div class=\"divider\"></div>
\t\t";
            } else {
                // line 34
                echo "\t\t<a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["link"], "href", []), "html", null, true);
                echo "\" class=\"item firstLine\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["link"], "name", []), "html", null, true);
                echo "</a>
\t\t";
            }
            // line 36
            echo "\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['link'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 37
        echo "\t</div>
</div>

<div class=\"ui borderless sidemenu menu \" style=\"display: none\">
\t<div class=\"ui container\">
\t\t<div class=\"header item\">
\t\t\t<i class=\"icon ";
        // line 43
        echo twig_escape_filter($this->env, ($context["module_icon"] ?? null), "html", null, true);
        echo "\"></i>&nbsp;&nbsp;
\t\t\t";
        // line 44
        echo twig_escape_filter($this->env, ($context["module_name"] ?? null), "html", null, true);
        echo "
\t\t</div>
\t\t<div class=\"item menuToggler link\"><i class=\"fa fa-bars\"></i>&nbsp;Открыть меню</div>
\t</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "menu.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  150 => 44,  146 => 43,  138 => 37,  132 => 36,  124 => 34,  120 => 32,  118 => 31,  114 => 29,  108 => 28,  100 => 26,  96 => 24,  94 => 23,  90 => 21,  79 => 19,  75 => 18,  70 => 16,  66 => 14,  63 => 13,  59 => 12,  54 => 10,  51 => 9,  48 => 8,  44 => 7,  39 => 5,  35 => 4,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"ui borderless main menu \">
\t<div class=\"ui container\">
\t\t<div class=\"header item firstLine\">
\t\t\t<i class=\"icon {{module_icon}}\"></i>&nbsp;&nbsp;
\t\t\t{{ module_name }}
\t\t</div>
\t\t{% for link in links %}
\t\t{% if link.type == 'dropdown' %}
\t\t<div class=\"ui dropdown item firstLine\" tabindex=\"0\">
\t\t\t{{link.name}} <i class=\"dropdown icon\"></i>
\t\t\t<div class=\"menu\" tabindex=\"0\">
\t\t\t\t{% for child in link.children %}
\t\t\t\t{% if child.type == 'dropdown' %}
\t\t\t\t<div class=\"item\">
\t\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t\t{{ child.name }}
\t\t\t\t\t<div class=\"menu\">
\t\t\t\t\t\t{% for subchild in child.children %}
\t\t\t\t\t\t<a href=\"{{ subchild.href }}\" class=\"item\">{{ subchild.name }}</a>
\t\t\t\t\t\t{% endfor %}
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t{% elseif child.type == 'divider' %}
\t\t\t\t<div class=\"divider\"></div>
\t\t\t\t{% else %}
\t\t\t\t<a href=\"{{ child.href }}\" class=\"item\">{{ child.name }}</a>
\t\t\t\t{% endif %}
\t\t\t\t{% endfor %}
\t\t\t</div>
\t\t</div>
\t\t{% elseif child.type == 'divider' %}
\t\t<div class=\"divider\"></div>
\t\t{% else %}
\t\t<a href=\"{{ link.href }}\" class=\"item firstLine\">{{ link.name }}</a>
\t\t{% endif %}
\t\t{% endfor %}
\t</div>
</div>

<div class=\"ui borderless sidemenu menu \" style=\"display: none\">
\t<div class=\"ui container\">
\t\t<div class=\"header item\">
\t\t\t<i class=\"icon {{module_icon}}\"></i>&nbsp;&nbsp;
\t\t\t{{ module_name }}
\t\t</div>
\t\t<div class=\"item menuToggler link\"><i class=\"fa fa-bars\"></i>&nbsp;Открыть меню</div>
\t</div>
</div>
", "menu.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\menu.html");
    }
}
