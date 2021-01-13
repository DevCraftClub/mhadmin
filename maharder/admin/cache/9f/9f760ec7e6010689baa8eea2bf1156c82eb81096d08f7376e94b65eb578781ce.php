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

/* templateIncludes/addInput.html */
class __TwigTemplate_3afa6f1b0ef58dd092bd967b8e42d29eac2eb790d716188870ddb68e1ee3296b extends \Twig\Template
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
\t<input type=\"";
        // line 2
        echo twig_escape_filter($this->env, ($context["type"] ?? null), "html", null, true);
        echo "\" id=\"";
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "\" name=\"";
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "\" placeholder=\"";
        echo twig_escape_filter($this->env, ($context["label"] ?? null), "html", null, true);
        echo "\" value=\"";
        echo twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
        echo "\" ";
        if ((($context["type"] ?? null) == "file")) {
            // line 3
            echo "class=\"icheck\" ";
        } else {
            if (($context["chosen"] ?? null)) {
                echo "class=\"chosen\" ";
            }
        }
        echo ">
\t";
        // line 4
        if ((($context["type"] ?? null) == "file")) {
            // line 5
            echo "\t<br>
\t<div class='ui checkbox'>
\t\t";
            // line 7
            echo twig_include($this->env, $context, "templateIncludes/addCheckbox.html", ["name" => ($context["name"] ?? null), "class" => "switch", "selected" => false]);
            echo "
\t\t<label for=\"";
            // line 8
            echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
            echo "_check\">";
            echo twig_escape_filter($this->env, ($context["label"] ?? null), "html", null, true);
            echo " заменить?</label>
\t</div>
\t";
        }
        // line 11
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "templateIncludes/addInput.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 11,  64 => 8,  60 => 7,  56 => 5,  54 => 4,  45 => 3,  33 => 2,  30 => 1,);
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
\t<input type=\"{{type}}\" id=\"{{name}}\" name=\"{{name}}\" placeholder=\"{{label}}\" value=\"{{value}}\" {% if type=='file'
\t\t%}class=\"icheck\" {%else%}{%if chosen%}class=\"chosen\" {%endif%}{%endif%}>
\t{% if type == 'file' %}
\t<br>
\t<div class='ui checkbox'>
\t\t{{ include('templateIncludes/addCheckbox.html', {name: name, class: 'switch', selected: false}) }}
\t\t<label for=\"{{name}}_check\">{{label}} заменить?</label>
\t</div>
\t{%endif%}
</div>
", "templateIncludes/addInput.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\templateIncludes\\addInput.html");
    }
}
