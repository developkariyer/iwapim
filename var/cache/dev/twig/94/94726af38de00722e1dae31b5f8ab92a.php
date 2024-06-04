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

/* @PimcoreAdmin/admin/asset/get_preview_video_error.html.twig */
class __TwigTemplate_63573a9a0777677a8bc47905c706a339 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/asset/get_preview_video_error.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/asset/get_preview_video_error.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"/bundles/pimcoreadmin/css/admin.css\"/>

    <style type=\"text/css\">

        /* hide from ie on mac \\*/
        html {
            height: 100%;
            overflow: hidden;
        }

        #wrapper {
            height: 100%;
        }

        /* end hide */

        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

    </style>

</head>

<body>

<table id=\"wrapper\" width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
    <tr>
        <td class=\"error\" align=\"center\" valign=\"center\">
            ";
        // line 36
        if ((array_key_exists("thumbnail", $context) && (twig_get_attribute($this->env, $this->source, (isset($context["thumbnail"]) || array_key_exists("thumbnail", $context) ? $context["thumbnail"] : (function () { throw new RuntimeError('Variable "thumbnail" does not exist.', 36, $this->source); })()), "status", [], "array", false, false, true, 36) == "inprogress"))) {
            // line 37
            echo "                <style type=\"text/css\">
                    .pimcore_editable_video_progress {
                        position:relative;
                        background:#555 url(\"";
            // line 40
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 40, $this->source); })()), "getImageThumbnail", [["width" => 640]], "method", false, false, true, 40), 40, $this->source), "html", null, true);
            echo "\") no-repeat center center;
                        font-family:Arial,Verdana,sans-serif;
                        color:#fff;
                        text-shadow: 0 0 3px #000, 0 0 5px #000, 0 0 1px #000;
                    }
                    .pimcore_editable_video_progress_status {
                        font-size:16px;
                        color:#555;
                        font-family:Arial,Verdana,sans-serif;
                        line-height:66px;
                        background:#fff url(/bundles/pimcoreadmin/img/video-loading.gif) center center no-repeat;
                        width:66px;
                        height:66px;
                        padding:20px;
                        border:1px solid #555;
                        text-align:center;
                        box-shadow: 2px 2px 5px #333;
                        border-radius:20px;
                        top: 137px;
                        left: 267px;
                        position:absolute;
                        opacity: 0.8;
                        text-shadow: none;
                    }
                </style>
                <div class=\"pimcore_editable_video_progress\" style=\"width:640px; height:380px;\">

                    <br />
                    ";
            // line 68
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("video_preview_in_progress", [], "admin"), "html", null, true);
            echo "
                    <br />
                    ";
            // line 70
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("please_wait", [], "admin"), "html", null, true);
            echo "

                    <div class=\"pimcore_editable_video_progress_status\"></div>
                </div>


                <script ";
            // line 76
            echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 76, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 76), 76, $this->source);
            echo ">
                    window.setTimeout(function () {
                        location.reload();
                    }, 5000);
                </script>
            ";
        } elseif ( !Pimcore\Video::isAvailable()) {
            // line 82
            echo "                ";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("preview_not_available", [], "admin"), "html", null, true);
            echo "
                <br />
                ";
            // line 84
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("php_cli_binary_and_or_ffmpeg_binary_setting_is_missing", [], "admin"), "html", null, true);
            echo "
            ";
        } else {
            // line 86
            echo "                ";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("preview_not_available", [], "admin"), "html", null, true);
            echo "
                <br />
                Error unknown, please check the log files
            ";
        }
        // line 90
        echo "        </td>
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
        return "@PimcoreAdmin/admin/asset/get_preview_video_error.html.twig";
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
        return array (  162 => 90,  154 => 86,  149 => 84,  143 => 82,  134 => 76,  125 => 70,  120 => 68,  89 => 40,  84 => 37,  82 => 36,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"/bundles/pimcoreadmin/css/admin.css\"/>

    <style type=\"text/css\">

        /* hide from ie on mac \\*/
        html {
            height: 100%;
            overflow: hidden;
        }

        #wrapper {
            height: 100%;
        }

        /* end hide */

        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

    </style>

</head>

<body>

<table id=\"wrapper\" width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
    <tr>
        <td class=\"error\" align=\"center\" valign=\"center\">
            {% if thumbnail is defined and thumbnail[\"status\"] == \"inprogress\" %}
                <style type=\"text/css\">
                    .pimcore_editable_video_progress {
                        position:relative;
                        background:#555 url(\"{{ asset.getImageThumbnail({\"width\": 640}) }}\") no-repeat center center;
                        font-family:Arial,Verdana,sans-serif;
                        color:#fff;
                        text-shadow: 0 0 3px #000, 0 0 5px #000, 0 0 1px #000;
                    }
                    .pimcore_editable_video_progress_status {
                        font-size:16px;
                        color:#555;
                        font-family:Arial,Verdana,sans-serif;
                        line-height:66px;
                        background:#fff url(/bundles/pimcoreadmin/img/video-loading.gif) center center no-repeat;
                        width:66px;
                        height:66px;
                        padding:20px;
                        border:1px solid #555;
                        text-align:center;
                        box-shadow: 2px 2px 5px #333;
                        border-radius:20px;
                        top: 137px;
                        left: 267px;
                        position:absolute;
                        opacity: 0.8;
                        text-shadow: none;
                    }
                </style>
                <div class=\"pimcore_editable_video_progress\" style=\"width:640px; height:380px;\">

                    <br />
                    {{ 'video_preview_in_progress'|trans([],'admin') }}
                    <br />
                    {{ 'please_wait'|trans([],'admin') }}

                    <div class=\"pimcore_editable_video_progress_status\"></div>
                </div>


                <script {{ pimcore_csp.getNonceHtmlAttribute()|raw }}>
                    window.setTimeout(function () {
                        location.reload();
                    }, 5000);
                </script>
            {% elseif not pimcore_video_is_available() %}
                {{ 'preview_not_available'|trans([],'admin') }}
                <br />
                {{ 'php_cli_binary_and_or_ffmpeg_binary_setting_is_missing'|trans([],'admin') }}
            {% else %}
                {{ 'preview_not_available'|trans([],'admin') }}
                <br />
                Error unknown, please check the log files
            {% endif %}
        </td>
    </tr>
</table>


</body>
</html>
", "@PimcoreAdmin/admin/asset/get_preview_video_error.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/asset/get_preview_video_error.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 36);
        static $filters = array("escape" => 40, "trans" => 68, "raw" => 76);
        static $functions = array("pimcore_video_is_available" => 81);

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape', 'trans', 'raw'],
                ['pimcore_video_is_available']
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
