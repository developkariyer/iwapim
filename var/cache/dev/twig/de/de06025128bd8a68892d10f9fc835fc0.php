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

/* @WebProfiler/Profiler/_request_summary.html.twig */
class __TwigTemplate_7548bc4a21e4dda2fafa4995b7ce3e99 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/_request_summary.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/_request_summary.html.twig"));

        // line 1
        $context["status_code"] = (((isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 1, $this->source); })())) ? (((twig_get_attribute($this->env, $this->source, ($context["request_collector"] ?? null), "statuscode", [], "any", true, true, true, 1)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["request_collector"] ?? null), "statuscode", [], "any", false, false, true, 1), 1, $this->source), 0)) : (0))) : (0));
        // line 2
        $context["css_class"] = ((((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 2, $this->source); })()) > 399)) ? ("status-error") : (((((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 2, $this->source); })()) > 299)) ? ("status-warning") : ("status-success"))));
        // line 3
        echo "
";
        // line 4
        if (((isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 4, $this->source); })()) && twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 4, $this->source); })()), "redirect", [], "any", false, false, true, 4))) {
            // line 5
            echo "    ";
            $context["redirect"] = twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 5, $this->source); })()), "redirect", [], "any", false, false, true, 5);
            // line 6
            echo "    ";
            $context["link_to_source_code"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["redirect"] ?? null), "controller", [], "any", false, true, true, 6), "class", [], "any", true, true, true, 6)) ? ($this->extensions['Symfony\Bridge\Twig\Extension\CodeExtension']->getFileLink($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["redirect"]) || array_key_exists("redirect", $context) ? $context["redirect"] : (function () { throw new RuntimeError('Variable "redirect" does not exist.', 6, $this->source); })()), "controller", [], "any", false, false, true, 6), "file", [], "any", false, false, true, 6), 6, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["redirect"]) || array_key_exists("redirect", $context) ? $context["redirect"] : (function () { throw new RuntimeError('Variable "redirect" does not exist.', 6, $this->source); })()), "controller", [], "any", false, false, true, 6), "line", [], "any", false, false, true, 6), 6, $this->source))) : (""));
            // line 7
            echo "    ";
            $context["redirect_route_name"] = ("@" . $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["redirect"]) || array_key_exists("redirect", $context) ? $context["redirect"] : (function () { throw new RuntimeError('Variable "redirect" does not exist.', 7, $this->source); })()), "route", [], "any", false, false, true, 7), 7, $this->source));
            // line 8
            echo "
    <div class=\"status status-compact status-warning\">
        <span class=\"icon icon-redirect\">";
            // line 10
            echo twig_source($this->env, "@WebProfiler/Icon/redirect.svg");
            echo "</span>

        <span class=\"status-response-status-code\">";
            // line 12
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["redirect"]) || array_key_exists("redirect", $context) ? $context["redirect"] : (function () { throw new RuntimeError('Variable "redirect" does not exist.', 12, $this->source); })()), "status_code", [], "any", false, false, true, 12), 12, $this->source), "html", null, true);
            echo "</span> redirect from

        <span class=\"status-request-method\">";
            // line 14
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["redirect"]) || array_key_exists("redirect", $context) ? $context["redirect"] : (function () { throw new RuntimeError('Variable "redirect" does not exist.', 14, $this->source); })()), "method", [], "any", false, false, true, 14), 14, $this->source), "html", null, true);
            echo "</span>

        ";
            // line 16
            if ((isset($context["link_to_source_code"]) || array_key_exists("link_to_source_code", $context) ? $context["link_to_source_code"] : (function () { throw new RuntimeError('Variable "link_to_source_code" does not exist.', 16, $this->source); })())) {
                // line 17
                echo "            <a href=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["link_to_source_code"]) || array_key_exists("link_to_source_code", $context) ? $context["link_to_source_code"] : (function () { throw new RuntimeError('Variable "link_to_source_code" does not exist.', 17, $this->source); })()), 17, $this->source), "html", null, true);
                echo "\" title=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["redirect"]) || array_key_exists("redirect", $context) ? $context["redirect"] : (function () { throw new RuntimeError('Variable "redirect" does not exist.', 17, $this->source); })()), "controller", [], "any", false, false, true, 17), "file", [], "any", false, false, true, 17), 17, $this->source), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["redirect_route_name"]) || array_key_exists("redirect_route_name", $context) ? $context["redirect_route_name"] : (function () { throw new RuntimeError('Variable "redirect_route_name" does not exist.', 17, $this->source); })()), 17, $this->source), "html", null, true);
                echo "</a>
        ";
            } else {
                // line 19
                echo "            ";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["redirect_route_name"]) || array_key_exists("redirect_route_name", $context) ? $context["redirect_route_name"] : (function () { throw new RuntimeError('Variable "redirect_route_name" does not exist.', 19, $this->source); })()), 19, $this->source), "html", null, true);
                echo "
        ";
            }
            // line 21
            echo "
        (<a href=\"";
            // line 22
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler", ["token" => twig_get_attribute($this->env, $this->source, (isset($context["redirect"]) || array_key_exists("redirect", $context) ? $context["redirect"] : (function () { throw new RuntimeError('Variable "redirect" does not exist.', 22, $this->source); })()), "token", [], "any", false, false, true, 22), "panel" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["request"]) || array_key_exists("request", $context) ? $context["request"] : (function () { throw new RuntimeError('Variable "request" does not exist.', 22, $this->source); })()), "query", [], "any", false, false, true, 22), "get", ["panel", "request"], "method", false, false, true, 22)]), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["redirect"]) || array_key_exists("redirect", $context) ? $context["redirect"] : (function () { throw new RuntimeError('Variable "redirect" does not exist.', 22, $this->source); })()), "token", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
            echo "</a>)
    </div>
";
        }
        // line 25
        echo "
