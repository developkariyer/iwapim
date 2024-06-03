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

/* @PimcoreAdmin/admin/install/check.html.twig */
class __TwigTemplate_5ffb5b2f8e6df7258f4cdc778243755e extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/install/check.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/install/check.html.twig"));

        // line 1
        if ( !(isset($context["headless"]) || array_key_exists("headless", $context) ? $context["headless"] : (function () { throw new RuntimeError('Variable "headless" does not exist.', 1, $this->source); })())) {
            // line 2
            echo "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"robots\" content=\"noindex, nofollow\" />
</head>
<body>";
        }
        // line 10
        echo "
";
        // line 31
        echo "
";
        // line 32
        $macros["s"] = $this->macros["s"] = $this;
        // line 33
        echo "
    <style type=\"text/css\">
        body {
            font-family: Arial, Tahoma, Verdana;
            font-size: 12px;
        }

        h2 {
            font-size: 16px;
            margin: 0;
            padding: 0 0 5px 0;
        }

        table {
            border-collapse: collapse;
        }

        a {
            color: #0066cc;
        }

        .legend {
            display: inline-block;
        }

        div.legend {
            padding-left: 20px;
        }

        span.legend {
            line-height: 30px;
            position: relative;
            padding: 0 30px 0 40px;
        }

        .legend img {
            position: absolute;
            top: 0;
            left: 0;
            width:30px;
        }

        table img {
            width:20px;
        }
    </style>

    <table cellpadding=\"20\">
        <tr>
            <td valign=\"top\">
                <h2>PHP</h2>
                <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">

                    ";
        // line 86
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["checksPHP"]) || array_key_exists("checksPHP", $context) ? $context["checksPHP"] : (function () { throw new RuntimeError('Variable "checksPHP" does not exist.', 86, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["check"]) {
            // line 87
            echo "                        ";
            echo twig_call_macro($macros["s"], "macro_check", [$context["check"]], 87, $context, $this->getSourceContext());
            echo "
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['check'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 89
        echo "
                </table>
            </td>
            <td valign=\"top\">
                <h2>MySQL</h2>
                <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">

                    ";
        // line 96
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["checksMySQL"]) || array_key_exists("checksMySQL", $context) ? $context["checksMySQL"] : (function () { throw new RuntimeError('Variable "checksMySQL" does not exist.', 96, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["check"]) {
            // line 97
            echo "                        ";
            echo twig_call_macro($macros["s"], "macro_check", [$context["check"]], 97, $context, $this->getSourceContext());
            echo "
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['check'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 99
        echo "
                </table>
            </td>
            <td valign=\"top\">
                <h2>Filesystem</h2>
                <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">

                    ";
        // line 106
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["checksFS"]) || array_key_exists("checksFS", $context) ? $context["checksFS"] : (function () { throw new RuntimeError('Variable "checksFS" does not exist.', 106, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["check"]) {
            // line 107
            echo "                        ";
            echo twig_call_macro($macros["s"], "macro_check", [$context["check"]], 107, $context, $this->getSourceContext());
            echo "
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['check'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 109
        echo "
                </table>

                <br />
                <br />

                <h2>CLI Tools &amp; Applications</h2>
                <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">

                    ";
        // line 118
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["checksApps"]) || array_key_exists("checksApps", $context) ? $context["checksApps"] : (function () { throw new RuntimeError('Variable "checksApps" does not exist.', 118, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["check"]) {
            // line 119
            echo "                        ";
            echo twig_call_macro($macros["s"], "macro_check", [$context["check"]], 119, $context, $this->getSourceContext());
            echo "
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['check'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 121
        echo "
                </table>
            </td>
        </tr>
    </table>

    <div class=\"legend\">
        <p>
            <b>Explanation:</b>
        </p>
        <p>
            <span class=\"legend\"><img src=\"/bundles/pimcoreadmin/img/flat-color-icons/ok.svg\" />Everything ok</span>
            <span class=\"legend\"><img width=\"26\" height=\"26\" src=\"/bundles/pimcoreadmin/img/flat-color-icons/overlay-error.svg\" /> Recommended but not required</span>
            <span class=\"legend\"><img src=\"/bundles/pimcoreadmin/img/flat-color-icons/high_priority.svg\" /> Required</span>
        </p>
    </div>

";
        // line 138
        if ( !(isset($context["headless"]) || array_key_exists("headless", $context) ? $context["headless"] : (function () { throw new RuntimeError('Variable "headless" does not exist.', 138, $this->source); })())) {
            // line 139
            echo "</body>
</html>";
        }
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 11
    public function macro_check($__check__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "check" => $__check__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start();
        try {
            $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "check"));

            $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "check"));

            // line 12
            echo "    ";
            $context["icon"] = "high_priority";
            // line 13
            echo "    ";
            if ((twig_get_attribute($this->env, $this->source, (isset($context["check"]) || array_key_exists("check", $context) ? $context["check"] : (function () { throw new RuntimeError('Variable "check" does not exist.', 13, $this->source); })()), "state", [], "any", false, false, true, 13) == twig_constant("Pimcore\\Tool\\Requirements\\Check::STATE_OK"))) {
                // line 14
                echo "        ";
                $context["icon"] = "ok";
                // line 15
                echo "    ";
            } elseif ((twig_get_attribute($this->env, $this->source, (isset($context["check"]) || array_key_exists("check", $context) ? $context["check"] : (function () { throw new RuntimeError('Variable "check" does not exist.', 15, $this->source); })()), "state", [], "any", false, false, true, 15) == twig_constant("Pimcore\\Tool\\Requirements\\Check::STATE_WARNING"))) {
                // line 16
                echo "        ";
                $context["icon"] = "overlay-error";
                // line 17
                echo "    ";
            }
            // line 18
            echo "
    <tr>
        <td>
            ";
            // line 21
            if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, (isset($context["check"]) || array_key_exists("check", $context) ? $context["check"] : (function () { throw new RuntimeError('Variable "check" does not exist.', 21, $this->source); })()), "link", [], "any", false, false, true, 21))) {
                // line 22
                echo "                <a href=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["check"]) || array_key_exists("check", $context) ? $context["check"] : (function () { throw new RuntimeError('Variable "check" does not exist.', 22, $this->source); })()), "link", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
                echo "\" target=\"_blank\">";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["check"]) || array_key_exists("check", $context) ? $context["check"] : (function () { throw new RuntimeError('Variable "check" does not exist.', 22, $this->source); })()), "name", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
                echo "</a>
            ";
            } else {
                // line 24
                echo "                ";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["check"]) || array_key_exists("check", $context) ? $context["check"] : (function () { throw new RuntimeError('Variable "check" does not exist.', 24, $this->source); })()), "name", [], "any", false, false, true, 24), 24, $this->source), "html", null, true);
                echo "
            ";
            }
            // line 26
            echo "        </td>

        <td><img src=\"/bundles/pimcoreadmin/img/flat-color-icons/";
            // line 28
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["icon"]) || array_key_exists("icon", $context) ? $context["icon"] : (function () { throw new RuntimeError('Variable "icon" does not exist.', 28, $this->source); })()), 28, $this->source), "html", null, true);
            echo ".svg\" title=\"";
            if (twig_get_attribute($this->env, $this->source, ($context["check"] ?? null), "message", [], "any", true, true, true, 28)) {
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["check"]) || array_key_exists("check", $context) ? $context["check"] : (function () { throw new RuntimeError('Variable "check" does not exist.', 28, $this->source); })()), "message", [], "any", false, false, true, 28), 28, $this->source), "html", null, true);
            }
            echo "\" /></td>
    </tr>
