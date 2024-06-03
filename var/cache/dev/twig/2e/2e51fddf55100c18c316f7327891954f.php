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

/* @PimcoreAdmin/admin/misc/icon_list.html.twig */
class __TwigTemplate_9535a0225cc8a8b32233a97e0e2b8265 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/misc/icon_list.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/misc/icon_list.html.twig"));

        // line 1
        $context["webRoot"] = twig_constant("PIMCORE_WEB_ROOT");
        // line 2
        echo "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <title>Pimcore :: Icon list</title>
    <style type=\"text/css\">

        body {
            font-family: Arial;
            font-size: 12px;
        }

        .icons {
            width:1200px;
            margin: 0 auto;
        }

        .icon {
            text-align: center;
            width:100px;
            height:75px;
            margin: 0 10px 20px 0;
            float: left;
            font-size: 10px;
            word-wrap: break-word;
            cursor: copy;
            padding-top: 5px;
            box-sizing: border-box;
        }

        .icon.black {
            background-color: #0C0F12;
        }

        .icon.black .label {
            color: #fff;
        }

        .info {
            text-align: center;
            margin-bottom: 30px;
            clear: both;
            font-size: 22px;
            padding-top: 50px;
        }

        .info small {
            font-size: 16px;
        }

        .icon img {
            width: 50px;
        }

        .language-icon img{
            width: 16px;
            cursor: copy;
        }

        .variant + .icon:not(.variant){
            border: 2px dotted green;
        }
        .variant{
            display: none;
            background-color: #eee;
        }
    </style>
</head>
<body>

<div class=\"info\">
    <a target=\"_blank\">Color Icons</a>
    <br>
    <small>based on the <a href=\"https://github.com/google/material-design-icons/blob/master/LICENSE\" target=\"_blank\">Material Design Icons</a></small>
</div>

