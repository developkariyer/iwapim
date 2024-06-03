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

/* @WebProfiler/Collector/http_client.html.twig */
class __TwigTemplate_5dbaaabfc03b4558b1b34c7d6f294a5e extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'head' => [$this, 'block_head'],
            'toolbar' => [$this, 'block_toolbar'],
            'menu' => [$this, 'block_menu'],
            'panel' => [$this, 'block_panel'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/http_client.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/http_client.html.twig"));

        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/http_client.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_head($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "head"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "head"));

        // line 4
        echo "    ";
        $this->displayParentBlock("head", $context, $blocks);
        echo "

    <style>
        .sf-profiler-httpclient-requests thead th {
            vertical-align: top;
        }
        .sf-profiler-httpclient-requests .http-method {
            border: 1px solid var(--header-status-request-method-color);
            border-radius: 5px;
            color: var(--header-status-request-method-color);
            display: inline-block;
            font-weight: 500;
            line-height: 1;
            margin-right: 6px;
            padding: 2px 4px;
            text-align: center;
            white-space: nowrap;
        }
        .sf-profiler-httpclient-requests .status-response-status-code {
            background: var(--gray-600);
            border-radius: 4px;
            color: var(--white);
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
            padding: 1px 3px;
        }
        .sf-profiler-httpclient-requests .status-response-status-code.status-success { background: var(--header-success-status-code-background); color: var(--header-success-status-code-color); }
        .sf-profiler-httpclient-requests .status-response-status-code.status-warning { background: var(--header-warning-status-code-background); color: var(--header-warning-status-code-color); }
        .sf-profiler-httpclient-requests .status-response-status-code.status-error { background: var(--header-error-status-code-background); color: var(--header-error-status-code-color); }
    </style>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 39
    public function block_toolbar($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "toolbar"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "toolbar"));

        // line 40
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 40, $this->source); })()), "requestCount", [], "any", false, false, true, 40)) {
            // line 41
            echo "        ";
            ob_start();
            // line 42
            echo "            ";
            echo twig_source($this->env, "@WebProfiler/Icon/http-client.svg");
            echo "
            ";
            // line 43
            $context["status_color"] = "";
            // line 44
            echo "            <span class=\"sf-toolbar-value\">";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 44, $this->source); })()), "requestCount", [], "any", false, false, true, 44), 44, $this->source), "html", null, true);
            echo "</span>
        ";
            $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
            // line 46
            echo "
        ";
            // line 47
            ob_start();
            // line 48
            echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Total requests</b>
                <span>";
            // line 50
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 50, $this->source); })()), "requestCount", [], "any", false, false, true, 50), 50, $this->source), "html", null, true);
            echo "</span>
            </div>
            <div class=\"sf-toolbar-info-piece\">
                <b>HTTP errors</b>
                <span class=\"sf-toolbar-status ";
            // line 54
            echo (((twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 54, $this->source); })()), "errorCount", [], "any", false, false, true, 54) > 0)) ? ("sf-toolbar-status-red") : (""));
            echo "\">";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 54, $this->source); })()), "errorCount", [], "any", false, false, true, 54), 54, $this->source), "html", null, true);
            echo "</span>
            </div>
        ";
            $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
            // line 57
            echo "
        ";
            // line 58
            echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", ["link" => (isset($context["profiler_url"]) || array_key_exists("profiler_url", $context) ? $context["profiler_url"] : (function () { throw new RuntimeError('Variable "profiler_url" does not exist.', 58, $this->source); })()), "status" => (isset($context["status_color"]) || array_key_exists("status_color", $context) ? $context["status_color"] : (function () { throw new RuntimeError('Variable "status_color" does not exist.', 58, $this->source); })())]);
            echo "
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 62
    public function block_menu($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        // line 63
        echo "<span class=\"label ";
        echo (((twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 63, $this->source); })()), "requestCount", [], "any", false, false, true, 63) == 0)) ? ("disabled") : (""));
        echo "\">
    <span class=\"icon\">";
        // line 64
        echo twig_source($this->env, "@WebProfiler/Icon/http-client.svg");
        echo "</span>
    <strong>HTTP Client</strong>
    ";
        // line 66
        if (twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 66, $this->source); })()), "requestCount", [], "any", false, false, true, 66)) {
            // line 67
            echo "        <span class=\"count\">
            ";
            // line 68
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 68, $this->source); })()), "requestCount", [], "any", false, false, true, 68), 68, $this->source), "html", null, true);
            echo "
        </span>
    ";
        }
        // line 71
        echo "</span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 74
    public function block_panel($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        // line 75
        echo "    <h2>HTTP Client</h2>
    ";
        // line 76
        if ((twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 76, $this->source); })()), "requestCount", [], "any", false, false, true, 76) == 0)) {
            // line 77
            echo "        <div class=\"empty empty-panel\">
            <p>No HTTP requests were made.</p>
        </div>
    ";
        } else {
            // line 81
            echo "        <div class=\"metrics\">
            <div class=\"metric\">
                <span class=\"value\">";
            // line 83
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 83, $this->source); })()), "requestCount", [], "any", false, false, true, 83), 83, $this->source), "html", null, true);
            echo "</span>
                <span class=\"label\">Total requests</span>
            </div>
            <div class=\"metric\">
                <span class=\"value\">";
            // line 87
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 87, $this->source); })()), "errorCount", [], "any", false, false, true, 87), 87, $this->source), "html", null, true);
            echo "</span>
                <span class=\"label\">HTTP errors</span>
            </div>
        </div>
        <h2>Clients</h2>
        <div class=\"sf-tabs\">
            ";
            // line 93
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 93, $this->source); })()), "clients", [], "any", false, false, true, 93));
            foreach ($context['_seq'] as $context["name"] => $context["client"]) {
                // line 94
                echo "                <div class=\"tab ";
                echo (((twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["client"], "traces", [], "any", false, false, true, 94), 94, $this->source)) == 0)) ? ("disabled") : (""));
                echo "\">
                    <h3 class=\"tab-title\">";
                // line 95
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["name"], 95, $this->source), "html", null, true);
                echo " <span class=\"badge\">";
                echo twig_escape_filter($this->env, twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["client"], "traces", [], "any", false, false, true, 95), 95, $this->source)), "html", null, true);
                echo "</span></h3>
                    <div class=\"tab-content\">
                        ";
                // line 97
                if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, $context["client"], "traces", [], "any", false, false, true, 97)) == 0)) {
                    // line 98
                    echo "                            <div class=\"empty\">
                                <p>No requests were made with the \"";
                    // line 99
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["name"], 99, $this->source), "html", null, true);
                    echo "\" service.</p>
                            </div>
                        ";
                } else {
                    // line 102
                    echo "                            <h4>Requests</h4>
                            ";
                    // line 103
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["client"], "traces", [], "any", false, false, true, 103));
                    foreach ($context['_seq'] as $context["_key"] => $context["trace"]) {
                        // line 104
                        echo "                                ";
                        $context["profiler_token"] = "";
                        // line 105
                        echo "                                ";
                        $context["profiler_link"] = "";
                        // line 106
                        echo "                                ";
                        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["trace"], "info", [], "any", false, true, true, 106), "response_headers", [], "any", true, true, true, 106)) {
                            // line 107
                            echo "                                    ";
                            $context['_parent'] = $context;
                            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["trace"], "info", [], "any", false, false, true, 107), "response_headers", [], "any", false, false, true, 107));
                            foreach ($context['_seq'] as $context["_key"] => $context["header"]) {
                                // line 108
                                echo "                                        ";
                                if (twig_matches("/^x-debug-token: .*\$/i", $context["header"])) {
                                    // line 109
                                    echo "                                            ";
                                    $context["profiler_token"] = twig_slice($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["header"], "getValue", [], "any", false, false, true, 109), 109, $this->source), twig_length_filter($this->env, "x-debug-token: "));
                                    // line 110
                                    echo "                                        ";
                                }
                                // line 111
                                echo "                                        ";
                                if (twig_matches("/^x-debug-token-link: .*\$/i", $context["header"])) {
                                    // line 112
                                    echo "                                            ";
                                    $context["profiler_link"] = twig_slice($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["header"], "getValue", [], "any", false, false, true, 112), 112, $this->source), twig_length_filter($this->env, "x-debug-token-link: "));
                                    // line 113
                                    echo "                                        ";
                                }
                                // line 114
                                echo "                                    ";
                            }
                            $_parent = $context['_parent'];
                            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['header'], $context['_parent'], $context['loop']);
                            $context = array_intersect_key($context, $_parent) + $_parent;
                            // line 115
                            echo "                                ";
                        }
                        // line 116
                        echo "                                <table class=\"sf-profiler-httpclient-requests\">
                                    <thead>
                                    <tr>
                                        <th>
                                            <span class=\"http-method\">";
                        // line 120
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["trace"], "method", [], "any", false, false, true, 120), 120, $this->source), "html", null, true);
                        echo "</span>
                                        </th>
                                        <th class=\"full-width\">
                                            ";
                        // line 123
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["trace"], "url", [], "any", false, false, true, 123), 123, $this->source), "html", null, true);
                        echo "
                                        </th>
                                        ";
                        // line 125
                        if (((isset($context["profiler_token"]) || array_key_exists("profiler_token", $context) ? $context["profiler_token"] : (function () { throw new RuntimeError('Variable "profiler_token" does not exist.', 125, $this->source); })()) && (isset($context["profiler_link"]) || array_key_exists("profiler_link", $context) ? $context["profiler_link"] : (function () { throw new RuntimeError('Variable "profiler_link" does not exist.', 125, $this->source); })()))) {
                            // line 126
                            echo "                                            <th>
                                                Profile
                                            </th>
                                        ";
                        }
                        // line 130
                        echo "                                        ";
                        if ((twig_get_attribute($this->env, $this->source, $context["trace"], "curlCommand", [], "any", true, true, true, 130) && twig_get_attribute($this->env, $this->source, $context["trace"], "curlCommand", [], "any", false, false, true, 130))) {
                            // line 131
                            echo "                                            <th>
                                                <button class=\"btn btn-sm hidden\" title=\"Copy as cURL\" data-clipboard-text=\"";
                            // line 132
                            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["trace"], "curlCommand", [], "any", false, false, true, 132), 132, $this->source), "html", null, true);
                            echo "\">Copy as cURL</button>
                                            </th>
                                        ";
                        }
                        // line 135
                        echo "                                    </tr>
                                    </thead>
                                    <tbody>
                                    ";
                        // line 138
                        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, $context["trace"], "options", [], "any", false, false, true, 138))) {
                            // line 139
                            echo "                                        <tr>
                                            <th class=\"font-normal\">Request options</th>
                                            <td>";
                            // line 141
                            echo $this->extensions['Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension']->dumpData($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["trace"], "options", [], "any", false, false, true, 141), 141, $this->source), 1);
                            echo "</td>
                                        </tr>
                                    ";
                        }
                        // line 144
                        echo "                                    <tr>
                                        <th class=\"font-normal\">Response</th>
                                        <td";
                        // line 146
                        if ((twig_get_attribute($this->env, $this->source, $context["trace"], "curlCommand", [], "any", true, true, true, 146) && twig_get_attribute($this->env, $this->source, $context["trace"], "curlCommand", [], "any", false, false, true, 146))) {
                            echo " colspan=\"2\"";
                        }
                        echo ">
                                            ";
                        // line 147
                        if ((twig_get_attribute($this->env, $this->source, $context["trace"], "http_code", [], "any", false, false, true, 147) >= 500)) {
                            // line 148
                            echo "                                                ";
                            $context["responseStatus"] = "error";
                            // line 149
                            echo "                                            ";
                        } elseif ((twig_get_attribute($this->env, $this->source, $context["trace"], "http_code", [], "any", false, false, true, 149) >= 400)) {
                            // line 150
                            echo "                                                ";
                            $context["responseStatus"] = "warning";
                            // line 151
                            echo "                                            ";
                        } else {
                            // line 152
                            echo "                                                ";
                            $context["responseStatus"] = "success";
                            // line 153
                            echo "                                            ";
                        }
                        // line 154
                        echo "                                            <span class=\"font-normal status-response-status-code status-";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["responseStatus"]) || array_key_exists("responseStatus", $context) ? $context["responseStatus"] : (function () { throw new RuntimeError('Variable "responseStatus" does not exist.', 154, $this->source); })()), 154, $this->source), "html", null, true);
                        echo "\">
                                                ";
                        // line 155
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["trace"], "http_code", [], "any", false, false, true, 155), 155, $this->source), "html", null, true);
                        echo "
                                            </span>

                                            ";
                        // line 158
                        echo $this->extensions['Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension']->dumpData($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["trace"], "info", [], "any", false, false, true, 158), 158, $this->source), 1);
                        echo "
                                        </td>
                                        ";
                        // line 160
                        if (((isset($context["profiler_token"]) || array_key_exists("profiler_token", $context) ? $context["profiler_token"] : (function () { throw new RuntimeError('Variable "profiler_token" does not exist.', 160, $this->source); })()) && (isset($context["profiler_link"]) || array_key_exists("profiler_link", $context) ? $context["profiler_link"] : (function () { throw new RuntimeError('Variable "profiler_link" does not exist.', 160, $this->source); })()))) {
                            // line 161
                            echo "                                            <td>
                                                <span><a href=\"";
                            // line 162
                            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profiler_link"]) || array_key_exists("profiler_link", $context) ? $context["profiler_link"] : (function () { throw new RuntimeError('Variable "profiler_link" does not exist.', 162, $this->source); })()), 162, $this->source), "html", null, true);
                            echo "\" target=\"_blank\">";
                            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profiler_token"]) || array_key_exists("profiler_token", $context) ? $context["profiler_token"] : (function () { throw new RuntimeError('Variable "profiler_token" does not exist.', 162, $this->source); })()), 162, $this->source), "html", null, true);
                            echo "</a></span>
                                            </td>
                                        ";
                        }
                        // line 165
                        echo "                                    </tr>
                                    </tbody>
                                </table>
                            ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['trace'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 169
                    echo "                        ";
                }
                // line 170
                echo "                    </div>
                </div>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['client'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 173
            echo "        ";
        }
        // line 174
        echo "    </div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@WebProfiler/Collector/http_client.html.twig";
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
        return array (  480 => 174,  477 => 173,  469 => 170,  466 => 169,  457 => 165,  449 => 162,  446 => 161,  444 => 160,  439 => 158,  433 => 155,  428 => 154,  425 => 153,  422 => 152,  419 => 151,  416 => 150,  413 => 149,  410 => 148,  408 => 147,  402 => 146,  398 => 144,  392 => 141,  388 => 139,  386 => 138,  381 => 135,  375 => 132,  372 => 131,  369 => 130,  363 => 126,  361 => 125,  356 => 123,  350 => 120,  344 => 116,  341 => 115,  335 => 114,  332 => 113,  329 => 112,  326 => 111,  323 => 110,  320 => 109,  317 => 108,  312 => 107,  309 => 106,  306 => 105,  303 => 104,  299 => 103,  296 => 102,  290 => 99,  287 => 98,  285 => 97,  278 => 95,  273 => 94,  269 => 93,  260 => 87,  253 => 83,  249 => 81,  243 => 77,  241 => 76,  238 => 75,  228 => 74,  217 => 71,  211 => 68,  208 => 67,  206 => 66,  201 => 64,  196 => 63,  186 => 62,  173 => 58,  170 => 57,  162 => 54,  155 => 50,  151 => 48,  149 => 47,  146 => 46,  140 => 44,  138 => 43,  133 => 42,  130 => 41,  127 => 40,  117 => 39,  73 => 4,  63 => 3,  40 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block head %}
    {{ parent() }}

    <style>
        .sf-profiler-httpclient-requests thead th {
            vertical-align: top;
        }
        .sf-profiler-httpclient-requests .http-method {
            border: 1px solid var(--header-status-request-method-color);
            border-radius: 5px;
            color: var(--header-status-request-method-color);
            display: inline-block;
            font-weight: 500;
            line-height: 1;
            margin-right: 6px;
            padding: 2px 4px;
            text-align: center;
            white-space: nowrap;
        }
        .sf-profiler-httpclient-requests .status-response-status-code {
            background: var(--gray-600);
            border-radius: 4px;
            color: var(--white);
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
            padding: 1px 3px;
        }
        .sf-profiler-httpclient-requests .status-response-status-code.status-success { background: var(--header-success-status-code-background); color: var(--header-success-status-code-color); }
        .sf-profiler-httpclient-requests .status-response-status-code.status-warning { background: var(--header-warning-status-code-background); color: var(--header-warning-status-code-color); }
        .sf-profiler-httpclient-requests .status-response-status-code.status-error { background: var(--header-error-status-code-background); color: var(--header-error-status-code-color); }
    </style>
{% endblock %}


