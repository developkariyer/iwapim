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

/* @PimcoreCore/Areabrick/wrapper.html.twig */
class __TwigTemplate_ed8d40e81bcaf690e3f850e0a0e0320c extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'areabrickWrapper' => [$this, 'block_areabrickWrapper'],
            'areabrickOpenTag' => [$this, 'block_areabrickOpenTag'],
            'areabrickFrontend' => [$this, 'block_areabrickFrontend'],
            'areabrickCloseTag' => [$this, 'block_areabrickCloseTag'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreCore/Areabrick/wrapper.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreCore/Areabrick/wrapper.html.twig"));

        // line 4
        echo "
";
        // line 6
        echo "
";
        // line 10
        echo "
";
        // line 11
        if (((isset($context["editmode"]) || array_key_exists("editmode", $context) ? $context["editmode"] : (function () { throw new RuntimeError('Variable "editmode" does not exist.', 11, $this->source); })()) && (isset($context["isAreaBlock"]) || array_key_exists("isAreaBlock", $context) ? $context["isAreaBlock"] : (function () { throw new RuntimeError('Variable "isAreaBlock" does not exist.', 11, $this->source); })()))) {
            // line 12
            echo "    <div class=\"pimcore_area_entry pimcore_block_entry\" ";
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeOuterAttributes"]) || array_key_exists("editmodeOuterAttributes", $context) ? $context["editmodeOuterAttributes"] : (function () { throw new RuntimeError('Variable "editmodeOuterAttributes" does not exist.', 12, $this->source); })()), 12, $this->source);
            echo " ";
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 12, $this->source); })()), 12, $this->source);
            echo ">
        <div class=\"pimcore_area_buttons\" ";
            // line 13
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 13, $this->source); })()), 13, $this->source);
            echo ">
            <div class=\"pimcore_area_buttons_inner\">
                <div class=\"pimcore_block_plus_up\" ";
            // line 15
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 15, $this->source); })()), 15, $this->source);
            echo "></div>
                <div class=\"pimcore_block_plus\" ";
            // line 16
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 16, $this->source); })()), 16, $this->source);
            echo "></div>
                <div class=\"pimcore_block_minus\" ";
            // line 17
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 17, $this->source); })()), 17, $this->source);
            echo "></div>
                <div class=\"pimcore_block_up\" ";
            // line 18
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 18, $this->source); })()), 18, $this->source);
            echo "></div>
                <div class=\"pimcore_block_down\" ";
            // line 19
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 19, $this->source); })()), 19, $this->source);
            echo "></div>

                <div class=\"pimcore_block_type\" ";
            // line 21
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 21, $this->source); })()), 21, $this->source);
            echo "></div>
                <div class=\"pimcore_block_options\" ";
            // line 22
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 22, $this->source); })()), 22, $this->source);
            echo "></div>
                <div class=\"pimcore_block_visibility\" ";
            // line 23
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 23, $this->source); })()), 23, $this->source);
            echo "></div>

                ";
            // line 25
            if ((isset($context["editableDialog"]) || array_key_exists("editableDialog", $context) ? $context["editableDialog"] : (function () { throw new RuntimeError('Variable "editableDialog" does not exist.', 25, $this->source); })())) {
                // line 26
                echo "                    <div class=\"pimcore_block_dialog\" ";
                echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 26, $this->source); })()), 26, $this->source);
                echo " ";
                echo $this->sandbox->ensureToStringAllowed((isset($context["editableDialogAttributes"]) || array_key_exists("editableDialogAttributes", $context) ? $context["editableDialogAttributes"] : (function () { throw new RuntimeError('Variable "editableDialogAttributes" does not exist.', 26, $this->source); })()), 26, $this->source);
                echo "></div>
                ";
            }
            // line 28
            echo "
                <div class=\"pimcore_block_label\" ";
            // line 29
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 29, $this->source); })()), 29, $this->source);
            echo "></div>
                <div class=\"pimcore_block_clear\" ";
            // line 30
            echo $this->sandbox->ensureToStringAllowed((isset($context["editmodeGenericAttributes"]) || array_key_exists("editmodeGenericAttributes", $context) ? $context["editmodeGenericAttributes"] : (function () { throw new RuntimeError('Variable "editmodeGenericAttributes" does not exist.', 30, $this->source); })()), 30, $this->source);
            echo "></div>
            </div>
        </div>
        ";
            // line 33
            if ((isset($context["editableDialog"]) || array_key_exists("editableDialog", $context) ? $context["editableDialog"] : (function () { throw new RuntimeError('Variable "editableDialog" does not exist.', 33, $this->source); })())) {
                // line 34
                echo "            <template id=\"dialogBoxConfig-";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["editableDialog"]) || array_key_exists("editableDialog", $context) ? $context["editableDialog"] : (function () { throw new RuntimeError('Variable "editableDialog" does not exist.', 34, $this->source); })()), "id", [], "any", false, false, true, 34), 34, $this->source), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, json_encode($this->sandbox->ensureToStringAllowed((isset($context["editableDialog"]) || array_key_exists("editableDialog", $context) ? $context["editableDialog"] : (function () { throw new RuntimeError('Variable "editableDialog" does not exist.', 34, $this->source); })()), 34, $this->source)), "html", null, true);
                echo "</template>
            ";
                // line 35
                echo $this->sandbox->ensureToStringAllowed((isset($context["dialogHtml"]) || array_key_exists("dialogHtml", $context) ? $context["dialogHtml"] : (function () { throw new RuntimeError('Variable "dialogHtml" does not exist.', 35, $this->source); })()), 35, $this->source);
                echo "
        ";
            }
        }
        // line 38
        echo "
        ";
        // line 39
        $this->displayBlock('areabrickWrapper', $context, $blocks);
        // line 52
        echo "
