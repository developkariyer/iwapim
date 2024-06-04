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

/* @PimcoreAdmin/admin/misc/admin_css.html.twig */
class __TwigTemplate_b4d8d2927d63c4ad4c9977041ee08d8e extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/misc/admin_css.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/misc/admin_css.html.twig"));

        // line 1
        echo "/**
* Pimcore
*
* This source file is available under two different licenses:
* - GNU General Public License version 3 (GPLv3)
* - Pimcore Commercial License (PCL)
* Full copyright and license information is available in
* LICENSE.md which is distributed with this source code.
*
* @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
* @license    http://www.pimcore.org/license     GPLv3 and PCL
*/

/* THIS FILE IS GENERATED DYNAMICALLY BECAUSE OF DYNAMIC CSS CLASSES IN THE ADMIN */

";
        // line 17
        if ((array_key_exists("customviews", $context) && is_iterable((isset($context["customviews"]) || array_key_exists("customviews", $context) ? $context["customviews"] : (function () { throw new RuntimeError('Variable "customviews" does not exist.', 17, $this->source); })())))) {
            // line 18
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["customviews"]) || array_key_exists("customviews", $context) ? $context["customviews"] : (function () { throw new RuntimeError('Variable "customviews" does not exist.', 18, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["cv"]) {
                // line 19
                echo "        ";
                if (twig_get_attribute($this->env, $this->source, $context["cv"], "icon", [], "array", true, true, true, 19)) {
                    // line 20
                    echo "            ";
                    $context["treetype"] = ((twig_get_attribute($this->env, $this->source, $context["cv"], "treetype", [], "array", false, false, true, 20)) ? (twig_get_attribute($this->env, $this->source, $context["cv"], "treetype", [], "array", false, false, true, 20)) : ("object"));
                    // line 21
                    echo ".pimcore_";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["treetype"]) || array_key_exists("treetype", $context) ? $context["treetype"] : (function () { throw new RuntimeError('Variable "treetype" does not exist.', 21, $this->source); })()), 21, $this->source), "html", null, true);
                    echo "_customview_icon_";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["cv"], "id", [], "array", false, false, true, 21), 21, $this->source), "html", null, true);
                    echo " {
    background: center / contain url(";
                    // line 22
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["cv"], "icon", [], "array", false, false, true, 22), 22, $this->source), "html", null, true);
                    echo ") no-repeat !important;
}
        ";
                }
                // line 25
                echo "    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cv'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        // line 27
        echo "
";
        // line 29
        if ((array_key_exists("languages", $context) && is_iterable((isset($context["languages"]) || array_key_exists("languages", $context) ? $context["languages"] : (function () { throw new RuntimeError('Variable "languages" does not exist.', 29, $this->source); })())))) {
            // line 30
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["languages"]) || array_key_exists("languages", $context) ? $context["languages"] : (function () { throw new RuntimeError('Variable "languages" does not exist.', 30, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                // line 31
                echo "        ";
                $context["iconFile"] = Pimcore\Bundle\AdminBundle\Tool::getLanguageFlagFile($this->sandbox->ensureToStringAllowed($context["language"], 31, $this->source), false);
                // line 32
                echo "/* tab icon for localized fields [ ";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 32, $this->source), "html", null, true);
                echo " ] */
.pimcore_icon_language_";
                // line 33
                echo twig_escape_filter($this->env, twig_lower_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 33, $this->source)), "html", null, true);
                echo " {
    background: url(";
                // line 34
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["iconFile"]) || array_key_exists("iconFile", $context) ? $context["iconFile"] : (function () { throw new RuntimeError('Variable "iconFile" does not exist.', 34, $this->source); })()), 34, $this->source), "html", null, true);
                echo ") center center/contain no-repeat;
}

/* grid column header icon in translations [ ";
                // line 37
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 37, $this->source), "html", null, true);
                echo " ] */
.x-column-header_";
                // line 38
                echo twig_escape_filter($this->env, twig_lower_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 38, $this->source)), "html", null, true);
                echo " {
    background: url(";
                // line 39
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["iconFile"]) || array_key_exists("iconFile", $context) ? $context["iconFile"] : (function () { throw new RuntimeError('Variable "iconFile" does not exist.', 39, $this->source); })()), 39, $this->source), "html", null, true);
                echo ") no-repeat left center/contain !important;
    padding-left:22px !important;
}
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        // line 44
        echo "
