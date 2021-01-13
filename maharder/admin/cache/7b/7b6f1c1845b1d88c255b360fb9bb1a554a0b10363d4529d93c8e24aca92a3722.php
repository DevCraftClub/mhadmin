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

/* footer.html */
class __TwigTemplate_e28a5f59a8c4850c2da5c37012cd9a799f0a417025705dd7d9f55cb55411e8d7 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
            'modullinks' => [$this, 'block_modullinks'],
            'extralinks' => [$this, 'block_extralinks'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<div class=\"ui inverted vertical footer segment\">
\t<div class=\"ui center aligned container\">
\t\t<div class=\"ui stackable inverted divided grid\">
\t\t\t<div class=\"five wide column\">
\t\t\t\t<h4 class=\"ui inverted header\">Модуль</h4>
\t\t\t\t<div class=\"ui inverted link list\">
\t\t\t\t\t";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["links"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
            // line 8
            echo "\t\t\t\t\t";
            if (($this->getAttribute($context["l"], "type", []) == "link")) {
                echo "<a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["l"], "href", []), "html", null, true);
                echo "\" class=\"item\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["l"], "name", []), "html", null, true);
                echo "</a>";
            }
            // line 9
            echo "\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 10
        echo "\t\t\t\t\t";
        $this->displayBlock('modullinks', $context, $blocks);
        // line 11
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"five wide column\">
\t\t\t\t<h4 class=\"ui inverted header\">Доп. ссылки</h4>
\t\t\t\t<div class=\"ui inverted link list\">
\t\t\t\t\t<a href=\"";
        // line 16
        echo twig_escape_filter($this->env, ($context["site_link"] ?? null), "html", null, true);
        echo "\" class=\"item\">На сайте автора</a>
\t\t\t\t\t<a href=\"";
        // line 17
        echo twig_escape_filter($this->env, ($context["docs_link"] ?? null), "html", null, true);
        echo "\" class=\"item\">Документация</a>
\t\t\t\t\t";
        // line 18
        $this->displayBlock('extralinks', $context, $blocks);
        // line 19
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"six wide column\">
\t\t\t\t<h4 class=\"ui inverted header\">Об авторе</h4>
\t\t\t\t<div class=\"ui inverted list\">
\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t<div class=\"header\">";
        // line 25
        echo twig_escape_filter($this->env, $this->getAttribute(($context["author"] ?? null), "name", []), "html", null, true);
        echo "</div>
\t\t\t\t\t\t<div class=\"ui horizontal inverted divided list\">
\t\t\t\t\t\t\t";
        // line 27
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["author"] ?? null), "contacts", []));
        foreach ($context['_seq'] as $context["_key"] => $context["c"]) {
            // line 28
            echo "\t\t\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t\t\t<div class=\"content\">
\t\t\t\t\t\t\t\t\t<a href=\"";
            // line 30
            echo twig_escape_filter($this->env, $this->getAttribute($context["c"], "link", []), "html", null, true);
            echo "\" target=\"_blank\" title=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["c"], "name", []), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["c"], "name", []), "html", null, true);
            echo "</a>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['c'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 34
        echo "\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t<div class=\"header\">Финансовая поддержка (добровольная)</div>
\t\t\t\t\t\t<div class=\"ui horizontal inverted divided list\">
\t\t\t\t\t\t\t";
        // line 39
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["author"] ?? null), "donate", []));
        foreach ($context['_seq'] as $context["_key"] => $context["d"]) {
            // line 40
            echo "\t\t\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t\t\t<div class=\"content\">
\t\t\t\t\t\t\t\t\t";
            // line 42
            if (($this->getAttribute($context["d"], "link", []) != "")) {
                echo "<a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["d"], "link", []), "html", null, true);
                echo "\" rel='' target=\"_blank\"
\t\t\t\t\t\t\t\t\t\ttitle=\"";
                // line 43
                echo twig_escape_filter($this->env, $this->getAttribute($context["d"], "name", []), "html", null, true);
                echo "\">";
            } else {
                echo "<span title=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["d"], "name", []), "html", null, true);
                echo "\">";
            }
            echo twig_escape_filter($this->env, $this->getAttribute($context["d"], "value", []), "html", null, true);
            // line 44
            if (($this->getAttribute($context["d"], "link", []) != "")) {
                echo "</a>";
            } else {
                echo "</span>";
            }
            // line 45
            echo "\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['d'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 48
        echo "\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
";
    }

    // line 10
    public function block_modullinks($context, array $blocks = [])
    {
        echo " ";
    }

    // line 18
    public function block_extralinks($context, array $blocks = [])
    {
    }

    public function getTemplateName()
    {
        return "footer.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  175 => 18,  169 => 10,  158 => 48,  150 => 45,  144 => 44,  135 => 43,  129 => 42,  125 => 40,  121 => 39,  114 => 34,  100 => 30,  96 => 28,  92 => 27,  87 => 25,  79 => 19,  77 => 18,  73 => 17,  69 => 16,  62 => 11,  59 => 10,  53 => 9,  44 => 8,  40 => 7,  32 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"ui inverted vertical footer segment\">
\t<div class=\"ui center aligned container\">
\t\t<div class=\"ui stackable inverted divided grid\">
\t\t\t<div class=\"five wide column\">
\t\t\t\t<h4 class=\"ui inverted header\">Модуль</h4>
\t\t\t\t<div class=\"ui inverted link list\">
\t\t\t\t\t{% for l in links %}
\t\t\t\t\t{% if l.type =='link' %}<a href=\"{{l.href}}\" class=\"item\">{{l.name}}</a>{% endif %}
\t\t\t\t\t{% endfor %}
\t\t\t\t\t{% block modullinks %} {% endblock %}
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"five wide column\">
\t\t\t\t<h4 class=\"ui inverted header\">Доп. ссылки</h4>
\t\t\t\t<div class=\"ui inverted link list\">
\t\t\t\t\t<a href=\"{{site_link}}\" class=\"item\">На сайте автора</a>
\t\t\t\t\t<a href=\"{{docs_link}}\" class=\"item\">Документация</a>
\t\t\t\t\t{% block extralinks %}{% endblock %}
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"six wide column\">
\t\t\t\t<h4 class=\"ui inverted header\">Об авторе</h4>
\t\t\t\t<div class=\"ui inverted list\">
\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t<div class=\"header\">{{ author.name }}</div>
\t\t\t\t\t\t<div class=\"ui horizontal inverted divided list\">
\t\t\t\t\t\t\t{% for c in author.contacts %}
\t\t\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t\t\t<div class=\"content\">
\t\t\t\t\t\t\t\t\t<a href=\"{{c.link}}\" target=\"_blank\" title=\"{{c.name}}\">{{c.name}}</a>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t{% endfor %}
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t<div class=\"header\">Финансовая поддержка (добровольная)</div>
\t\t\t\t\t\t<div class=\"ui horizontal inverted divided list\">
\t\t\t\t\t\t\t{% for d in author.donate %}
\t\t\t\t\t\t\t<div class=\"item\">
\t\t\t\t\t\t\t\t<div class=\"content\">
\t\t\t\t\t\t\t\t\t{% if d.link != '' %}<a href=\"{{d.link}}\" rel='' target=\"_blank\"
\t\t\t\t\t\t\t\t\t\ttitle=\"{{d.name}}\">{% else %}<span title=\"{{d.name}}\">{% endif %}{{d.value}}{%
\t\t\t\t\t\t\t\t\t\t\tif d.link != '' %}</a>{% else %}</span>{% endif %}
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t{% endfor %}
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
", "footer.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\footer.html");
    }
}