";
            
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

            
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);


            return ('' === $tmp = ob_get_contents()) ? '' : new Markup($tmp, $this->env->getCharset());
        } finally {
            ob_end_clean();
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreAdmin/admin/install/check.html.twig";
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
        return array (  295 => 28,  291 => 26,  285 => 24,  277 => 22,  275 => 21,  270 => 18,  267 => 17,  264 => 16,  261 => 15,  258 => 14,  255 => 13,  252 => 12,  233 => 11,  221 => 139,  219 => 138,  200 => 121,  191 => 119,  187 => 118,  176 => 109,  167 => 107,  163 => 106,  154 => 99,  145 => 97,  141 => 96,  132 => 89,  123 => 87,  119 => 86,  64 => 33,  62 => 32,  59 => 31,  56 => 10,  47 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% if not headless -%}
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"robots\" content=\"noindex, nofollow\" />
</head>
<body>
{%- endif %}

{% macro check(check) %}
    {% set icon = 'high_priority' %}
    {% if check.state == constant('\\Pimcore\\\\Tool\\\\Requirements\\\\Check::STATE_OK') %}
        {% set icon = 'ok' %}
    {% elseif check.state == constant('\\Pimcore\\\\Tool\\\\Requirements\\\\Check::STATE_WARNING') %}
        {% set icon = 'overlay-error' %}
    {% endif %}

    <tr>
        <td>
            {% if check.link is not empty %}
                <a href=\"{{ check.link }}\" target=\"_blank\">{{ check.name }}</a>
            {% else %}
                {{ check.name }}
            {% endif %}
        </td>

        <td><img src=\"/bundles/pimcoreadmin/img/flat-color-icons/{{ icon }}.svg\" title=\"{% if check.message is defined %}{{ check.message }}{%  endif %}\" /></td>
    </tr>
{% endmacro %}

{% import _self as s %}

    <style type=\"text/css\">
        body {
            font-family: Arial, Tahoma, Verdana;
            font-size: 12px;
        }

        h2 {
            font-size: 16px;
            margin: 0;
            padding: 0 0 5px 0;
        }

        table {
            border-collapse: collapse;
        }

        a {
            color: #0066cc;
        }

        .legend {
            display: inline-block;
        }

        div.legend {
            padding-left: 20px;
        }

        span.legend {
            line-height: 30px;
            position: relative;
            padding: 0 30px 0 40px;
        }

        .legend img {
            position: absolute;
            top: 0;
            left: 0;
            width:30px;
        }

        table img {
            width:20px;
        }
    </style>

    <table cellpadding=\"20\">
        <tr>
            <td valign=\"top\">
                <h2>PHP</h2>
                <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">

                    {% for check in checksPHP %}
                        {{ s.check(check) }}
                    {% endfor %}

                </table>
            </td>
            <td valign=\"top\">
                <h2>MySQL</h2>
                <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">

                    {% for check in checksMySQL %}
                        {{ s.check(check) }}
                    {% endfor %}

                </table>
            </td>
            <td valign=\"top\">
                <h2>Filesystem</h2>
                <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">

                    {% for check in checksFS %}
                        {{ s.check(check) }}
                    {% endfor %}

                </table>

                <br />
                <br />

                <h2>CLI Tools &amp; Applications</h2>
                <table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">

                    {% for check in checksApps %}
                        {{ s.check(check) }}
                    {% endfor %}

                </table>
            </td>
        </tr>
    </table>

    <div class=\"legend\">
        <p>
            <b>Explanation:</b>
        </p>
        <p>
            <span class=\"legend\"><img src=\"/bundles/pimcoreadmin/img/flat-color-icons/ok.svg\" />Everything ok</span>
            <span class=\"legend\"><img width=\"26\" height=\"26\" src=\"/bundles/pimcoreadmin/img/flat-color-icons/overlay-error.svg\" /> Recommended but not required</span>
            <span class=\"legend\"><img src=\"/bundles/pimcoreadmin/img/flat-color-icons/high_priority.svg\" /> Required</span>
        </p>
    </div>

{% if not headless -%}
</body>
</html>
{%- endif %}
", "@PimcoreAdmin/admin/install/check.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/install/check.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 1, "import" => 32, "for" => 86, "macro" => 11, "set" => 12);
        static $filters = array("escape" => 22);
        static $functions = array("constant" => 13);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'import', 'for', 'macro', 'set'],
                ['escape'],
                ['constant']
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