";
        // line 53
        if (((isset($context["editmode"]) || array_key_exists("editmode", $context) ? $context["editmode"] : (function () { throw new RuntimeError('Variable "editmode" does not exist.', 53, $this->source); })()) && (isset($context["isAreaBlock"]) || array_key_exists("isAreaBlock", $context) ? $context["isAreaBlock"] : (function () { throw new RuntimeError('Variable "isAreaBlock" does not exist.', 53, $this->source); })()))) {
            // line 54
            echo "    </div>
";
        }
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 39
    public function block_areabrickWrapper($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "areabrickWrapper"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "areabrickWrapper"));

        // line 40
        echo "            ";
        $this->displayBlock('areabrickOpenTag', $context, $blocks);
        // line 43
        echo "
                ";
        // line 44
        $this->displayBlock('areabrickFrontend', $context, $blocks);
        // line 47
        echo "
            ";
        // line 48
        $this->displayBlock('areabrickCloseTag', $context, $blocks);
        // line 51
        echo "        ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 40
    public function block_areabrickOpenTag($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "areabrickOpenTag"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "areabrickOpenTag"));

        // line 41
        echo "                ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["brick"]) || array_key_exists("brick", $context) ? $context["brick"] : (function () { throw new RuntimeError('Variable "brick" does not exist.', 41, $this->source); })()), "htmlTagOpen", [(isset($context["info"]) || array_key_exists("info", $context) ? $context["info"] : (function () { throw new RuntimeError('Variable "info" does not exist.', 41, $this->source); })())], "method", false, false, true, 41), 41, $this->source);
        echo "
            ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 44
    public function block_areabrickFrontend($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "areabrickFrontend"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "areabrickFrontend"));

        // line 45
        echo "                    ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["templating"]) || array_key_exists("templating", $context) ? $context["templating"] : (function () { throw new RuntimeError('Variable "templating" does not exist.', 45, $this->source); })()), "render", [(isset($context["viewTemplate"]) || array_key_exists("viewTemplate", $context) ? $context["viewTemplate"] : (function () { throw new RuntimeError('Variable "viewTemplate" does not exist.', 45, $this->source); })()), (isset($context["viewParameters"]) || array_key_exists("viewParameters", $context) ? $context["viewParameters"] : (function () { throw new RuntimeError('Variable "viewParameters" does not exist.', 45, $this->source); })())], "method", false, false, true, 45), 45, $this->source);
        echo "
                ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 48
    public function block_areabrickCloseTag($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "areabrickCloseTag"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "areabrickCloseTag"));

        // line 49
        echo "                ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["brick"]) || array_key_exists("brick", $context) ? $context["brick"] : (function () { throw new RuntimeError('Variable "brick" does not exist.', 49, $this->source); })()), "htmlTagClose", [(isset($context["info"]) || array_key_exists("info", $context) ? $context["info"] : (function () { throw new RuntimeError('Variable "info" does not exist.', 49, $this->source); })())], "method", false, false, true, 49), 49, $this->source);
        echo "
            ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreCore/Areabrick/wrapper.html.twig";
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
        return array (  255 => 49,  245 => 48,  232 => 45,  222 => 44,  209 => 41,  199 => 40,  189 => 51,  187 => 48,  184 => 47,  182 => 44,  179 => 43,  176 => 40,  166 => 39,  154 => 54,  152 => 53,  149 => 52,  147 => 39,  144 => 38,  138 => 35,  131 => 34,  129 => 33,  123 => 30,  119 => 29,  116 => 28,  108 => 26,  106 => 25,  101 => 23,  97 => 22,  93 => 21,  88 => 19,  84 => 18,  80 => 17,  76 => 16,  72 => 15,  67 => 13,  60 => 12,  58 => 11,  55 => 10,  52 => 6,  49 => 4,);
    }

    public function getSourceContext()
    {
        return new Source("{# @var brick \\Pimcore\\Extension\\Document\\Areabrick\\AreabrickInterface #}
{# @var info \\Pimcore\\Model\\Document\\Editable\\Area\\Info #}
{# @var templating \\Symfony\\Component\\Templating\\EngineInterface #}

{# @var editmode bool #}

{# @var viewTemplate string #}
{# @var viewParameters array #}
{# @var editableDialog \\Pimcore\\Extension\\Document\\Areabrick\\EditableDialogBoxConfiguration #}

{% if editmode and isAreaBlock %}
    <div class=\"pimcore_area_entry pimcore_block_entry\" {{ editmodeOuterAttributes|raw }} {{ editmodeGenericAttributes|raw }}>
        <div class=\"pimcore_area_buttons\" {{ editmodeGenericAttributes|raw }}>
            <div class=\"pimcore_area_buttons_inner\">
                <div class=\"pimcore_block_plus_up\" {{ editmodeGenericAttributes|raw }}></div>
                <div class=\"pimcore_block_plus\" {{ editmodeGenericAttributes|raw }}></div>
                <div class=\"pimcore_block_minus\" {{ editmodeGenericAttributes|raw }}></div>
                <div class=\"pimcore_block_up\" {{ editmodeGenericAttributes|raw }}></div>
                <div class=\"pimcore_block_down\" {{ editmodeGenericAttributes|raw }}></div>

                <div class=\"pimcore_block_type\" {{ editmodeGenericAttributes|raw }}></div>
                <div class=\"pimcore_block_options\" {{ editmodeGenericAttributes|raw }}></div>
                <div class=\"pimcore_block_visibility\" {{ editmodeGenericAttributes|raw }}></div>

                {% if editableDialog %}
                    <div class=\"pimcore_block_dialog\" {{ editmodeGenericAttributes|raw }} {{ editableDialogAttributes|raw }}></div>
                {% endif %}

                <div class=\"pimcore_block_label\" {{ editmodeGenericAttributes|raw }}></div>
                <div class=\"pimcore_block_clear\" {{ editmodeGenericAttributes|raw }}></div>
            </div>
        </div>
        {% if editableDialog %}
            <template id=\"dialogBoxConfig-{{ editableDialog.id }}\">{{ editableDialog|json_encode() }}</template>
            {{ dialogHtml|raw }}
        {% endif %}
{% endif %}

        {% block areabrickWrapper %}
            {% block areabrickOpenTag %}
                {{ brick.htmlTagOpen(info) | raw }}
            {% endblock %}

                {% block areabrickFrontend %}
                    {{ templating.render(viewTemplate, viewParameters) | raw }}
                {% endblock %}

            {% block areabrickCloseTag %}
                {{ brick.htmlTagClose(info) | raw }}
            {% endblock %}
        {% endblock %}

{% if editmode and isAreaBlock %}
    </div>
{% endif %}
", "@PimcoreCore/Areabrick/wrapper.html.twig", "/var/www/iwapim/vendor/pimcore/pimcore/bundles/CoreBundle/templates/Areabrick/wrapper.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 11, "block" => 39);
        static $filters = array("raw" => 12, "escape" => 34, "json_encode" => 34);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if', 'block'],
                ['raw', 'escape', 'json_encode'],
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