<div id=\"color_icons\" class=\"icons\">
    <div style=\"margin-bottom: 20px; text-align: left\">ℹ Click on icon to copy path to clipboard.</div>
    ";
        // line 81
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["colorIcons"]) || array_key_exists("colorIcons", $context) ? $context["colorIcons"] : (function () { throw new RuntimeError('Variable "colorIcons" does not exist.', 81, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["icon"]) {
            // line 82
            echo "        ";
            $context["iconPath"] = twig_replace_filter($this->sandbox->ensureToStringAllowed($context["icon"], 82, $this->source), [(isset($context["webRoot"]) || array_key_exists("webRoot", $context) ? $context["webRoot"] : (function () { throw new RuntimeError('Variable "webRoot" does not exist.', 82, $this->source); })()) => ""]);
            // line 83
            echo "        <div class=\"icon\">
            ";
            // line 84
            echo $this->extensions['Pimcore\Bundle\AdminBundle\Twig\Extension\AdminExtension']->inlineIcon($this->sandbox->ensureToStringAllowed($context["icon"], 84, $this->source));
            echo "
            <div class=\"label\">
                ";
            // line 86
            echo ((twig_in_filter((isset($context["iconPath"]) || array_key_exists("iconPath", $context) ? $context["iconPath"] : (function () { throw new RuntimeError('Variable "iconPath" does not exist.', 86, $this->source); })()), (isset($context["iconsCss"]) || array_key_exists("iconsCss", $context) ? $context["iconsCss"] : (function () { throw new RuntimeError('Variable "iconsCss" does not exist.', 86, $this->source); })()))) ? ("*") : (""));
            echo "
                ";
            // line 87
            echo twig_escape_filter($this->env, $this->extensions['Pimcore\Twig\Extension\HelpersExtension']->basenameFilter($this->sandbox->ensureToStringAllowed((isset($context["iconPath"]) || array_key_exists("iconPath", $context) ? $context["iconPath"] : (function () { throw new RuntimeError('Variable "iconPath" does not exist.', 87, $this->source); })()), 87, $this->source)), "html", null, true);
            echo "
            </div>
        </div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['icon'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 91
        echo "</div>

<div class=\"info\">
    <a target=\"_blank\">White Icons</a>
    <br>
    <small>based on the <a href=\"https://github.com/google/material-design-icons/blob/master/LICENSE\" target=\"_blank\">Material Design Icons</a></small>
</div>

<div id=\"white_icons\" class=\"icons\">
    ";
        // line 100
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["whiteIcons"]) || array_key_exists("whiteIcons", $context) ? $context["whiteIcons"] : (function () { throw new RuntimeError('Variable "whiteIcons" does not exist.', 100, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["icon"]) {
            // line 101
            echo "        ";
            $context["iconPath"] = twig_replace_filter($this->sandbox->ensureToStringAllowed($context["icon"], 101, $this->source), [(isset($context["webRoot"]) || array_key_exists("webRoot", $context) ? $context["webRoot"] : (function () { throw new RuntimeError('Variable "webRoot" does not exist.', 101, $this->source); })()) => ""]);
            // line 102
            echo "        <div class=\"icon black\">
            ";
            // line 103
            echo $this->extensions['Pimcore\Bundle\AdminBundle\Twig\Extension\AdminExtension']->inlineIcon($this->sandbox->ensureToStringAllowed($context["icon"], 103, $this->source));
            echo "
            <div class=\"label\">
                ";
            // line 105
            echo ((twig_in_filter((isset($context["iconPath"]) || array_key_exists("iconPath", $context) ? $context["iconPath"] : (function () { throw new RuntimeError('Variable "iconPath" does not exist.', 105, $this->source); })()), (isset($context["iconsCss"]) || array_key_exists("iconsCss", $context) ? $context["iconsCss"] : (function () { throw new RuntimeError('Variable "iconsCss" does not exist.', 105, $this->source); })()))) ? ("*") : (""));
            echo "
                ";
            // line 106
            echo twig_escape_filter($this->env, $this->extensions['Pimcore\Twig\Extension\HelpersExtension']->basenameFilter($this->sandbox->ensureToStringAllowed((isset($context["iconPath"]) || array_key_exists("iconPath", $context) ? $context["iconPath"] : (function () { throw new RuntimeError('Variable "iconPath" does not exist.', 106, $this->source); })()), 106, $this->source)), "html", null, true);
            echo "
            </div>
        </div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['icon'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 110
        echo "</div>

<div class=\"info\">
    <a href=\"https://github.com/twitter/twemoji\" target=\"_blank\">Source (Twemoji)</a>
</div>

<div id=\"twemoji\" class=\"icons\">
    <div style=\"margin-bottom: 20px; text-align: left\">ℹ Click on icon to copy path to clipboard.</div>
    <div style=\"margin-bottom: 20px; text-align: left\">ℹ Click on icon with green border to display all its related variants. Click on the letter to display flags with the clicked initial</div>
    ";
        // line 119
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["twemoji"]) || array_key_exists("twemoji", $context) ? $context["twemoji"] : (function () { throw new RuntimeError('Variable "twemoji" does not exist.', 119, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["icon"]) {
            // line 120
            echo "        ";
            $context["iconPath"] = twig_replace_filter($this->sandbox->ensureToStringAllowed($context["icon"], 120, $this->source), [(isset($context["webRoot"]) || array_key_exists("webRoot", $context) ? $context["webRoot"] : (function () { throw new RuntimeError('Variable "webRoot" does not exist.', 120, $this->source); })()) => ""]);
            // line 121
            echo "        ";
            // line 125
            echo "        ";
            if ((twig_in_filter("-", (isset($context["iconPath"]) || array_key_exists("iconPath", $context) ? $context["iconPath"] : (function () { throw new RuntimeError('Variable "iconPath" does not exist.', 125, $this->source); })())) && (twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_split_filter($this->env, $this->extensions['Pimcore\Twig\Extension\HelpersExtension']->basenameFilter((isset($context["iconPath"]) || array_key_exists("iconPath", $context) ? $context["iconPath"] : (function () { throw new RuntimeError('Variable "iconPath" does not exist.', 125, $this->source); })())), "-"), 0, [], "array", false, false, true, 125)) > 3))) {
                // line 126
                echo "            <div class=\"icon variant\">
                ";
                // line 127
                echo $this->extensions['Pimcore\Bundle\AdminBundle\Twig\Extension\AdminExtension']->twemojiVariantIcon($this->sandbox->ensureToStringAllowed($context["icon"], 127, $this->source));
                echo "
                <div class=\"label\">";
                // line 128
                echo twig_escape_filter($this->env, $this->extensions['Pimcore\Twig\Extension\HelpersExtension']->basenameFilter($this->sandbox->ensureToStringAllowed((isset($context["iconPath"]) || array_key_exists("iconPath", $context) ? $context["iconPath"] : (function () { throw new RuntimeError('Variable "iconPath" does not exist.', 128, $this->source); })()), 128, $this->source)), "html", null, true);
                echo "</div>
            </div>
        ";
            } else {
                // line 131
                echo "            <div class=\"icon\">
                ";
                // line 132
                echo $this->extensions['Pimcore\Bundle\AdminBundle\Twig\Extension\AdminExtension']->inlineIcon($this->sandbox->ensureToStringAllowed($context["icon"], 132, $this->source));
                echo "
                <div class=\"label\">";
                // line 133
                echo twig_escape_filter($this->env, $this->extensions['Pimcore\Twig\Extension\HelpersExtension']->basenameFilter($this->sandbox->ensureToStringAllowed((isset($context["iconPath"]) || array_key_exists("iconPath", $context) ? $context["iconPath"] : (function () { throw new RuntimeError('Variable "iconPath" does not exist.', 133, $this->source); })()), 133, $this->source)), "html", null, true);
                echo "</div>
            </div>
        ";
            }
            // line 136
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['icon'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 137
        echo "</div>

<div class=\"info\">
    Flags
</div>

<table>
    <tr>
        <th>Flag</th>
        <th>Code</th>
        <th>Name</th>
    </tr>
    ";
        // line 149
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["languageOptions"]) || array_key_exists("languageOptions", $context) ? $context["languageOptions"] : (function () { throw new RuntimeError('Variable "languageOptions" does not exist.', 149, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["langOpt"]) {
            // line 150
            echo "        <tr>
            <td class=\"language-icon\">";
            // line 151
            echo $this->extensions['Pimcore\Bundle\AdminBundle\Twig\Extension\AdminExtension']->inlineIcon($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["langOpt"], "flag", [], "array", false, false, true, 151), 151, $this->source));
            echo "</td>
            <td>";
            // line 152
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["langOpt"], "language", [], "array", false, false, true, 152), 152, $this->source), "html", null, true);
            echo "</td>
            <td>";
            // line 153
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["langOpt"], "display", [], "array", false, false, true, 153), 153, $this->source), "html", null, true);
            echo "</td>
        </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['langOpt'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 156
        echo "</table>

<script
    src=\"https://code.jquery.com/jquery-3.7.0.slim.js\"
    integrity=\"sha256-7GO+jepT9gJe9LB4XFf8snVOjX3iYNb0FHYr5LI1N5c=\"
    crossorigin=\"anonymous\"
    ";
        // line 162
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 162, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 162), 162, $this->source);
        echo "></script>
<script src=\"/bundles/pimcoreadmin/js/pimcore/common.js\"></script>
<script src=\"/bundles/pimcoreadmin/js/pimcore/functions.js\"></script>
<script src=\"/bundles/pimcoreadmin/js/pimcore/helpers.js\"></script>

<script ";
        // line 167
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 167, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 167), 167, $this->source);
        echo ">
    \$( document ).ready(function() {
        // Add click event to copy icon path on all icons
        \$('img').each(function () {
            \$(this).click(function () {
                pimcore.helpers.copyStringToClipboard(\$(this).data('imgpath'));
            });
        });
        // Twimoji only: clicking on icon with green border displays all its variants
        \$('.icon:not(.variant)').each(function () {
            \$(this).click(function () {
                let variants = \$(this).prevUntil('div.icon:not(.variant)');
                variants.each(function () {
                    if (!\$(this).is(':visible')) {
                        let img = \$(this).children('img');
                        img.attr('src', img.data('imgpath'));
                        \$(this).show();
                    }
                });
            });
        });
    });
