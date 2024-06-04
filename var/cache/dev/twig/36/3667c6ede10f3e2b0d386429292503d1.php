<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* web2print/layout.html.twig */
class __TwigTemplate_4453a119819f4e2a5967e56c3ea3b13b extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "web2print/layout.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "web2print/layout.html.twig"));

        // line 1
        echo "
<!DOCTYPE html>
<html lang=\"\">
<head>

    <style type=\"text/css\">

        @page {
            counter-increment: page;

            -ro-scale-content: none;
            hyphens: auto;

            size: A4 portrait;
            margin:15mm 14mm 14mm 14mm;

        }

    </style>

    <style type=\"text/css\" media=\"screen\">
        body {
            background:#CCC;
        }

        .site {
            margin:0 auto;
            width: 21cm;
            padding:1cm 0 1cm 0;
        }

        .page {
            width: 21cm;
            padding:0.5cm 0 0.5cm 0;
            background:#FFF;
            -webkit-box-shadow: 0 0 4px 4px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 0 4px 4px rgba(0, 0, 0, 0.2);
            box-shadow: 0 0 4px 4px rgba(0, 0, 0, 0.2);

            position: relative;
        }
    </style>


    ";
        // line 45
        if ((array_key_exists("printermarks", $context) && ((isset($context["printermarks"]) || array_key_exists("printermarks", $context) ? $context["printermarks"] : (function () { throw new RuntimeError('Variable "printermarks" does not exist.', 45, $this->source); })()) == true))) {
            // line 46
            echo "        <link rel=\"stylesheet\" type=\"text/css\" href=\"/bundles/pimcoreadmin/css/print/print-printermarks.css\" media=\"print\" />
    ";
        }
        // line 48
        echo "
</head>

<body>
<div class=\"site\">
    ";
        // line 53
        $this->displayBlock("content", $context, $blocks);
        echo "
</div>

</body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "web2print/layout.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  104 => 53,  97 => 48,  93 => 46,  91 => 45,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("
<!DOCTYPE html>
<html lang=\"\">
<head>

    <style type=\"text/css\">

        @page {
            counter-increment: page;

            -ro-scale-content: none;
            hyphens: auto;

            size: A4 portrait;
            margin:15mm 14mm 14mm 14mm;

        }

    </style>

    <style type=\"text/css\" media=\"screen\">
        body {
            background:#CCC;
        }

        .site {
            margin:0 auto;
            width: 21cm;
            padding:1cm 0 1cm 0;
        }

        .page {
            width: 21cm;
            padding:0.5cm 0 0.5cm 0;
            background:#FFF;
            -webkit-box-shadow: 0 0 4px 4px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 0 4px 4px rgba(0, 0, 0, 0.2);
            box-shadow: 0 0 4px 4px rgba(0, 0, 0, 0.2);

            position: relative;
        }
    </style>


    {% if printermarks is defined and printermarks == true %}
        <link rel=\"stylesheet\" type=\"text/css\" href=\"/bundles/pimcoreadmin/css/print/print-printermarks.css\" media=\"print\" />
    {% endif %}

</head>

<body>
<div class=\"site\">
    {{ block('content') }}
</div>

</body>
</html>
", "web2print/layout.html.twig", "/var/www/iwapim/templates/web2print/layout.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 45);
        static $filters = array();
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                [],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
