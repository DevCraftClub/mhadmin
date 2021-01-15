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

/* templateIncludes/boxes.html */
class __TwigTemplate_63f21c1d65cf79f40dc6d1d75e6c1e7d5d568ec5d8826d1cb9e06d9a67f2cf08 extends \Twig\Template
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
        echo "<div class=\"ui top attached tabular menu\" id=\"box-navi\">

\t";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["boxes"] ?? null));
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
        foreach ($context['_seq'] as $context["id"] => $context["box"]) {
            // line 4
            echo "\t<a href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["box"], "link", []), "html", null, true);
            echo "\" class=\"item";
            if (($this->getAttribute($context["loop"], "index0", []) == 0)) {
                echo " active";
            }
            echo "\" ";
            if (($this->getAttribute($context["box"], "link", []) == "#")) {
                // line 5
                echo "\t\tdata-tab=\"";
                echo twig_escape_filter($this->env, $context["id"], "html", null, true);
                echo "\" ";
            }
            echo ">
\t\t<i class=\"";
            // line 6
            echo twig_escape_filter($this->env, $this->getAttribute($context["box"], "icon", []), "html", null, true);
            echo "\"></i>&nbsp;&nbsp;";
            echo twig_escape_filter($this->env, $this->getAttribute($context["box"], "name", []), "html", null, true);
            echo "
\t</a>
\t";
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
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['box'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 9
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "templateIncludes/boxes.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  86 => 9,  67 => 6,  60 => 5,  51 => 4,  34 => 3,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"ui top attached tabular menu\" id=\"box-navi\">

\t{% for id,box in boxes %}
\t<a href=\"{{box.link}}\" class=\"item{% if loop.index0 == 0 %} active{% endif %}\" {% if box.link=='#' %}
\t\tdata-tab=\"{{id}}\" {% endif %}>
\t\t<i class=\"{{box.icon}}\"></i>&nbsp;&nbsp;{{box.name}}
\t</a>
\t{% endfor %}

</div>
", "templateIncludes/boxes.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\templateIncludes\\boxes.html");
    }
}
