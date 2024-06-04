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

/* @PimcoreCore/Workflow/NotificationEmail/notificationEmail.html.twig */
class __TwigTemplate_bd93ae569ec466fe91d80523dbf0b3c6 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreCore/Workflow/NotificationEmail/notificationEmail.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreCore/Workflow/NotificationEmail/notificationEmail.html.twig"));

        // line 8
        echo "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
</head>
<body>
";
        // line 15
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["translator"]) || array_key_exists("translator", $context) ? $context["translator"] : (function () { throw new RuntimeError('Variable "translator" does not exist.', 15, $this->source); })()), "trans", ["workflow_change_email_notification_text", [(($this->sandbox->ensureToStringAllowed((isset($context["subjectType"]) || array_key_exists("subjectType", $context) ? $context["subjectType"] : (function () { throw new RuntimeError('Variable "subjectType" does not exist.', 15, $this->source); })()), 15, $this->source) . " ") . $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["subject"]) || array_key_exists("subject", $context) ? $context["subject"] : (function () { throw new RuntimeError('Variable "subject" does not exist.', 15, $this->source); })()), "getFullPath", [], "method", false, false, true, 15), 15, $this->source)), twig_get_attribute($this->env, $this->source, (isset($context["subject"]) || array_key_exists("subject", $context) ? $context["subject"] : (function () { throw new RuntimeError('Variable "subject" does not exist.', 15, $this->source); })()), "getId", [], "method", false, false, true, 15), twig_get_attribute($this->env, $this->source, (isset($context["translator"]) || array_key_exists("translator", $context) ? $context["translator"] : (function () { throw new RuntimeError('Variable "translator" does not exist.', 15, $this->source); })()), "trans", [(isset($context["action"]) || array_key_exists("action", $context) ? $context["action"] : (function () { throw new RuntimeError('Variable "action" does not exist.', 15, $this->source); })()), [], "admin", (isset($context["lang"]) || array_key_exists("lang", $context) ? $context["lang"] : (function () { throw new RuntimeError('Variable "lang" does not exist.', 15, $this->source); })())], "method", false, false, true, 15), twig_get_attribute($this->env, $this->source, (isset($context["translator"]) || array_key_exists("translator", $context) ? $context["translator"] : (function () { throw new RuntimeError('Variable "translator" does not exist.', 15, $this->source); })()), "trans", [twig_get_attribute($this->env, $this->source, (isset($context["workflow"]) || array_key_exists("workflow", $context) ? $context["workflow"] : (function () { throw new RuntimeError('Variable "workflow" does not exist.', 15, $this->source); })()), "getName", [], "method", false, false, true, 15), [], "admin", (isset($context["lang"]) || array_key_exists("lang", $context) ? $context["lang"] : (function () { throw new RuntimeError('Variable "lang" does not exist.', 15, $this->source); })())], "method", false, false, true, 15)], "admin", (isset($context["lang"]) || array_key_exists("lang", $context) ? $context["lang"] : (function () { throw new RuntimeError('Variable "lang" does not exist.', 15, $this->source); })())], "method", false, false, true, 15), 15, $this->source), "html", null, true);
        echo "<br>
";
        // line 16
        if ( !twig_test_empty(twig_trim_filter((isset($context["deeplink"]) || array_key_exists("deeplink", $context) ? $context["deeplink"] : (function () { throw new RuntimeError('Variable "deeplink" does not exist.', 16, $this->source); })())))) {
            // line 17
            echo "    <a href=\"";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["deeplink"]) || array_key_exists("deeplink", $context) ? $context["deeplink"] : (function () { throw new RuntimeError('Variable "deeplink" does not exist.', 17, $this->source); })()), 17, $this->source), "html", null, true);
            echo "\" target=\"_blank\">";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["translator"]) || array_key_exists("translator", $context) ? $context["translator"] : (function () { throw new RuntimeError('Variable "translator" does not exist.', 17, $this->source); })()), "trans", ["workflow_change_email_notification_deeplink", [], "admin", (isset($context["lang"]) || array_key_exists("lang", $context) ? $context["lang"] : (function () { throw new RuntimeError('Variable "lang" does not exist.', 17, $this->source); })())], "method", false, false, true, 17), 17, $this->source), "html", null, true);
            echo "</a><br>
";
        }
        // line 19
        echo "<br>

";
        // line 21
        if ( !twig_test_empty(twig_trim_filter((isset($context["note_description"]) || array_key_exists("note_description", $context) ? $context["note_description"] : (function () { throw new RuntimeError('Variable "note_description" does not exist.', 21, $this->source); })())))) {
            // line 22
            echo "    <strong>";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["translator"]) || array_key_exists("translator", $context) ? $context["translator"] : (function () { throw new RuntimeError('Variable "translator" does not exist.', 22, $this->source); })()), "trans", ["workflow_change_email_notification_note", [], "admin", (isset($context["lang"]) || array_key_exists("lang", $context) ? $context["lang"] : (function () { throw new RuntimeError('Variable "lang" does not exist.', 22, $this->source); })())], "method", false, false, true, 22), 22, $this->source), "html", null, true);
            echo "</strong>
    <p>";
            // line 23
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["note_description"]) || array_key_exists("note_description", $context) ? $context["note_description"] : (function () { throw new RuntimeError('Variable "note_description" does not exist.', 23, $this->source); })()), 23, $this->source), "html", null, true);
            echo "</p>
";
        }
        // line 25
        echo "</body>
</html>";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreCore/Workflow/NotificationEmail/notificationEmail.html.twig";
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
        return array (  84 => 25,  79 => 23,  74 => 22,  72 => 21,  68 => 19,  60 => 17,  58 => 16,  54 => 15,  45 => 8,);
    }

    public function getSourceContext()
    {
        return new Source("{# @var subjectType string #}
{# @var subject AbstractElement #}
{# @var action string #}
{# @var deeplink string #}
{# @var note_description string #}
{# @var translator \\Symfony\\Contracts\\Translation\\TranslatorInterface #}
{# @var lang string #}

<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
</head>
<body>
{{ translator.trans('workflow_change_email_notification_text', [subjectType ~ ' ' ~ subject.getFullPath(), subject.getId(), translator.trans(action, [], 'admin', lang), translator.trans(workflow.getName(), [], 'admin', lang)], 'admin', lang) }}<br>
{% if deeplink|trim is not empty %}
    <a href=\"{{ deeplink }}\" target=\"_blank\">{{ translator.trans('workflow_change_email_notification_deeplink', [], 'admin', lang) }}</a><br>
{% endif %}
<br>

{% if note_description|trim is not empty %}
    <strong>{{ translator.trans('workflow_change_email_notification_note', [], 'admin', lang) }}</strong>
    <p>{{ note_description }}</p>
{% endif %}
</body>
</html>", "@PimcoreCore/Workflow/NotificationEmail/notificationEmail.html.twig", "/var/www/iwapim/vendor/pimcore/pimcore/bundles/CoreBundle/templates/Workflow/NotificationEmail/notificationEmail.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 16);
        static $filters = array("escape" => 15, "trim" => 16);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape', 'trim'],
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