</script>

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
        return "@PimcoreAdmin/admin/misc/icon_list.html.twig";
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
        return array (  306 => 167,  298 => 162,  290 => 156,  281 => 153,  277 => 152,  273 => 151,  270 => 150,  266 => 149,  252 => 137,  246 => 136,  240 => 133,  236 => 132,  233 => 131,  227 => 128,  223 => 127,  220 => 126,  217 => 125,  215 => 121,  212 => 120,  208 => 119,  197 => 110,  187 => 106,  183 => 105,  178 => 103,  175 => 102,  172 => 101,  168 => 100,  157 => 91,  147 => 87,  143 => 86,  138 => 84,  135 => 83,  132 => 82,  128 => 81,  47 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% set webRoot = constant('PIMCORE_WEB_ROOT') %}

<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <title>Pimcore :: Icon list</title>
    <style type=\"text/css\">

        body {
            font-family: Arial;
            font-size: 12px;
        }

        .icons {
            width:1200px;
            margin: 0 auto;
        }

        .icon {
            text-align: center;
            width:100px;
            height:75px;
            margin: 0 10px 20px 0;
            float: left;
            font-size: 10px;
            word-wrap: break-word;
            cursor: copy;
            padding-top: 5px;
            box-sizing: border-box;
        }

        .icon.black {
            background-color: #0C0F12;
        }

        .icon.black .label {
            color: #fff;
        }

        .info {
            text-align: center;
            margin-bottom: 30px;
            clear: both;
            font-size: 22px;
            padding-top: 50px;
        }

        .info small {
            font-size: 16px;
        }

        .icon img {
            width: 50px;
        }

        .language-icon img{
            width: 16px;
            cursor: copy;
        }

        .variant + .icon:not(.variant){
            border: 2px dotted green;
        }
        .variant{
            display: none;
            background-color: #eee;
        }
    </style>
