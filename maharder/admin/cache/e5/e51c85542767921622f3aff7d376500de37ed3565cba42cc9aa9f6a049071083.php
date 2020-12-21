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
        echo "<div class=\"ui left sidebar vertical menu inverted adminSidebar\">
\t<div class=\"ui accordion fluid inverted\">
\t\t";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["links"] ?? null));
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
        foreach ($context['_seq'] as $context["_key"] => $context["link"]) {
            // line 4
            echo "\t\t";
            if (($this->getAttribute($context["link"], "type", []) == "dropdown")) {
                // line 5
                echo "\t\t<div class=\"item\" tabindex=\"0\">
\t\t\t<a class=\"";
                // line 6
                if ($this->getAttribute($context["loop"], "first", [])) {
                    echo "active ";
                }
                echo "title\"><i class=\"dropdown icon\"></i>&nbsp;";
                echo twig_escape_filter($this->env, $this->getAttribute($context["link"], "name", []), "html", null, true);
                echo "</a>
\t\t\t<div class=\"";
                // line 7
                if ($this->getAttribute($context["loop"], "first", [])) {
                    echo "active ";
                }
                echo "content\">
\t\t\t\t";
                // line 8
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["link"], "children", []));
                foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                    // line 9
                    echo "\t\t\t\t";
                    if (($this->getAttribute($context["child"], "type", []) == "dropdown")) {
                        // line 10
                        echo "\t\t\t\t<div class=\"accordion\">
\t\t\t\t\t<a class=\"title\"><i class=\"dropdown icon\"></i>&nbsp;";
                        // line 11
                        echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "name", []), "html", null, true);
                        echo "</a>
\t\t\t\t\t<div class=\"content\">
\t\t\t\t\t\t";
                        // line 13
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["child"], "children", []));
                        foreach ($context['_seq'] as $context["_key"] => $context["subchild"]) {
                            // line 14
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
                        // line 16
                        echo "\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t";
                    } elseif (($this->getAttribute(                    // line 18
$context["child"], "type", []) == "divider")) {
                        // line 19
                        echo "\t\t\t\t<div class=\"divider\"></div>
\t\t\t\t";
                    } else {
                        // line 21
                        echo "\t\t\t\t<a href=\"";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "href", []), "html", null, true);
                        echo "\" class=\"item\">";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["child"], "name", []), "html", null, true);
                        echo "</a>
\t\t\t\t";
                    }
                    // line 23
                    echo "\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 24
                echo "\t\t\t</div>
\t\t</div>
\t\t";
            } elseif (($this->getAttribute(            // line 26
($context["child"] ?? null), "type", []) == "divider")) {
                // line 27
                echo "\t\t<div class=\"divider\"></div>
\t\t";
            } else {
                // line 29
                echo "\t\t<a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["link"], "href", []), "html", null, true);
                echo "\" class=\"item\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["link"], "name", []), "html", null, true);
                echo "</a>
\t\t";
            }
            // line 31
            echo "\t\t";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['link'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 32
        echo "\t</div>
\t<div class=\"item menuToggler link\"><i class=\"icon close\"></i>&nbsp;Закрыть меню</div>
</div>
";
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
        return array (  157 => 32,  143 => 31,  135 => 29,  131 => 27,  129 => 26,  125 => 24,  119 => 23,  111 => 21,  107 => 19,  105 => 18,  101 => 16,  90 => 14,  86 => 13,  81 => 11,  78 => 10,  75 => 9,  71 => 8,  65 => 7,  57 => 6,  54 => 5,  51 => 4,  34 => 3,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"ui left sidebar vertical menu inverted adminSidebar\">
\t<div class=\"ui accordion fluid inverted\">
\t\t{% for link in links %}
\t\t{% if link.type == 'dropdown' %}
\t\t<div class=\"item\" tabindex=\"0\">
\t\t\t<a class=\"{% if loop.first %}active {%endif%}title\"><i class=\"dropdown icon\"></i>&nbsp;{{link.name}}</a>
\t\t\t<div class=\"{% if loop.first %}active {%endif%}content\">
\t\t\t\t{% for child in link.children %}
\t\t\t\t{% if child.type == 'dropdown' %}
\t\t\t\t<div class=\"accordion\">
\t\t\t\t\t<a class=\"title\"><i class=\"dropdown icon\"></i>&nbsp;{{child.name}}</a>
\t\t\t\t\t<div class=\"content\">
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
\t\t<a href=\"{{ link.href }}\" class=\"item\">{{ link.name }}</a>
\t\t{% endif %}
\t\t{% endfor %}
\t</div>
\t<div class=\"item menuToggler link\"><i class=\"icon close\"></i>&nbsp;Закрыть меню</div>
</div>
", "sidebar.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\sidebar.html");
    }
}
