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

/* @PimcoreAdmin/admin/asset/show_version_image.html.twig */
class __TwigTemplate_8923667f5efad16cc33c84beb41d4f4c extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/asset/show_version_image.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/asset/show_version_image.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">

    <style type=\"text/css\">

        html, body, #wrapper {
            height: 100%;
            margin: 0;
            padding: 0;
            border: none;
            text-align: center;
        }

        #wrapper {
            margin: 0 auto;
            text-align: left;
            vertical-align: middle;
            width: 400px;
        }


    </style>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"/bundles/pimcoreadmin/css/object_versions.css\"/>

</head>

<body>

";
        // line 31
        $context["tempFile"] = twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 31, $this->source); })()), "getTemporaryFile", [], "method", false, false, true, 31);
        // line 32
        $context["dataUri"] = $this->extensions['Pimcore\Twig\Extension\HelpersExtension']->getImageVersionPreview($this->sandbox->ensureToStringAllowed((isset($context["tempFile"]) || array_key_exists("tempFile", $context) ? $context["tempFile"] : (function () { throw new RuntimeError('Variable "tempFile" does not exist.', 32, $this->source); })()), 32, $this->source));
        // line 33
        echo "
<table id=\"wrapper\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
    <tr>
        <td align=\"center\">
            <img src=\"";
        // line 37
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["dataUri"]) || array_key_exists("dataUri", $context) ? $context["dataUri"] : (function () { throw new RuntimeError('Variable "dataUri" does not exist.', 37, $this->source); })()), 37, $this->source), "html", null, true);
        echo "\"/>
              <table class=\"preview\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                    <tbody>
                        <tr class=\"odd\">
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>";
        // line 46
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 46, $this->source); })()), "getFileName", [], "method", false, false, true, 46), 46, $this->source), "html", null, true);
        echo "</td>
                        </tr>
                        <tr>
                            <td>Creation Date</td>
                            <td>";
        // line 50
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 50, $this->source); })()), "getCreationDate", [], "method", false, false, true, 50), 50, $this->source), "m/d/Y H:i:s"), "html", null, true);
        echo "</td>
                        </tr>
                        <tr>
                            <td>Modification Date</td>
                            <td>";
        // line 54
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 54, $this->source); })()), "getModificationDate", [], "method", false, false, true, 54), 54, $this->source), "m/d/Y H:i:s"), "html", null, true);
        echo "</td>
                        </tr>
                        <tr>
                            <td>File Size</td>
                            <td>";
        // line 58
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 58, $this->source); })()), "getFileSize", [true], "method", false, false, true, 58), 58, $this->source), "html", null, true);
        echo " </td>
                        </tr>
                        <tr>
                            <td>Mime Type</td>
                            <td>";
        // line 62
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 62, $this->source); })()), "getMimeType", [], "method", false, false, true, 62), 62, $this->source), "html", null, true);
        echo "</td>
                        </tr>
                        <tr>
                            <td>Dimensions</td>
                            <td>
                                ";
        // line 67
        if (is_iterable(twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 67, $this->source); })()), "getDimensions", [], "method", false, false, true, 67))) {
            // line 68
            echo "                                    ";
            echo twig_escape_filter($this->env, (($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 68, $this->source); })()), "getDimensions", [], "method", false, false, true, 68), "width", [], "array", false, false, true, 68), 68, $this->source) . " X ") . $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 68, $this->source); })()), "getDimensions", [], "method", false, false, true, 68), "height", [], "array", false, false, true, 68), 68, $this->source)), "html", null, true);
            echo "
                                ";
        }
        // line 70
        echo "                            </td>
                        </tr>
                        ";
        // line 72
        if (twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 72, $this->source); })()), "getHasMetadata", [], "method", false, false, true, 72)) {
            // line 73
            echo "                            ";
            $context["metaData"] = twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 73, $this->source); })()), "getMetadata", [], "method", false, false, true, 73);
            // line 74
            echo "
                            ";
            // line 75
            if ((is_iterable((isset($context["metaData"]) || array_key_exists("metaData", $context) ? $context["metaData"] : (function () { throw new RuntimeError('Variable "metaData" does not exist.', 75, $this->source); })())) && (twig_length_filter($this->env, (isset($context["metaData"]) || array_key_exists("metaData", $context) ? $context["metaData"] : (function () { throw new RuntimeError('Variable "metaData" does not exist.', 75, $this->source); })())) > 0))) {
                // line 76
                echo "                                ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["metaData"]) || array_key_exists("metaData", $context) ? $context["metaData"] : (function () { throw new RuntimeError('Variable "metaData" does not exist.', 76, $this->source); })()));
                foreach ($context['_seq'] as $context["_key"] => $context["data"]) {
                    // line 77
                    echo "                                    ";
                    $context["preview"] = twig_get_attribute($this->env, $this->source, $context["data"], "data", [], "array", false, false, true, 77);
                    // line 78
                    echo "                                    ";
                    $context["instance"] = twig_get_attribute($this->env, $this->source, (isset($context["loader"]) || array_key_exists("loader", $context) ? $context["loader"] : (function () { throw new RuntimeError('Variable "loader" does not exist.', 78, $this->source); })()), "build", [twig_get_attribute($this->env, $this->source, $context["data"], "type", [], "array", false, false, true, 78)], "method", false, false, true, 78);
                    // line 79
                    echo "                                    ";
                    $context["preview"] = twig_get_attribute($this->env, $this->source, (isset($context["instance"]) || array_key_exists("instance", $context) ? $context["instance"] : (function () { throw new RuntimeError('Variable "instance" does not exist.', 79, $this->source); })()), "getVersionPreview", [(isset($context["preview"]) || array_key_exists("preview", $context) ? $context["preview"] : (function () { throw new RuntimeError('Variable "preview" does not exist.', 79, $this->source); })()), $context["data"]], "method", false, false, true, 79);
                    // line 80
                    echo "
                                    <tr>
                                        <td>";
                    // line 82
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["data"], "name", [], "array", false, false, true, 82), 82, $this->source), "html", null, true);
                    echo " (";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["data"], "type", [], "array", false, false, true, 82), 82, $this->source), "html", null, true);
                    echo ")</td>
                                        <td>";
                    // line 83
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["preview"]) || array_key_exists("preview", $context) ? $context["preview"] : (function () { throw new RuntimeError('Variable "preview" does not exist.', 83, $this->source); })()), 83, $this->source), "html", null, true);
                    echo "</td>
                                    </tr>
                                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['data'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 86
                echo "                            ";
            }
            // line 87
            echo "                        ";
        }
        // line 88
        echo "                    </tbody>
              </table>
        </td>
    </tr>