<div class=\"status ";
        // line 26
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["css_class"]) || array_key_exists("css_class", $context) ? $context["css_class"] : (function () { throw new RuntimeError('Variable "css_class" does not exist.', 26, $this->source); })()), 26, $this->source), "html", null, true);
        echo "\">
    ";
        // line 27
        if (((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 27, $this->source); })()) > 399)) {
            // line 28
            echo "        <p class=\"status-error-details\">
            <span class=\"icon\">";
            // line 29
            echo twig_source($this->env, "@WebProfiler/Icon/alert-circle.svg");
            echo "</span>
            <span class=\"status-response-status-code\">Error ";
            // line 30
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 30, $this->source); })()), 30, $this->source), "html", null, true);
            echo "</span>
            <span class=\"status-response-status-text\">";
            // line 31
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 31, $this->source); })()), "statusText", [], "any", false, false, true, 31), 31, $this->source), "html", null, true);
            echo "</span>
        </p>
    ";
        }
        // line 34
        echo "
    <h2>
        <span class=\"status-request-method\">
            ";
        // line 37
        echo twig_escape_filter($this->env, twig_upper_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 37, $this->source); })()), "method", [], "any", false, false, true, 37), 37, $this->source)), "html", null, true);
        echo "
        </span>

        ";
        // line 40
        $context["profile_title"] = (((twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 40, $this->source); })()), "url", [], "any", false, false, true, 40), 40, $this->source)) < 160)) ? (twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 40, $this->source); })()), "url", [], "any", false, false, true, 40)) : ((twig_slice($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 40, $this->source); })()), "url", [], "any", false, false, true, 40), 40, $this->source), 0, 160) . "…")));
        // line 41
        echo "        ";
        if (twig_in_filter(twig_upper_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 41, $this->source); })()), "method", [], "any", false, false, true, 41)), ["GET", "HEAD"])) {
            // line 42
            echo "            <a href=\"";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 42, $this->source); })()), "url", [], "any", false, false, true, 42), 42, $this->source), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_title"]) || array_key_exists("profile_title", $context) ? $context["profile_title"] : (function () { throw new RuntimeError('Variable "profile_title" does not exist.', 42, $this->source); })()), 42, $this->source), "html", null, true);
            echo "</a>
        ";
        } else {
            // line 44
            echo "            ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_title"]) || array_key_exists("profile_title", $context) ? $context["profile_title"] : (function () { throw new RuntimeError('Variable "profile_title" does not exist.', 44, $this->source); })()), 44, $this->source), "html", null, true);
            echo "
        ";
        }
        // line 46
        echo "    </h2>

    <dl class=\"metadata\">
        ";
        // line 49
        if (((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 49, $this->source); })()) < 400)) {
            // line 50
            echo "            <dt>Response</dt>
            <dd>
                <span class=\"status-response-status-code\">";
            // line 52
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 52, $this->source); })()), 52, $this->source), "html", null, true);
            echo "</span>
                <span class=\"status-response-status-text\">";
            // line 53
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 53, $this->source); })()), "statusText", [], "any", false, false, true, 53), 53, $this->source), "html", null, true);
            echo "</span>
            </dd>
        ";
        }
        // line 56
        echo "
        ";
        // line 57
        $context["referer"] = (((isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 57, $this->source); })())) ? (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 57, $this->source); })()), "requestheaders", [], "any", false, false, true, 57), "get", ["referer"], "method", false, false, true, 57)) : (null));
        // line 58
        echo "        ";
        if ((isset($context["referer"]) || array_key_exists("referer", $context) ? $context["referer"] : (function () { throw new RuntimeError('Variable "referer" does not exist.', 58, $this->source); })())) {
            // line 59
            echo "            <dt></dt>
            <dd>
                <span class=\"icon icon-referer\">";
            // line 61
            echo twig_source($this->env, "@WebProfiler/Icon/referrer.svg");
            echo "</span>
                <a href=\"";
            // line 62
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["referer"]) || array_key_exists("referer", $context) ? $context["referer"] : (function () { throw new RuntimeError('Variable "referer" does not exist.', 62, $this->source); })()), 62, $this->source), "html", null, true);
            echo "\" class=\"referer\">Browse referrer URL</a>
            </dd>
        ";
        }
        // line 65
        echo "
        <dt>IP</dt>
        <dd>
            <a href=\"";
        // line 68
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler_search_results", ["token" => (isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 68, $this->source); })()), "limit" => 10, "ip" => twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 68, $this->source); })()), "ip", [], "any", false, false, true, 68)]), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 68, $this->source); })()), "ip", [], "any", false, false, true, 68), 68, $this->source), "html", null, true);
        echo "</a>
        </dd>

        <dt>Profiled on</dt>
        <dd><time data-convert-to-user-timezone data-render-as-datetime datetime=\"";
        // line 72
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 72, $this->source); })()), "time", [], "any", false, false, true, 72), 72, $this->source), "c"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 72, $this->source); })()), "time", [], "any", false, false, true, 72), 72, $this->source), "r"), "html", null, true);
        echo "</time></dd>

        <dt>Token</dt>
        <dd>";
        // line 75
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 75, $this->source); })()), "token", [], "any", false, false, true, 75), 75, $this->source), "html", null, true);
        echo "</dd>
    </dl>