{% block toolbar %}
    {% if collector.requestCount %}
        {% set icon %}
            {{ source('@WebProfiler/Icon/http-client.svg') }}
            {% set status_color = '' %}
            <span class=\"sf-toolbar-value\">{{ collector.requestCount }}</span>
        {% endset %}

        {% set text %}
            <div class=\"sf-toolbar-info-piece\">
                <b>Total requests</b>
                <span>{{ collector.requestCount }}</span>
            </div>
            <div class=\"sf-toolbar-info-piece\">
                <b>HTTP errors</b>
                <span class=\"sf-toolbar-status {{ collector.errorCount > 0 ? 'sf-toolbar-status-red' }}\">{{ collector.errorCount }}</span>
            </div>
        {% endset %}

        {{ include('@WebProfiler/Profiler/toolbar_item.html.twig', { link: profiler_url, status: status_color }) }}
    {% endif %}
{% endblock %}

{% block menu %}
<span class=\"label {{ collector.requestCount == 0 ? 'disabled' }}\">
    <span class=\"icon\">{{ source('@WebProfiler/Icon/http-client.svg') }}</span>
    <strong>HTTP Client</strong>
    {% if collector.requestCount %}
        <span class=\"count\">
            {{ collector.requestCount }}
        </span>
    {% endif %}
