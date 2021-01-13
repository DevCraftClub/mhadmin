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

/* templateIncludes/addCheckbox.html */
class __TwigTemplate_3db56f7f1a308c6066a46216378d38fc9f6ed19ec52f20d6b2a5b37b334afbe1 extends \Twig\Template
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
        echo "<div class=\"ui left floated compact segment\">
\t<div class=\"ui fitted toggle checkbox\">
\t\t<input id=\"";
        // line 3
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "\" name=\"";
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "\" type=\"checkbox\" ";
        if ((($context["selected"] ?? null) == 1)) {
            echo "checked";
        }
        echo ">
\t\t<label></label>
\t</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "templateIncludes/addCheckbox.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 3,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"ui left floated compact segment\">
\t<div class=\"ui fitted toggle checkbox\">
\t\t<input id=\"{{name}}\" name=\"{{name}}\" type=\"checkbox\" {% if selected==1 %}checked{%endif%}>
\t\t<label></label>
\t</div>
</div>
", "templateIncludes/addCheckbox.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\templateIncludes\\addCheckbox.html");
    }
}
