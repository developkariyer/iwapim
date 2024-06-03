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

/* @PimcoreAdmin/admin/document/document/diff_versions.html.twig */
class __TwigTemplate_f1b628e8017dcc7332c97f2f2d3d1746 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/document/document/diff_versions.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/document/document/diff_versions.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html>
<head lang=\"en\">
    <meta charset=\"UTF-8\">

    <style type=\"text/css\">
        html, body {
            padding: 0;
            margin: 0;
        }

        body {
            text-align: center;
            position: relative;
        }

        img {
            max-width: 100%;
        }

        #left, #right {
            position: absolute;
            top:0;
            width:50%;
        }

        #left {
            left: 0;
            z-index: 1;
        }

        #right {
            right: 0;
            z-index: 2;
            border-left: 1px dashed darkred;
        }
    </style>
</head>
<body>
    ";
        // line 40
        if (array_key_exists("image", $context)) {
            // line 41
            echo "        <img src=\"data:image/png;base64,";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["image"]) || array_key_exists("image", $context) ? $context["image"] : (function () { throw new RuntimeError('Variable "image" does not exist.', 41, $this->source); })()), 41, $this->source), "html", null, true);
            echo "\" />
    ";
        } else {
            // line 43
            echo "        <div id=\"left\">
            <img src=\"data:image/png;base64,";
            // line 44
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["image1"]) || array_key_exists("image1", $context) ? $context["image1"] : (function () { throw new RuntimeError('Variable "image1" does not exist.', 44, $this->source); })()), 44, $this->source), "html", null, true);
            echo "\" />
        </div>
        <div id=\"right\">
            <img src=\"data:image/png;base64,";
            // line 47
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["image2"]) || array_key_exists("image2", $context) ? $context["image2"] : (function () { throw new RuntimeError('Variable "image2" does not exist.', 47, $this->source); })()), 47, $this->source), "html", null, true);
            echo "\" />
        </div>
    ";
        }
        // line 50
        echo "</body>
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
        return "@PimcoreAdmin/admin/document/document/diff_versions.html.twig";
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
        return array (  109 => 50,  103 => 47,  97 => 44,  94 => 43,  88 => 41,  86 => 40,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html>
<head lang=\"en\">
    <meta charset=\"UTF-8\">

    <style type=\"text/css\">
        html, body {
            padding: 0;
            margin: 0;
        }

        body {
            text-align: center;
            position: relative;
        }

        img {
            max-width: 100%;
        }

        #left, #right {
            position: absolute;
            top:0;
            width:50%;
        }

        #left {
            left: 0;
            z-index: 1;
        }

        #right {
            right: 0;
            z-index: 2;
            border-left: 1px dashed darkred;
        }
    </style>
</head>
<body>
    {% if image is defined %}
        <img src=\"data:image/png;base64,{{ image }}\" />
    {% else %}
        <div id=\"left\">
            <img src=\"data:image/png;base64,{{ image1 }}\" />
        </div>
        <div id=\"right\">
            <img src=\"data:image/png;base64,{{ image2 }}\" />
        </div>
    {% endif %}
</body>
</html>
", "@PimcoreAdmin/admin/document/document/diff_versions.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/document/document/diff_versions.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 40);
        static $filters = array("escape" => 41);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape'],
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
