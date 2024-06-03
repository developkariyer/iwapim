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

/* @WebProfiler/Profiler/header.html.twig */
class __TwigTemplate_6558475a7b34dc885d6734b62014e128 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/header.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/header.html.twig"));

        // line 1
        echo "<div id=\"header\">
    <h1><a href=\"";
        // line 2
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler_home");
        echo "\">";
        echo twig_source($this->env, "@WebProfiler/Icon/symfony.svg");
        echo " Symfony Profiler</a></h1>

    <div class=\"search\">
        <form method=\"get\" action=\"https://symfony.com/search\" target=\"_blank\">
            <div class=\"form-row\">
                <input name=\"q\" id=\"search-id\" type=\"search\" placeholder=\"search on symfony.com\" aria-label=\"Search on symfony.com\">
                <button type=\"submit\" class=\"visually-hidden\">Search</button>
            </div>
       </form>
    </div>
</div>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@WebProfiler/Profiler/header.html.twig";
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
        return array (  48 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<div id=\"header\">
    <h1><a href=\"{{ path('_profiler_home') }}\">{{ source('@WebProfiler/Icon/symfony.svg') }} Symfony Profiler</a></h1>

    <div class=\"search\">
        <form method=\"get\" action=\"https://symfony.com/search\" target=\"_blank\">
            <div class=\"form-row\">
                <input name=\"q\" id=\"search-id\" type=\"search\" placeholder=\"search on symfony.com\" aria-label=\"Search on symfony.com\">
                <button type=\"submit\" class=\"visually-hidden\">Search</button>
            </div>
       </form>
    </div>
</div>
", "@WebProfiler/Profiler/header.html.twig", "/var/www/iwapim/vendor/symfony/web-profiler-bundle/Resources/views/Profiler/header.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array();
        static $filters = array();
        static $functions = array("path" => 2, "source" => 2);

        try {
            $this->sandbox->checkSecurity(
                [],
                [],
                ['path', 'source']
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
