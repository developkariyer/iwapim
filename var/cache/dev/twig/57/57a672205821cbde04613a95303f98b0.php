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

/* @PimcoreCore/Profiler/logo.svg.twig */
class __TwigTemplate_e63bd9e68560ba3c07eee9a2077bd111 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreCore/Profiler/logo.svg.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreCore/Profiler/logo.svg.twig"));

        // line 1
        echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 45 26.25\">
    <path d=\"M38.13,16.63a9.37,9.37,0,0,0-7.82,4.21L23.44,31.18a9.38,9.38,0,1,1,0-10.42l1.23,1.86,2.25-3.4-.33-.5a13.12,13.12,0,1,0,0,14.53l2.55-3.85,1.18,1.78a9.38,9.38,0,1,0,7.82-14.55Zm0,15a5.62,5.62,0,0,1-4.7-2.54l-2-3.09,2.06-3.11a5.62,5.62,0,1,1,4.69,8.73Z\" transform=\"translate(-2.5 -12.88)\" style=\"fill:#fff\"/>
</svg>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreCore/Profiler/logo.svg.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<?xml version=\"1.0\" encoding=\"utf-8\"?>
<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 45 26.25\">
    <path d=\"M38.13,16.63a9.37,9.37,0,0,0-7.82,4.21L23.44,31.18a9.38,9.38,0,1,1,0-10.42l1.23,1.86,2.25-3.4-.33-.5a13.12,13.12,0,1,0,0,14.53l2.55-3.85,1.18,1.78a9.38,9.38,0,1,0,7.82-14.55Zm0,15a5.62,5.62,0,0,1-4.7-2.54l-2-3.09,2.06-3.11a5.62,5.62,0,1,1,4.69,8.73Z\" transform=\"translate(-2.5 -12.88)\" style=\"fill:#fff\"/>
</svg>
", "@PimcoreCore/Profiler/logo.svg.twig", "/var/www/iwapim/vendor/pimcore/pimcore/bundles/CoreBundle/templates/Profiler/logo.svg.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array();
        static $filters = array();
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                [],
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