</div>

";
        // line 79
        if (((isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 79, $this->source); })()) && twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 79, $this->source); })()), "forwardtoken", [], "any", false, false, true, 79))) {
            // line 80
            $context["forward_profile"] = twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 80, $this->source); })()), "childByToken", [twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 80, $this->source); })()), "forwardtoken", [], "any", false, false, true, 80)], "method", false, false, true, 80);
            // line 81
            echo "    ";
            $context["controller"] = (((isset($context["forward_profile"]) || array_key_exists("forward_profile", $context) ? $context["forward_profile"] : (function () { throw new RuntimeError('Variable "forward_profile" does not exist.', 81, $this->source); })())) ? (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["forward_profile"]) || array_key_exists("forward_profile", $context) ? $context["forward_profile"] : (function () { throw new RuntimeError('Variable "forward_profile" does not exist.', 81, $this->source); })()), "collector", ["request"], "method", false, false, true, 81), "controller", [], "any", false, false, true, 81)) : ("n/a"));
            // line 82
            echo "    <div class=\"status status-compact status-compact-forward\">
        <span class=\"icon icon-forward\">";
            // line 83
            echo twig_source($this->env, "@WebProfiler/Icon/forward.svg");
            echo "</span>

        Forwarded to

        ";
            // line 87
            $context["link"] = ((twig_get_attribute($this->env, $this->source, ($context["controller"] ?? null), "file", [], "any", true, true, true, 87)) ? ($this->extensions['Symfony\Bridge\Twig\Extension\CodeExtension']->getFileLink($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["controller"]) || array_key_exists("controller", $context) ? $context["controller"] : (function () { throw new RuntimeError('Variable "controller" does not exist.', 87, $this->source); })()), "file", [], "any", false, false, true, 87), 87, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["controller"]) || array_key_exists("controller", $context) ? $context["controller"] : (function () { throw new RuntimeError('Variable "controller" does not exist.', 87, $this->source); })()), "line", [], "any", false, false, true, 87), 87, $this->source))) : (null));
            // line 88
            if ((isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 88, $this->source); })())) {
                echo "<a href=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 88, $this->source); })()), 88, $this->source), "html", null, true);
                echo "\" title=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["controller"]) || array_key_exists("controller", $context) ? $context["controller"] : (function () { throw new RuntimeError('Variable "controller" does not exist.', 88, $this->source); })()), "file", [], "any", false, false, true, 88), 88, $this->source), "html", null, true);
                echo "\">";
            }
            // line 89
            if (twig_get_attribute($this->env, $this->source, ($context["controller"] ?? null), "class", [], "any", true, true, true, 89)) {
                // line 90
                echo twig_escape_filter($this->env, twig_striptags($this->extensions['Symfony\Bridge\Twig\Extension\CodeExtension']->abbrClass(twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["controller"]) || array_key_exists("controller", $context) ? $context["controller"] : (function () { throw new RuntimeError('Variable "controller" does not exist.', 90, $this->source); })()), "class", [], "any", false, false, true, 90), 90, $this->source), "html", null, true))), "html", null, true);
                // line 91
                ((twig_get_attribute($this->env, $this->source, (isset($context["controller"]) || array_key_exists("controller", $context) ? $context["controller"] : (function () { throw new RuntimeError('Variable "controller" does not exist.', 91, $this->source); })()), "method", [], "any", false, false, true, 91)) ? (print (twig_escape_filter($this->env, (" :: " . twig_get_attribute($this->env, $this->source, (isset($context["controller"]) || array_key_exists("controller", $context) ? $context["controller"] : (function () { throw new RuntimeError('Variable "controller" does not exist.', 91, $this->source); })()), "method", [], "any", false, false, true, 91)), "html", null, true))) : (print ("")));
            } else {
                // line 93
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["controller"]) || array_key_exists("controller", $context) ? $context["controller"] : (function () { throw new RuntimeError('Variable "controller" does not exist.', 93, $this->source); })()), 93, $this->source), "html", null, true);
            }
            // line 95
            if ((isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 95, $this->source); })())) {
                echo "</a>";
            }
            // line 96
            echo "        (<a href=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler", ["token" => twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 96, $this->source); })()), "forwardtoken", [], "any", false, false, true, 96)]), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 96, $this->source); })()), "forwardtoken", [], "any", false, false, true, 96), 96, $this->source), "html", null, true);
            echo "</a>)

    </div>";
        }
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@WebProfiler/Profiler/_request_summary.html.twig";
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
        return array (  280 => 96,  276 => 95,  273 => 93,  270 => 91,  268 => 90,  266 => 89,  258 => 88,  256 => 87,  249 => 83,  246 => 82,  243 => 81,  241 => 80,  239 => 79,  232 => 75,  224 => 72,  215 => 68,  210 => 65,  204 => 62,  200 => 61,  196 => 59,  193 => 58,  191 => 57,  188 => 56,  182 => 53,  178 => 52,  174 => 50,  172 => 49,  167 => 46,  161 => 44,  153 => 42,  150 => 41,  148 => 40,  142 => 37,  137 => 34,  131 => 31,  127 => 30,  123 => 29,  120 => 28,  118 => 27,  114 => 26,  111 => 25,  103 => 22,  100 => 21,  94 => 19,  84 => 17,  82 => 16,  77 => 14,  72 => 12,  67 => 10,  63 => 8,  60 => 7,  57 => 6,  54 => 5,  52 => 4,  49 => 3,  47 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% set status_code = request_collector ? request_collector.statuscode|default(0) : 0 %}
{% set css_class = status_code > 399 ? 'status-error' : status_code > 299 ? 'status-warning' : 'status-success' %}

