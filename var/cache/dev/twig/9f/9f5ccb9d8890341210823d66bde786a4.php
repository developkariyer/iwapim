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

/* @PimcoreAdmin/admin/asset/show_version_document.html.twig */
class __TwigTemplate_b02a9723cb52c5140ab86f2fb31d5c09 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/asset/show_version_document.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/asset/show_version_document.html.twig"));

        // line 1
        if ((twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 1, $this->source); })()), "getMimeType", [], "method", false, false, true, 1) == "application/pdf")) {
            // line 2
            echo "    ";
            $context["tempFile"] = twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 2, $this->source); })()), "getTemporaryFile", [], "method", false, false, true, 2);
            // line 3
            echo "    ";
            $context["dataUri"] = $this->extensions['Pimcore\Twig\Extension\HelpersExtension']->getAssetVersionPreview($this->sandbox->ensureToStringAllowed((isset($context["tempFile"]) || array_key_exists("tempFile", $context) ? $context["tempFile"] : (function () { throw new RuntimeError('Variable "tempFile" does not exist.', 3, $this->source); })()), 3, $this->source));
            // line 4
            echo "
    <div style=\"display: flex; width: 100%; height: 100%; flex-direction: column; overflow: hidden;\">
        <iframe src=\"";
            // line 6
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["dataUri"]) || array_key_exists("dataUri", $context) ? $context["dataUri"] : (function () { throw new RuntimeError('Variable "dataUri" does not exist.', 6, $this->source); })()), 6, $this->source), "html", null, true);
            echo "\" frameborder=\"0\" style=\"flex-grow: 1; border: none; margin: 0; padding: 0;\"></iframe>
    </div>
";
        } else {
            // line 9
            echo "    ";
            $this->loadTemplate("@PimcoreAdmin/admin/asset/show_version_unknown.html.twig", "@PimcoreAdmin/admin/asset/show_version_document.html.twig", 9)->display($context);
        }
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreAdmin/admin/asset/show_version_document.html.twig";
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
        return array (  63 => 9,  57 => 6,  53 => 4,  50 => 3,  47 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% if asset.getMimeType() == 'application/pdf' %}
    {% set tempFile = asset.getTemporaryFile() %}
    {% set dataUri = pimcore_asset_version_preview(tempFile) %}

    <div style=\"display: flex; width: 100%; height: 100%; flex-direction: column; overflow: hidden;\">
        <iframe src=\"{{ dataUri }}\" frameborder=\"0\" style=\"flex-grow: 1; border: none; margin: 0; padding: 0;\"></iframe>
    </div>
{% else %}
    {% include '@PimcoreAdmin/admin/asset/show_version_unknown.html.twig' %}
{% endif %}", "@PimcoreAdmin/admin/asset/show_version_document.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/asset/show_version_document.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 1, "set" => 2, "include" => 9);
        static $filters = array("escape" => 6);
        static $functions = array("pimcore_asset_version_preview" => 3);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'set', 'include'],
                ['escape'],
                ['pimcore_asset_version_preview']
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