</span>
{% endblock %}

{% block panel %}
    <h2>HTTP Client</h2>
    {% if collector.requestCount == 0 %}
        <div class=\"empty empty-panel\">
            <p>No HTTP requests were made.</p>
        </div>
    {% else %}
        <div class=\"metrics\">
            <div class=\"metric\">
                <span class=\"value\">{{ collector.requestCount }}</span>
                <span class=\"label\">Total requests</span>
            </div>
            <div class=\"metric\">
                <span class=\"value\">{{ collector.errorCount }}</span>
                <span class=\"label\">HTTP errors</span>
            </div>
        </div>
        <h2>Clients</h2>
        <div class=\"sf-tabs\">
            {% for name, client in collector.clients %}
                <div class=\"tab {{ client.traces|length == 0 ? 'disabled' }}\">
                    <h3 class=\"tab-title\">{{ name }} <span class=\"badge\">{{ client.traces|length }}</span></h3>
                    <div class=\"tab-content\">
                        {% if client.traces|length == 0 %}
                            <div class=\"empty\">
                                <p>No requests were made with the \"{{ name }}\" service.</p>
                            </div>
                        {% else %}
                            <h4>Requests</h4>
                            {% for trace in client.traces %}
                                {% set profiler_token = '' %}
                                {% set profiler_link = '' %}
                                {% if trace.info.response_headers is defined %}
                                    {% for header in trace.info.response_headers %}
                                        {% if header matches '/^x-debug-token: .*\$/i' %}
                                            {% set profiler_token = (header.getValue | slice('x-debug-token: ' | length)) %}
                                        {% endif %}
                                        {% if header matches '/^x-debug-token-link: .*\$/i' %}
                                            {% set profiler_link = (header.getValue | slice('x-debug-token-link: ' | length)) %}
                                        {% endif %}
                                    {% endfor %}
                                {% endif %}
                                <table class=\"sf-profiler-httpclient-requests\">
                                    <thead>
                                    <tr>
                                        <th>
                                            <span class=\"http-method\">{{ trace.method }}</span>
                                        </th>
                                        <th class=\"full-width\">
                                            {{ trace.url }}
                                        </th>
                                        {% if profiler_token and profiler_link %}
                                            <th>
                                                Profile
                                            </th>
                                        {% endif %}
                                        {% if trace.curlCommand is defined and trace.curlCommand %}
                                            <th>
                                                <button class=\"btn btn-sm hidden\" title=\"Copy as cURL\" data-clipboard-text=\"{{ trace.curlCommand }}\">Copy as cURL</button>
                                            </th>
                                        {% endif %}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {% if trace.options is not empty %}
                                        <tr>
                                            <th class=\"font-normal\">Request options</th>
                                            <td>{{ profiler_dump(trace.options, maxDepth=1) }}</td>
                                        </tr>
                                    {% endif %}
                                    <tr>
                                        <th class=\"font-normal\">Response</th>
                                        <td{% if trace.curlCommand is defined and trace.curlCommand %} colspan=\"2\"{% endif %}>
                                            {% if trace.http_code >= 500 %}
                                                {% set responseStatus = 'error' %}
                                            {% elseif trace.http_code >= 400 %}
                                                {% set responseStatus = 'warning' %}
                                            {% else %}
                                                {% set responseStatus = 'success' %}
                                            {% endif %}
                                            <span class=\"font-normal status-response-status-code status-{{ responseStatus }}\">
                                                {{ trace.http_code }}
                                            </span>

                                            {{ profiler_dump(trace.info, maxDepth=1) }}
                                        </td>
                                        {% if profiler_token and profiler_link %}
                                            <td>
                                                <span><a href=\"{{ profiler_link }}\" target=\"_blank\">{{ profiler_token }}</a></span>
                                            </td>
                                        {% endif %}
                                    </tr>
                                    </tbody>
                                </table>
                            {% endfor %}
                        {% endif %}
                    </div>
                </div>
            {% endfor %}
        {% endif %}
    </div>
{% endblock %}
", "@WebProfiler/Collector/http_client.html.twig", "/var/www/iwapim/vendor/symfony/web-profiler-bundle/Resources/views/Collector/http_client.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 40, "set" => 41, "for" => 93);
        static $filters = array("escape" => 44, "length" => 94, "slice" => 109);
        static $functions = array("source" => 42, "include" => 58, "profiler_dump" => 141);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'set', 'for'],
                ['escape', 'length', 'slice'],
                ['source', 'include', 'profiler_dump']
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