</table>

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
        return "@PimcoreAdmin/admin/asset/show_version_image.html.twig";
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
        return array (  196 => 88,  193 => 87,  190 => 86,  181 => 83,  175 => 82,  171 => 80,  168 => 79,  165 => 78,  162 => 77,  157 => 76,  155 => 75,  152 => 74,  149 => 73,  147 => 72,  143 => 70,  137 => 68,  135 => 67,  127 => 62,  120 => 58,  113 => 54,  106 => 50,  99 => 46,  87 => 37,  81 => 33,  79 => 32,  77 => 31,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">

    <style type=\"text/css\">

        html, body, #wrapper {
            height: 100%;
            margin: 0;
            padding: 0;
            border: none;
            text-align: center;
        }

        #wrapper {
            margin: 0 auto;
            text-align: left;
            vertical-align: middle;
            width: 400px;
        }


    </style>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"/bundles/pimcoreadmin/css/object_versions.css\"/>

</head>

<body>

{% set tempFile = asset.getTemporaryFile() %}
{% set dataUri = pimcore_image_version_preview(tempFile) %}

<table id=\"wrapper\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
    <tr>
        <td align=\"center\">
            <img src=\"{{ dataUri }}\"/>
              <table class=\"preview\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                    <tbody>
                        <tr class=\"odd\">
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>{{ asset.getFileName() }}</td>
                        </tr>
                        <tr>
                            <td>Creation Date</td>
                            <td>{{ asset.getCreationDate()|date('m/d/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td>Modification Date</td>
                            <td>{{ asset.getModificationDate()|date('m/d/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td>File Size</td>
                            <td>{{ asset.getFileSize(true) }} </td>
                        </tr>
                        <tr>
                            <td>Mime Type</td>
                            <td>{{ asset.getMimeType() }}</td>
                        </tr>
                        <tr>
                            <td>Dimensions</td>
                            <td>
                                {% if asset.getDimensions() is iterable %}
                                    {{ asset.getDimensions()[\"width\"] ~ ' X ' ~ asset.getDimensions()[\"height\"] }}
                                {% endif %}
                            </td>
                        </tr>
                        {% if asset.getHasMetadata() %}
                            {% set metaData = asset.getMetadata() %}

                            {% if metaData is iterable and metaData|length > 0 %}
                                {% for data in metaData %}
                                    {% set preview = data[\"data\"] %}
                                    {% set instance = loader.build(data['type']) %}
                                    {% set preview = instance.getVersionPreview(preview,data) %}

                                    <tr>
                                        <td>{{ data['name'] }} ({{ data['type'] }})</td>
                                        <td>{{ preview }}</td>
                                    </tr>
                                {% endfor %}
                            {% endif %}
                        {% endif %}
                    </tbody>
              </table>
        </td>
    </tr>
</table>

</body>
</html>
", "@PimcoreAdmin/admin/asset/show_version_image.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/asset/show_version_image.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 31, "if" => 67, "for" => 76);
        static $filters = array("escape" => 37, "date" => 50, "length" => 75);
        static $functions = array("pimcore_image_version_preview" => 32);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'for'],
                ['escape', 'date', 'length'],
                ['pimcore_image_version_preview']
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