</head>
<body>

<div class=\"info\">
    <a target=\"_blank\">Color Icons</a>
    <br>
    <small>based on the <a href=\"https://github.com/google/material-design-icons/blob/master/LICENSE\" target=\"_blank\">Material Design Icons</a></small>
</div>

<div id=\"color_icons\" class=\"icons\">
    <div style=\"margin-bottom: 20px; text-align: left\">ℹ Click on icon to copy path to clipboard.</div>
    {% for icon in colorIcons %}
        {% set iconPath = icon|replace({(webRoot): ''}) %}
        <div class=\"icon\">
            {{ icon | pimcore_inline_icon | raw }}
            <div class=\"label\">
                {{ iconPath in iconsCss  ? '*' : '' }}
                {{ iconPath|basename }}
            </div>
        </div>
    {% endfor %}
</div>

<div class=\"info\">
    <a target=\"_blank\">White Icons</a>
    <br>
    <small>based on the <a href=\"https://github.com/google/material-design-icons/blob/master/LICENSE\" target=\"_blank\">Material Design Icons</a></small>
</div>

<div id=\"white_icons\" class=\"icons\">
    {% for icon in whiteIcons %}
        {% set iconPath = icon|replace({(webRoot): ''}) %}
        <div class=\"icon black\">
            {{ icon | pimcore_inline_icon | raw }}
            <div class=\"label\">
                {{ iconPath in iconsCss  ? '*' : '' }}
                {{ iconPath|basename }}
            </div>
        </div>
    {% endfor %}
