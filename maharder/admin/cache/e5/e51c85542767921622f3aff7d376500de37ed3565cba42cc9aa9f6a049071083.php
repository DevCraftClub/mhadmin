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

/* sidebar.html */
class __TwigTemplate_82e5ebe6e9e4b07c4ac2b6e67296c65aa0fe1ce4433b5ea4d465cde9b3fffaf6 extends \Twig\Template
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
        echo "<div class=\"ui left vertical menu sidebar\">
\t";
        // line 2
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["links"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["link"]) {
            // line 3
            echo "\t";
            if (($this->getAttribute($context["link"], "type", []) == "dropdown")) {
                // line 4
                echo "\t<div class=\"ui dropdown item top left pointing\" tabindex=\"0\">
\t\t";
                // line 5
                echo twig_escape_filter($this->env, $this->getAttribute($context["link"], "name", []), "html", null, true);
                echo " <i class=\"dropdown icon\"></i>
\t\t<div class=\"menu\" tabindex=\"0\">
\t\t\t";
                // line 7
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["link"], "children", []));
                foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                    // line 8
                    echo "\t\t\t";
                    if (($this->getAttribute($context["child"], "type", []) == "dropdown")) {
                        // line 9
                        echo "\t\t\t<div class=\"item\">
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t";
                        // line 11
                        echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "name", []), "html", null, true);
                        echo "
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t";
                        // line 13
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["child"], "children", []));
                        foreach ($context['_seq'] as $context["_key"] => $context["subchild"]) {
                            // line 14
                            echo "\t\t\t\t\t<a href=\"";
                            echo twig_escape_filter($this->env, $this->getAttribute($context["subchild"], "href", []), "html", null, true);
                            echo "\" class=\"item\">";
                            echo twig_escape_filter($this->env, $this->getAttribute($context["subchild"], "name", []), "html", null, true);
                            echo "</a>
\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subchild'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 16
                        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t\t";
                    } elseif (($this->getAttribute(                    // line 18
$context["child"], "type", []) == "divider")) {
                        // line 19
                        echo "\t\t\t<div class=\"divider\"></div>
\t\t\t";
                    } else {
                        // line 21
                        echo "\t\t\t<a href=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "href", []), "html", null, true);
                        echo "\" class=\"item\">";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "name", []), "html", null, true);
                        echo "</a>
\t\t\t";
                    }
                    // line 23
                    echo "\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 24
                echo "\t\t</div>
\t</div>
\t";
            } elseif (($this->getAttribute(            // line 26
($context["child"] ?? null), "type", []) == "divider")) {
                // line 27
                echo "\t<div class=\"divider\"></div>
\t";
            } else {
                // line 29
                echo "\t<a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["link"], "href", []), "html", null, true);
                echo "\" class=\"item\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["link"], "name", []), "html", null, true);
                echo "</a>
\t";
            }
            // line 31
            echo "\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['link'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 32
        echo "</div>";
    }

    public function getTemplateName()
    {
        return "sidebar.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  127 => 32,  121 => 31,  113 => 29,  109 => 27,  107 => 26,  103 => 24,  97 => 23,  89 => 21,  85 => 19,  83 => 18,  79 => 16,  68 => 14,  64 => 13,  59 => 11,  55 => 9,  52 => 8,  48 => 7,  43 => 5,  40 => 4,  37 => 3,  33 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"ui left vertical menu sidebar\">
\t{% for link in links %}
\t{% if link.type == 'dropdown' %}
\t<div class=\"ui dropdown item top left pointing\" tabindex=\"0\">
\t\t{{link.name}} <i class=\"dropdown icon\"></i>
\t\t<div class=\"menu\" tabindex=\"0\">
\t\t\t{% for child in link.children %}
\t\t\t{% if child.type == 'dropdown' %}
\t\t\t<div class=\"item\">
\t\t\t\t<i class=\"dropdown icon\"></i>
\t\t\t\t{{ child.name }}
\t\t\t\t<div class=\"menu\">
\t\t\t\t\t{% for subchild in child.children %}
\t\t\t\t\t<a href=\"{{ subchild.href }}\" class=\"item\">{{ subchild.name }}</a>
\t\t\t\t\t{% endfor %}
\t\t\t\t</div>
\t\t\t</div>
\t\t\t{% elseif child.type == 'divider' %}
\t\t\t<div class=\"divider\"></div>
\t\t\t{% else %}
\t\t\t<a href=\"{{ child.href }}\" class=\"item\">{{ child.name }}</a>
\t\t\t{% endif %}
\t\t\t{% endfor %}
\t\t</div>
\t</div>
\t{% elseif child.type == 'divider' %}
\t<div class=\"divider\"></div>
\t{% else %}
\t<a href=\"{{ link.href }}\" class=\"item\">{{ link.name }}</a>
\t{% endif %}
\t{% endfor %}
</div>", "sidebar.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\sidebar.html");
    }
}
