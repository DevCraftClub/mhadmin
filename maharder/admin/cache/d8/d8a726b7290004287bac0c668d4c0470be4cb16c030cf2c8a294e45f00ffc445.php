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
        echo "<div class=\"ui huge breadcrumb\">
\t<a class=\"section\" href=\"";
        // line 2
        echo twig_escape_filter($this->env, ($context["modulindex"] ?? null), "html", null, true);
        echo "\">Главная</a>
\t<i class=\"right chevron icon divider\"></i>
\t<a class=\"section\">Registration</a>
\t<i class=\"right chevron icon divider\"></i>
\t<div class=\"active section\">Personal Information</div>
</div>";
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
        return array (  33 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"ui huge breadcrumb\">
\t<a class=\"section\" href=\"{{modulindex}}\">Главная</a>
\t<i class=\"right chevron icon divider\"></i>
\t<a class=\"section\">Registration</a>
\t<i class=\"right chevron icon divider\"></i>
\t<div class=\"active section\">Personal Information</div>
</div>", "breadcrumb.html", "D:\\OSPanel\\domains\\mhadmin.local\\maharder\\admin\\templates\\breadcrumb.html");
    }
}
