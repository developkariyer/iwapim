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

/* @WebProfiler/Profiler/_command_summary.html.twig */
class __TwigTemplate_49b712e9a2e73ec902882a43b490f57d extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/_command_summary.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/_command_summary.html.twig"));

        // line 1
        $context["status_code"] = ((twig_get_attribute($this->env, $this->source, ($context["profile"] ?? null), "statuscode", [], "any", true, true, true, 1)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["profile"] ?? null), "statuscode", [], "any", false, false, true, 1), 1, $this->source), 0)) : (0));
        // line 2
        $context["interrupted"] = ((((isset($context["command_collector"]) || array_key_exists("command_collector", $context) ? $context["command_collector"] : (function () { throw new RuntimeError('Variable "command_collector" does not exist.', 2, $this->source); })()) === false)) ? (null) : (twig_get_attribute($this->env, $this->source, (isset($context["command_collector"]) || array_key_exists("command_collector", $context) ? $context["command_collector"] : (function () { throw new RuntimeError('Variable "command_collector" does not exist.', 2, $this->source); })()), "interruptedBySignal", [], "any", false, false, true, 2)));
        // line 3
        $context["css_class"] = (((((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 3, $this->source); })()) == 113) ||  !(null === (isset($context["interrupted"]) || array_key_exists("interrupted", $context) ? $context["interrupted"] : (function () { throw new RuntimeError('Variable "interrupted" does not exist.', 3, $this->source); })())))) ? ("status-warning") : (((((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 3, $this->source); })()) > 0)) ? ("status-error") : ("status-success"))));
        // line 4
        echo "
<div class=\"terminal status ";
        // line 5
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["css_class"]) || array_key_exists("css_class", $context) ? $context["css_class"] : (function () { throw new RuntimeError('Variable "css_class" does not exist.', 5, $this->source); })()), 5, $this->source), "html", null, true);
        echo "\">
    <div class=\"container\">
        <h2>
            <span class=\"status-request-method\">
                ";
        // line 9
        echo twig_escape_filter($this->env, twig_upper_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 9, $this->source); })()), "method", [], "any", false, false, true, 9), 9, $this->source)), "html", null, true);
        echo "
            </span>

            <span class=\"status-command\">
                ";
        // line 13
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 13, $this->source); })()), "url", [], "any", false, false, true, 13), 13, $this->source), "html", null, true);
        echo "
            </span>
        </h2>

        <dl class=\"metadata\">
            ";
        // line 18
        if ((isset($context["interrupted"]) || array_key_exists("interrupted", $context) ? $context["interrupted"] : (function () { throw new RuntimeError('Variable "interrupted" does not exist.', 18, $this->source); })())) {
            // line 19
            echo "                <span class=\"status-response-status-code\">Interrupted</span>
                <dt>Signal</dt>
                <dd class=\"status-response-status-text\">";
            // line 21
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["interrupted"]) || array_key_exists("interrupted", $context) ? $context["interrupted"] : (function () { throw new RuntimeError('Variable "interrupted" does not exist.', 21, $this->source); })()), 21, $this->source), "html", null, true);
            echo "</dd>

                <dt>Exit code</dt>
                <dd class=\"status-response-status-text\">";
            // line 24
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 24, $this->source); })()), 24, $this->source), "html", null, true);
            echo "</dd>
            ";
        } elseif ((        // line 25
(isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 25, $this->source); })()) == 0)) {
            // line 26
            echo "                <span class=\"status-response-status-code\">Success</span>
            ";
        } elseif ((        // line 27
(isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 27, $this->source); })()) > 0)) {
            // line 28
            echo "                <span class=\"status-response-status-code\">Error</span>
                <dt>Exit code</dt>
                <dd class=\"status-response-status-text\"><span class=\"status-response-status-code\">";
            // line 30
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 30, $this->source); })()), 30, $this->source), "html", null, true);
            echo "</span></dd>
            ";
        }
        // line 32
        echo "
            ";
        // line 33
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 33, $this->source); })()), "requestserver", [], "any", false, false, true, 33), "has", ["SYMFONY_CLI_BINARY_NAME"], "method", false, false, true, 33)) {
            // line 34
            echo "                <dt>Symfony CLI</dt>
                <dd>v";
            // line 35
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 35, $this->source); })()), "requestserver", [], "any", false, false, true, 35), "get", ["SYMFONY_CLI_VERSION"], "method", false, false, true, 35), 35, $this->source), "html", null, true);
            echo "</dd>
            ";
        }
        // line 37
        echo "
            <dt>Application</dt>
            <dd>
                <a href=\"";
        // line 40
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler_search_results", ["token" => (isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 40, $this->source); })()), "limit" => 10, "ip" => twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 40, $this->source); })()), "ip", [], "any", false, false, true, 40), "type" => "command"]), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 40, $this->source); })()), "ip", [], "any", false, false, true, 40), 40, $this->source), "html", null, true);
        echo "</a>
            </dd>

            <dt>Profiled on</dt>
            <dd><time data-convert-to-user-timezone data-render-as-datetime datetime=\"";
        // line 44
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 44, $this->source); })()), "time", [], "any", false, false, true, 44), 44, $this->source), "c"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 44, $this->source); })()), "time", [], "any", false, false, true, 44), 44, $this->source), "r"), "html", null, true);
        echo "</time></dd>

            <dt>Token</dt>
            <dd>";
        // line 47
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 47, $this->source); })()), "token", [], "any", false, false, true, 47), 47, $this->source), "html", null, true);
        echo "</dd>
        </dl>
    </div>
