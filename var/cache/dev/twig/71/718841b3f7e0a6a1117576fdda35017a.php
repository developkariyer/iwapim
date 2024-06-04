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

/* @PimcoreSeo/misc/http_error_log_detail.html.twig */
class __TwigTemplate_07891045cb588310b796f31783f02ef8 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreSeo/misc/http_error_log_detail.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreSeo/misc/http_error_log_detail.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html>
<head>

<style type=\"text/css\">
    body {
        margin: 0;
        padding: 10px;
        font-family: Arial;
        font-size: 12px;
    }

    h2 {
        border-bottom: 1px solid #000;
    }

    .sub {
        font-style: italic;
        border:0;
    }

    table {
        border-left: 1px solid #000;
        border-top: 1px solid #000;
        border-collapse: collapse;
    }

    td, th {
        border-right: 1px solid #000;
        border-bottom: 1px solid #000;
        padding: 2px;
    }

    th {
        text-align: left;
    }
</style>


</head>

<body>

<h2>";
        // line 44
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["data"]) || array_key_exists("data", $context) ? $context["data"] : (function () { throw new RuntimeError('Variable "data" does not exist.', 44, $this->source); })()), "code", [], "array", false, false, true, 44), 44, $this->source), "html", null, true);
        echo " | ";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["data"]) || array_key_exists("data", $context) ? $context["data"] : (function () { throw new RuntimeError('Variable "data" does not exist.', 44, $this->source); })()), "uri", [], "array", false, false, true, 44), 44, $this->source), "html", null, true);
        echo "</h2>

";
        // line 46
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["data"]) || array_key_exists("data", $context) ? $context["data"] : (function () { throw new RuntimeError('Variable "data" does not exist.', 46, $this->source); })()));
        foreach ($context['_seq'] as $context["key"] => $context["value"]) {
            // line 47
            echo "    ";
            if (twig_in_filter($context["key"], ["parametersGet", "parametersPost", "serverVars", "cookies"])) {
                // line 48
                echo "        ";
                if ( !twig_test_empty($context["value"])) {
                    // line 49
                    echo "            <h2 class=\"sub\">";
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed($context["key"], 49, $this->source), [], "admin"), "html", null, true);
                    echo "</h2>

            <table>
                ";
                    // line 52
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($context["value"]);
                    foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                        // line 53
                        echo "                    <tr>
                        <th valign=\"top\">";
                        // line 54
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["key"], 54, $this->source), "html", null, true);
                        echo "</th>
                        <td valign=\"top\">";
                        // line 55
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["value"], 55, $this->source), "html", null, true);
                        echo "</td>
                    </tr>
                ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 58
                    echo "            </table>
        ";
                }
                // line 60
                echo "    ";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 62
        echo "

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
        return "@PimcoreSeo/misc/http_error_log_detail.html.twig";
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
        return array (  145 => 62,  138 => 60,  134 => 58,  125 => 55,  121 => 54,  118 => 53,  114 => 52,  107 => 49,  104 => 48,  101 => 47,  97 => 46,  90 => 44,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html>
<head>

<style type=\"text/css\">
    body {
        margin: 0;
        padding: 10px;
        font-family: Arial;
        font-size: 12px;
    }

    h2 {
        border-bottom: 1px solid #000;
    }

    .sub {
        font-style: italic;
        border:0;
    }

    table {
        border-left: 1px solid #000;
        border-top: 1px solid #000;
        border-collapse: collapse;
    }

    td, th {
        border-right: 1px solid #000;
        border-bottom: 1px solid #000;
        padding: 2px;
    }

    th {
        text-align: left;
    }
</style>


</head>

<body>

<h2>{{ data[\"code\"] }} | {{ data[\"uri\"] }}</h2>

{% for key,value in data %}
    {% if key in [\"parametersGet\", \"parametersPost\", \"serverVars\", \"cookies\"] %}
        {% if value is not empty %}
            <h2 class=\"sub\">{{ key|trans([], 'admin') }}</h2>

            <table>
                {% for key,value in value %}
                    <tr>
                        <th valign=\"top\">{{ key }}</th>
                        <td valign=\"top\">{{ value }}</td>
                    </tr>
                {% endfor %}
            </table>
        {% endif %}
    {% endif %}
{% endfor %}


</body>
</html>
", "@PimcoreSeo/misc/http_error_log_detail.html.twig", "/var/www/iwapim/vendor/pimcore/pimcore/bundles/SeoBundle/templates/misc/http_error_log_detail.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("for" => 46, "if" => 47);
        static $filters = array("escape" => 44, "trans" => 49);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['for', 'if'],
                ['escape', 'trans'],
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
