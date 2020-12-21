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

/* breadcrumb.html */
class __TwigTemplate_30bbc2be5ee925d22a5384c9efb7f2340ff894772c4605e85fe4f5ef278d4763 extends \Twig\Template
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
        echo "
<div class=\"ui huge breadcrumb\">
\t";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["breadcrumbs"] ?? null));
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
        foreach ($context['_seq'] as $context["_key"] => $context["b"]) {
            // line 4
            echo "\t\t<a class=\"section\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["b"], "url", []), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["b"], "name", []), "html", null, true);
            echo "</a>
\t\t";
            // line 5
            if ($this->getAttribute($context["loop"], "last", [])) {
            } else {
                // line 6
                echo "\t\t\t<i class=\"right chevron icon divider\"></i>
\t\t";
            }
            // line 8
            echo "\t";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['b'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 9
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "breadcrumb.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 9,  65 => 8,  61 => 6,  58 => 5,  51 => 4,  34 => 3,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("
<div class=\"ui huge breadcrumb\">
\t{% for b in breadcrumbs %}
\t\t<a class=\"section\" href=\"{{ b.url }}\">{{ b.name }}</a>
\t\t{% if loop.last %}{% else %}
\t\t\t<i class=\"right chevron icon divider\"></i>
\t\t{% endif %}
\t{% endfor %}
</div>
", "breadcrumb.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\breadcrumb.html");
    }
}