{% if request_collector and request_collector.redirect %}
    {% set redirect = request_collector.redirect %}
    {% set link_to_source_code = redirect.controller.class is defined ? redirect.controller.file|file_link(redirect.controller.line) %}
    {% set redirect_route_name = '@' ~ redirect.route %}

    <div class=\"status status-compact status-warning\">
        <span class=\"icon icon-redirect\">{{ source('@WebProfiler/Icon/redirect.svg') }}</span>

        <span class=\"status-response-status-code\">{{ redirect.status_code }}</span> redirect from

        <span class=\"status-request-method\">{{ redirect.method }}</span>

        {% if link_to_source_code %}
            <a href=\"{{ link_to_source_code }}\" title=\"{{ redirect.controller.file }}\">{{ redirect_route_name }}</a>
        {% else %}
            {{ redirect_route_name }}
        {% endif %}

        (<a href=\"{{ path('_profiler', { token: redirect.token, panel: request.query.get('panel', 'request') }) }}\">{{ redirect.token }}</a>)
    </div>
{% endif %}

<div class=\"status {{ css_class }}\">
    {% if status_code > 399 %}
        <p class=\"status-error-details\">
            <span class=\"icon\">{{ source('@WebProfiler/Icon/alert-circle.svg') }}</span>
            <span class=\"status-response-status-code\">Error {{ status_code }}</span>
            <span class=\"status-response-status-text\">{{ request_collector.statusText }}</span>
        </p>
    {% endif %}

    <h2>
        <span class=\"status-request-method\">
            {{ profile.method|upper }}
        </span>

        {% set profile_title = profile.url|length < 160 ? profile.url : profile.url[:160] ~ '…' %}
        {% if profile.method|upper in ['GET', 'HEAD'] %}
            <a href=\"{{ profile.url }}\">{{ profile_title }}</a>
        {% else %}
            {{ profile_title }}
        {% endif %}
    </h2>

    <dl class=\"metadata\">
        {% if status_code < 400 %}
            <dt>Response</dt>
            <dd>
                <span class=\"status-response-status-code\">{{ status_code }}</span>
                <span class=\"status-response-status-text\">{{ request_collector.statusText }}</span>
            </dd>
        {% endif %}

        {% set referer = request_collector ? request_collector.requestheaders.get('referer') : null %}
        {% if referer %}
            <dt></dt>
            <dd>
                <span class=\"icon icon-referer\">{{ source('@WebProfiler/Icon/referrer.svg') }}</span>
                <a href=\"{{ referer }}\" class=\"referer\">Browse referrer URL</a>
            </dd>
        {% endif %}

        <dt>IP</dt>
        <dd>
            <a href=\"{{ path('_profiler_search_results', { token: token, limit: 10, ip: profile.ip }) }}\">{{ profile.ip }}</a>
        </dd>

        <dt>Profiled on</dt>
        <dd><time data-convert-to-user-timezone data-render-as-datetime datetime=\"{{ profile.time|date('c') }}\">{{ profile.time|date('r') }}</time></dd>

        <dt>Token</dt>
        <dd>{{ profile.token }}</dd>
    </dl>
</div>

{% if request_collector and request_collector.forwardtoken -%}
    {% set forward_profile = profile.childByToken(request_collector.forwardtoken) %}
    {% set controller = forward_profile ? forward_profile.collector('request').controller : 'n/a' %}
    <div class=\"status status-compact status-compact-forward\">
        <span class=\"icon icon-forward\">{{ source('@WebProfiler/Icon/forward.svg') }}</span>

        Forwarded to

        {% set link = controller.file is defined ? controller.file|file_link(controller.line) : null -%}
        {%- if link %}<a href=\"{{ link }}\" title=\"{{ controller.file }}\">{% endif -%}
            {% if controller.class is defined %}
                {{- controller.class|abbr_class|striptags -}}
                {{- controller.method ? ' :: ' ~ controller.method -}}
            {% else %}
                {{- controller -}}
            {% endif %}
            {%- if link %}</a>{% endif %}
        (<a href=\"{{ path('_profiler', { token: request_collector.forwardtoken }) }}\">{{ request_collector.forwardtoken }}</a>)

    </div>
{%- endif %}
", "@WebProfiler/Profiler/_request_summary.html.twig", "/var/www/iwapim/vendor/symfony/web-profiler-bundle/Resources/views/Profiler/_request_summary.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 1, "if" => 4);
        static $filters = array("default" => 1, "file_link" => 6, "escape" => 12, "upper" => 37, "length" => 40, "slice" => 40, "date" => 72, "striptags" => 90, "abbr_class" => 90);
        static $functions = array("source" => 10, "path" => 22);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['default', 'file_link', 'escape', 'upper', 'length', 'slice', 'date', 'striptags', 'abbr_class'],
                ['source', 'path']
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