</div>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@WebProfiler/Profiler/_command_summary.html.twig";
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
        return array (  143 => 47,  135 => 44,  126 => 40,  121 => 37,  116 => 35,  113 => 34,  111 => 33,  108 => 32,  103 => 30,  99 => 28,  97 => 27,  94 => 26,  92 => 25,  88 => 24,  82 => 21,  78 => 19,  76 => 18,  68 => 13,  61 => 9,  54 => 5,  51 => 4,  49 => 3,  47 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% set status_code = profile.statuscode|default(0) %}
{% set interrupted = command_collector is same as false ? null : command_collector.interruptedBySignal %}
{% set css_class = status_code == 113 or interrupted is not null ? 'status-warning' : status_code > 0 ? 'status-error' : 'status-success' %}

<div class=\"terminal status {{ css_class }}\">
    <div class=\"container\">
        <h2>
            <span class=\"status-request-method\">
                {{ profile.method|upper }}
            </span>

            <span class=\"status-command\">
                {{ profile.url }}
            </span>
        </h2>

        <dl class=\"metadata\">
            {% if interrupted %}
                <span class=\"status-response-status-code\">Interrupted</span>
                <dt>Signal</dt>
                <dd class=\"status-response-status-text\">{{ interrupted }}</dd>

                <dt>Exit code</dt>
                <dd class=\"status-response-status-text\">{{ status_code }}</dd>
            {% elseif status_code == 0 %}
                <span class=\"status-response-status-code\">Success</span>
            {% elseif status_code > 0 %}
                <span class=\"status-response-status-code\">Error</span>
                <dt>Exit code</dt>
                <dd class=\"status-response-status-text\"><span class=\"status-response-status-code\">{{ status_code }}</span></dd>
            {% endif %}

            {% if request_collector.requestserver.has('SYMFONY_CLI_BINARY_NAME') %}
                <dt>Symfony CLI</dt>
                <dd>v{{ request_collector.requestserver.get('SYMFONY_CLI_VERSION') }}</dd>
            {% endif %}

            <dt>Application</dt>
            <dd>
                <a href=\"{{ path('_profiler_search_results', { token: token, limit: 10, ip: profile.ip, type: 'command' }) }}\">{{ profile.ip }}</a>
            </dd>

            <dt>Profiled on</dt>
            <dd><time data-convert-to-user-timezone data-render-as-datetime datetime=\"{{ profile.time|date('c') }}\">{{ profile.time|date('r') }}</time></dd>

            <dt>Token</dt>
            <dd>{{ profile.token }}</dd>
        </dl>
    </div>
</div>
", "@WebProfiler/Profiler/_command_summary.html.twig", "/var/www/iwapim/vendor/symfony/web-profiler-bundle/Resources/views/Profiler/_command_summary.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 1, "if" => 18);
        static $filters = array("default" => 1, "escape" => 5, "upper" => 9, "date" => 44);
        static $functions = array("path" => 40);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['default', 'escape', 'upper', 'date'],
                ['path']
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