</div>

<div class=\"info\">
    <a href=\"https://github.com/twitter/twemoji\" target=\"_blank\">Source (Twemoji)</a>
</div>

<div id=\"twemoji\" class=\"icons\">
    <div style=\"margin-bottom: 20px; text-align: left\">ℹ Click on icon to copy path to clipboard.</div>
    <div style=\"margin-bottom: 20px; text-align: left\">ℹ Click on icon with green border to display all its related variants. Click on the letter to display flags with the clicked initial</div>
    {% for icon in twemoji %}
        {% set iconPath = icon|replace({(webRoot): ''}) %}
        {#
            Any icon with basename that has a dash will be considered as a variant to avoid recurisvely searching for \"parent\" icon.
            It happens that all icon that have variants have at least a prefix of 4-5 characters.
        #}
        {% if '-' in iconPath and iconPath|basename|split('-')[0]|length > 3 %}
            <div class=\"icon variant\">
                {{ icon | pimcore_twemoji_variant_icon | raw }}
                <div class=\"label\">{{ iconPath|basename }}</div>
            </div>
        {% else %}
            <div class=\"icon\">
                {{ icon | pimcore_inline_icon | raw }}
                <div class=\"label\">{{ iconPath|basename }}</div>
            </div>
        {% endif %}
    {% endfor %}
</div>

<div class=\"info\">
    Flags
</div>

<table>
    <tr>
        <th>Flag</th>
        <th>Code</th>
        <th>Name</th>
    </tr>
    {% for langOpt in languageOptions %}
        <tr>
            <td class=\"language-icon\">{{ langOpt['flag'] | pimcore_inline_icon | raw }}</td>
            <td>{{ langOpt['language'] }}</td>
            <td>{{ langOpt['display'] }}</td>
        </tr>
    {% endfor %}
</table>

<script
    src=\"https://code.jquery.com/jquery-3.7.0.slim.js\"
    integrity=\"sha256-7GO+jepT9gJe9LB4XFf8snVOjX3iYNb0FHYr5LI1N5c=\"
    crossorigin=\"anonymous\"
    {{ pimcore_csp.getNonceHtmlAttribute()|raw }}></script>
<script src=\"/bundles/pimcoreadmin/js/pimcore/common.js\"></script>
<script src=\"/bundles/pimcoreadmin/js/pimcore/functions.js\"></script>
<script src=\"/bundles/pimcoreadmin/js/pimcore/helpers.js\"></script>

<script {{ pimcore_csp.getNonceHtmlAttribute()|raw }}>
    \$( document ).ready(function() {
        // Add click event to copy icon path on all icons
        \$('img').each(function () {
            \$(this).click(function () {
                pimcore.helpers.copyStringToClipboard(\$(this).data('imgpath'));
            });
        });
        // Twimoji only: clicking on icon with green border displays all its variants
        \$('.icon:not(.variant)').each(function () {
            \$(this).click(function () {
                let variants = \$(this).prevUntil('div.icon:not(.variant)');
                variants.each(function () {
                    if (!\$(this).is(':visible')) {
                        let img = \$(this).children('img');
                        img.attr('src', img.data('imgpath'));
                        \$(this).show();
                    }
                });
            });
        });
    });
</script>

</body>
</html>
", "@PimcoreAdmin/admin/misc/icon_list.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/misc/icon_list.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 1, "for" => 81, "if" => 125);
        static $filters = array("replace" => 82, "raw" => 84, "pimcore_inline_icon" => 84, "escape" => 87, "basename" => 87, "length" => 125, "split" => 125, "pimcore_twemoji_variant_icon" => 127);
        static $functions = array("constant" => 1);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'for', 'if'],
                ['replace', 'raw', 'pimcore_inline_icon', 'escape', 'basename', 'length', 'split', 'pimcore_twemoji_variant_icon'],
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
