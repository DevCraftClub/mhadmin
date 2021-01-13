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

/* templateIncludes/addSelect.html */
class __TwigTemplate_122c490a7e18cb1ad0beb9d6ed01debdc9bfb121307927fdef1e3f0ba5fe7e32 extends \Twig\Template
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
        echo "<div class=\"field\">
\t<div class=\"ui search clearable selection fluid dropdown";
        // line 2
        if (($context["multiple"] ?? null)) {
            echo " multiple";
        }
        echo "\">
\t\t<input type=\"hidden\" id=\"";
        // line 3
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "\" name=\"";
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "\" value=\"";
        echo twig_escape_filter($this->env, ($context["selected"] ?? null), "html", null, true);
        echo "\">
\t\t<i class=\"dropdown icon\"></i>
\t\t<div class=\"default text\">";
        // line 5
        echo twig_escape_filter($this->env, ($context["label"] ?? null), "html", null, true);
        echo "</div>
\t\t<div class=\"menu\">
\t\t\t";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["values"] ?? null));
        foreach ($context['_seq'] as $context["n"] => $context["v"]) {
            // line 8
            echo "\t\t\t\t";
            if (twig_test_iterable($context["v"])) {
                // line 9
                echo "\t\t\t\t\t";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($context["v"]);
                foreach ($context['_seq'] as $context["i"] => $context["k"]) {
                    // line 10
                    echo "\t\t\t\t\t\t<div data-value=\"";
                    echo twig_escape_filter($this->env, $context["i"], "html", null, true);
                    echo "\" class=\"item";
                    if (twig_in_filter($context["i"], twig_split_filter($this->env, ($context["selected"] ?? null), ","))) {
                        echo " active selected";
                    }
                    echo "\">";
                    echo twig_escape_filter($this->env, $context["k"], "html", null, true);
                    echo "</div>
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['i'], $context['k'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 12
                echo "\t\t\t\t";
            } else {
                // line 13
                echo "\t\t\t\t\t<div data-value=\"";
                echo twig_escape_filter($this->env, $context["n"], "html", null, true);
                echo "\" class=\"item";
                if (twig_in_filter($context["n"], twig_split_filter($this->env, ($context["selected"] ?? null), ","))) {
                    echo " active selected";
                }
                echo "\">";
                echo twig_escape_filter($this->env, $context["v"], "html", null, true);
                echo "</div>
\t\t\t\t";
            }
            // line 15
            echo "\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['n'], $context['v'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 16
        echo "\t\t</div>
\t</div>
\t";
        // line 18
        if (($context["multiple"] ?? null)) {
            // line 19
            echo "\t\t<script>
\t\t\tsetTimeout(()=> {
\t\t\t\t\$('#";
            // line 21
            echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
            echo "').parent().dropdown({
\t\t\t\t\tvalues: [
\t\t\t\t\t\t";
            // line 23
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["values"] ?? null));
            foreach ($context['_seq'] as $context["n"] => $context["v"]) {
                // line 24
                echo "\t\t\t\t\t\t\t";
                if (twig_test_iterable($context["v"])) {
                    // line 25
                    echo "\t\t\t\t\t\t\t\t";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($context["v"]);
                    foreach ($context['_seq'] as $context["i"] => $context["k"]) {
                        // line 26
                        echo "\t\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\t\tname: '";
                        // line 27
                        echo twig_escape_filter($this->env, $context["k"], "html", null, true);
                        echo "',
\t\t\t\t\t\t\t\t\t\tvalue: '";
                        // line 28
                        echo twig_escape_filter($this->env, $context["i"], "html", null, true);
                        echo "',
\t\t\t\t\t\t\t\t\t\t";
                        // line 29
                        if (twig_in_filter($context["i"], twig_split_filter($this->env, ($context["selected"] ?? null), ","))) {
                            echo " selected : true";
                        }
                        // line 30
                        echo "\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['i'], $context['k'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 32
                    echo "\t\t\t\t\t\t\t";
                } else {
                    // line 33
                    echo "\t\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\t\tname: '";
                    // line 34
                    echo twig_escape_filter($this->env, $context["v"], "html", null, true);
                    echo "',
\t\t\t\t\t\t\t\t\t\tvalue: '";
                    // line 35
                    echo twig_escape_filter($this->env, $context["n"], "html", null, true);
                    echo "',
\t\t\t\t\t\t\t\t\t\t";
                    // line 36
                    if (twig_in_filter($context["n"], twig_split_filter($this->env, ($context["selected"] ?? null), ","))) {
                        echo " selected : true";
                    }
                    // line 37
                    echo "\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t";
                }
                // line 39
                echo "\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['n'], $context['v'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 40
            echo "\t\t\t\t\t]
\t\t\t\t});
\t\t}, 100);
\t\t</script>
\t";
        }
        // line 45
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "templateIncludes/addSelect.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  185 => 45,  178 => 40,  172 => 39,  168 => 37,  164 => 36,  160 => 35,  156 => 34,  153 => 33,  150 => 32,  143 => 30,  139 => 29,  135 => 28,  131 => 27,  128 => 26,  123 => 25,  120 => 24,  116 => 23,  111 => 21,  107 => 19,  105 => 18,  101 => 16,  95 => 15,  83 => 13,  80 => 12,  65 => 10,  60 => 9,  57 => 8,  53 => 7,  48 => 5,  39 => 3,  33 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"field\">
\t<div class=\"ui search clearable selection fluid dropdown{% if multiple %} multiple{% endif %}\">
\t\t<input type=\"hidden\" id=\"{{name}}\" name=\"{{name}}\" value=\"{{selected}}\">
\t\t<i class=\"dropdown icon\"></i>
\t\t<div class=\"default text\">{{label}}</div>
\t\t<div class=\"menu\">
\t\t\t{% for n,v in values %}
\t\t\t\t{% if v is iterable %}
\t\t\t\t\t{% for i,k in v %}
\t\t\t\t\t\t<div data-value=\"{{i}}\" class=\"item{% if i in selected|split(',') %} active selected{% endif %}\">{{k}}</div>
\t\t\t\t\t{% endfor %}
\t\t\t\t{% else %}
\t\t\t\t\t<div data-value=\"{{n}}\" class=\"item{% if n in selected|split(',') %} active selected{% endif %}\">{{v}}</div>
\t\t\t\t{% endif %}
\t\t\t{% endfor %}
\t\t</div>
\t</div>
\t{% if multiple %}
\t\t<script>
\t\t\tsetTimeout(()=> {
\t\t\t\t\$('#{{name}}').parent().dropdown({
\t\t\t\t\tvalues: [
\t\t\t\t\t\t{% for n,v in values %}
\t\t\t\t\t\t\t{% if v is iterable %}
\t\t\t\t\t\t\t\t{% for i,k in v %}
\t\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\t\tname: '{{k}}',
\t\t\t\t\t\t\t\t\t\tvalue: '{{i}}',
\t\t\t\t\t\t\t\t\t\t{% if i in selected|split(',') %} selected : true{% endif %}
\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{% endfor %}
\t\t\t\t\t\t\t{% else %}
\t\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\t\tname: '{{v}}',
\t\t\t\t\t\t\t\t\t\tvalue: '{{n}}',
\t\t\t\t\t\t\t\t\t\t{% if n in selected|split(',') %} selected : true{% endif %}
\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t{% endif %}
\t\t\t\t\t\t{% endfor %}
\t\t\t\t\t]
\t\t\t\t});
\t\t}, 100);
\t\t</script>
\t{% endif %}
</div>
", "templateIncludes/addSelect.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\templateIncludes\\addSelect.html");
    }
}