";
        // line 46
        if ((array_key_exists("adminSettings", $context) && twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["adminSettings"] ?? null), "branding", [], "array", false, true, true, 46), "color_admin_interface", [], "array", true, true, true, 46))) {
            // line 47
            echo "    ";
            $context["interfaceColor"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["adminSettings"]) || array_key_exists("adminSettings", $context) ? $context["adminSettings"] : (function () { throw new RuntimeError('Variable "adminSettings" does not exist.', 47, $this->source); })()), "branding", [], "array", false, false, true, 47), "color_admin_interface", [], "array", false, false, true, 47);
            // line 48
            echo "#pimcore_signet {
    background-color: ";
            // line 49
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["interfaceColor"]) || array_key_exists("interfaceColor", $context) ? $context["interfaceColor"] : (function () { throw new RuntimeError('Variable "interfaceColor" does not exist.', 49, $this->source); })()), 49, $this->source), "html", null, true);
            echo " !important;
}

#pimcore_avatar {
    background-color: ";
            // line 53
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["interfaceColor"]) || array_key_exists("interfaceColor", $context) ? $context["interfaceColor"] : (function () { throw new RuntimeError('Variable "interfaceColor" does not exist.', 53, $this->source); })()), 53, $this->source), "html", null, true);
            echo " !important;
}

#pimcore_navigation li:hover:after {
    background-color: ";
            // line 57
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["interfaceColor"]) || array_key_exists("interfaceColor", $context) ? $context["interfaceColor"] : (function () { throw new RuntimeError('Variable "interfaceColor" does not exist.', 57, $this->source); })()), 57, $this->source), "html", null, true);
            echo " !important;
}
";
        }
        // line 60
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
        return "@PimcoreAdmin/admin/misc/admin_css.html.twig";
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
        return array (  173 => 60,  167 => 57,  160 => 53,  153 => 49,  150 => 48,  147 => 47,  145 => 46,  142 => 44,  131 => 39,  127 => 38,  123 => 37,  117 => 34,  113 => 33,  108 => 32,  105 => 31,  100 => 30,  98 => 29,  95 => 27,  88 => 25,  82 => 22,  75 => 21,  72 => 20,  69 => 19,  64 => 18,  62 => 17,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("/**
* Pimcore
*
* This source file is available under two different licenses:
* - GNU General Public License version 3 (GPLv3)
* - Pimcore Commercial License (PCL)
* Full copyright and license information is available in
* LICENSE.md which is distributed with this source code.
*
* @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
* @license    http://www.pimcore.org/license     GPLv3 and PCL
*/

/* THIS FILE IS GENERATED DYNAMICALLY BECAUSE OF DYNAMIC CSS CLASSES IN THE ADMIN */

{# custom views #}
{% if customviews is defined and customviews is iterable %}
    {% for cv in customviews %}
        {%  if cv['icon'] is defined %}
            {% set treetype = cv[\"treetype\"] ? cv[\"treetype\"] : \"object\" %}
.pimcore_{{ treetype }}_customview_icon_{{ cv[\"id\"] }} {
    background: center / contain url({{ cv[\"icon\"] }}) no-repeat !important;
}
        {% endif %}
    {% endfor %}
{% endif %}

{# language icons #}
{% if languages is defined and languages is iterable %}
    {% for language in languages %}
        {% set iconFile = pimcore_language_flag(language, false) %}
/* tab icon for localized fields [ {{ language }} ] */
.pimcore_icon_language_{{ language|lower }} {
    background: url({{ iconFile }}) center center/contain no-repeat;
}

/* grid column header icon in translations [ {{ language }} ] */
.x-column-header_{{ language|lower }} {
    background: url({{ iconFile }}) no-repeat left center/contain !important;
    padding-left:22px !important;
}
    {% endfor %}
{% endif %}

{# CUSTOM BRANDING #}
{% if adminSettings is defined and adminSettings['branding']['color_admin_interface'] is defined %}
    {% set interfaceColor = adminSettings['branding']['color_admin_interface'] %}
#pimcore_signet {
    background-color: {{ interfaceColor }} !important;
}

#pimcore_avatar {
    background-color: {{ interfaceColor }} !important;
}

#pimcore_navigation li:hover:after {
    background-color: {{ interfaceColor }} !important;
}
{% endif %}

", "@PimcoreAdmin/admin/misc/admin_css.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/misc/admin_css.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 17, "for" => 18, "set" => 20);
        static $filters = array("escape" => 21, "lower" => 33);
        static $functions = array("pimcore_language_flag" => 31);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'for', 'set'],
                ['escape', 'lower'],
                ['pimcore_language_flag']
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
