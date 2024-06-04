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

/* @PimcoreAdmin/searchadmin/search/quicksearch/asset.html.twig */
class __TwigTemplate_42df2dc645a000c6ebe4e0a54f05f7ca extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/searchadmin/search/quicksearch/asset.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/searchadmin/search/quicksearch/asset.html.twig"));

        // line 2
        $context["previewImage"] = null;
        // line 3
        $context["params"] = ["id" => twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 3, $this->source); })()), "id", [], "any", false, false, true, 3), "treepreview" => true];
        // line 4
        echo "
";
        // line 5
        if ($this->env->getTest('instanceof')->getCallable()((isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 5, $this->source); })()), "\\Pimcore\\Model\\Asset\\Image")) {
            // line 6
            echo "    ";
            $context["previewImage"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_asset_getimagethumbnail", $this->sandbox->ensureToStringAllowed((isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 6, $this->source); })()), 6, $this->source));
        } elseif (($this->env->getTest('instanceof')->getCallable()(        // line 7
(isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 7, $this->source); })()), "\\Pimcore\\Model\\Asset\\Video") && Pimcore\Video::isAvailable())) {
            // line 8
            echo "    ";
            $context["previewImage"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_asset_getvideothumbnail", $this->sandbox->ensureToStringAllowed((isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 8, $this->source); })()), 8, $this->source));
        } elseif (($this->env->getTest('instanceof')->getCallable()(        // line 9
(isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 9, $this->source); })()), "\\Pimcore\\Model\\Asset\\Document") && Pimcore\Document::isAvailable())) {
            // line 10
            echo "    ";
            $context["previewImage"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_asset_getdocumentthumbnail", $this->sandbox->ensureToStringAllowed((isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 10, $this->source); })()), 10, $this->source));
        }
        // line 12
        echo "
";
        // line 13
        if (array_key_exists("previewImage", $context)) {
            // line 14
            echo "    <div class=\"full-preview\">
        <img src=\"";
            // line 15
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["previewImage"]) || array_key_exists("previewImage", $context) ? $context["previewImage"] : (function () { throw new RuntimeError('Variable "previewImage" does not exist.', 15, $this->source); })()), 15, $this->source), "html", null, true);
            echo "\" onload=\"this.parentNode.className += ' complete';\">
        ";
            // line 16
            $this->loadTemplate("@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig", "@PimcoreAdmin/searchadmin/search/quicksearch/asset.html.twig", 16)->display(twig_array_merge($context, ["element" => (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 16, $this->source); })())]));
            // line 17
            echo "    </div>
";
        } else {
            // line 19
            echo "    <div class=\"mega-icon ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["iconCls"]) || array_key_exists("iconCls", $context) ? $context["iconCls"] : (function () { throw new RuntimeError('Variable "iconCls" does not exist.', 19, $this->source); })()), 19, $this->source), "html", null, true);
            echo "\"></div>
    ";
            // line 20
            $this->loadTemplate("@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig", "@PimcoreAdmin/searchadmin/search/quicksearch/asset.html.twig", 20)->display(twig_array_merge($context, ["element" => (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 20, $this->source); })())]));
        }
        // line 22
        echo "
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreAdmin/searchadmin/search/quicksearch/asset.html.twig";
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
        return array (  94 => 22,  91 => 20,  86 => 19,  82 => 17,  80 => 16,  76 => 15,  73 => 14,  71 => 13,  68 => 12,  64 => 10,  62 => 9,  59 => 8,  57 => 7,  54 => 6,  52 => 5,  49 => 4,  47 => 3,  45 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("{# @var \$element \\Pimcore\\Model\\Asset\\Image|\\Pimcore\\Model\\Asset\\Document|\\Pimcore\\Model\\Asset\\Video #}
{% set previewImage = null %}
{% set params = { 'id': element.id, 'treepreview': true } %}

{% if element is instanceof('\\\\Pimcore\\\\Model\\\\Asset\\\\Image') %}
    {% set previewImage = path('pimcore_admin_asset_getimagethumbnail', params) %}
{% elseif element is instanceof('\\\\Pimcore\\\\Model\\\\Asset\\\\Video') and pimcore_video_is_available() %}
    {% set previewImage = path('pimcore_admin_asset_getvideothumbnail', params) %}
{% elseif element is instanceof('\\\\Pimcore\\\\Model\\\\Asset\\\\Document') and pimcore_document_is_available() %}
    {% set previewImage = path('pimcore_admin_asset_getdocumentthumbnail', params) %}
{% endif %}

{% if previewImage is defined %}
    <div class=\"full-preview\">
        <img src=\"{{ previewImage }}\" onload=\"this.parentNode.className += ' complete';\">
        {% include '@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig' with {'element': element} %}
    </div>
{% else %}
    <div class=\"mega-icon {{ iconCls }}\"></div>
    {% include '@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig' with {'element': element} %}
{% endif %}

", "@PimcoreAdmin/searchadmin/search/quicksearch/asset.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/searchadmin/search/quicksearch/asset.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 2, "if" => 5, "include" => 16);
        static $filters = array("escape" => 15);
        static $functions = array("path" => 6, "pimcore_video_is_available" => 7, "pimcore_document_is_available" => 9);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'include'],
                ['escape'],
                ['path', 'pimcore_video_is_available', 'pimcore_document_is_available']
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
