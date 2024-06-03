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

/* @WebProfiler/Collector/time.html.twig */
class __TwigTemplate_1041dc2f7d3642e9b7eed82774a249ac extends Template
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
            'panelContent' => [$this, 'block_panelContent'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
        $macros["_self"] = $this->macros["_self"] = $this;
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/time.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/time.html.twig"));

        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/time.html.twig", 1);
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
        #timeline-control {
            background: var(--table-background);
            box-shadow: var(--shadow);
            margin: 1em 0;
            padding: 10px;
        }
        #timeline-control label {
            font-weight: bold;
            margin-right: 1em;
        }
        #timeline-control input {
            background: var(--metric-value-background);
            font-size: 16px;
            padding: 4px;
            text-align: right;
            width: 5em;
        }
        #timeline-control .help {
            margin-left: 1em;
        }

        .sf-profiler-timeline .legends {
            font-size: 12px;
            line-height: 1.5em;
        }
        .sf-profiler-timeline .legends button {
            color: var(--color-text);
        }
        .sf-profiler-timeline + p.help {
            margin-top: 0;
        }
    </style>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 41
    public function block_toolbar($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "toolbar"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "toolbar"));

        // line 42
        echo "    ";
        $context["has_time_events"] = (twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 42, $this->source); })()), "events", [], "any", false, false, true, 42), 42, $this->source)) > 0);
        // line 43
        echo "    ";
        $context["total_time"] = (((isset($context["has_time_events"]) || array_key_exists("has_time_events", $context) ? $context["has_time_events"] : (function () { throw new RuntimeError('Variable "has_time_events" does not exist.', 43, $this->source); })())) ? (twig_sprintf("%.0f", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 43, $this->source); })()), "duration", [], "any", false, false, true, 43), 43, $this->source))) : ("n/a"));
        // line 44
        echo "    ";
        $context["initialization_time"] = ((twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 44, $this->source); })()), "events", [], "any", false, false, true, 44), 44, $this->source))) ? (twig_sprintf("%.0f", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 44, $this->source); })()), "inittime", [], "any", false, false, true, 44), 44, $this->source))) : ("n/a"));
        // line 45
        echo "    ";
        $context["status_color"] = ((((isset($context["has_time_events"]) || array_key_exists("has_time_events", $context) ? $context["has_time_events"] : (function () { throw new RuntimeError('Variable "has_time_events" does not exist.', 45, $this->source); })()) && (twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 45, $this->source); })()), "duration", [], "any", false, false, true, 45) > 1000))) ? ("yellow") : (""));
        // line 46
        echo "
    ";
        // line 47
        ob_start();
        // line 48
        echo "        ";
        echo twig_source($this->env, "@WebProfiler/Icon/time.svg");
        echo "
        <span class=\"sf-toolbar-value\">";
        // line 49
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["total_time"]) || array_key_exists("total_time", $context) ? $context["total_time"] : (function () { throw new RuntimeError('Variable "total_time" does not exist.', 49, $this->source); })()), 49, $this->source), "html", null, true);
        echo "</span>
        <span class=\"sf-toolbar-label\">ms</span>
    ";
        $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 52
        echo "
    ";
        // line 53
        ob_start();
        // line 54
        echo "        <div class=\"sf-toolbar-info-piece\">
            <b>Total time</b>
            <span>";
        // line 56
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["total_time"]) || array_key_exists("total_time", $context) ? $context["total_time"] : (function () { throw new RuntimeError('Variable "total_time" does not exist.', 56, $this->source); })()), 56, $this->source), "html", null, true);
        echo " ms</span>
        </div>
        <div class=\"sf-toolbar-info-piece\">
            <b>Initialization time</b>
            <span>";
        // line 60
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["initialization_time"]) || array_key_exists("initialization_time", $context) ? $context["initialization_time"] : (function () { throw new RuntimeError('Variable "initialization_time" does not exist.', 60, $this->source); })()), 60, $this->source), "html", null, true);
        echo " ms</span>
        </div>
    ";
        $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 63
        echo "
    ";
        // line 64
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", ["link" => (isset($context["profiler_url"]) || array_key_exists("profiler_url", $context) ? $context["profiler_url"] : (function () { throw new RuntimeError('Variable "profiler_url" does not exist.', 64, $this->source); })()), "status" => (isset($context["status_color"]) || array_key_exists("status_color", $context) ? $context["status_color"] : (function () { throw new RuntimeError('Variable "status_color" does not exist.', 64, $this->source); })())]);
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 67
    public function block_menu($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        // line 68
        echo "    <span class=\"label\">
        <span class=\"icon\">";
        // line 69
        echo twig_source($this->env, "@WebProfiler/Icon/time.svg");
        echo "</span>
        <strong>Performance</strong>
    </span>
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
        echo "    ";
        $context["has_time_events"] = (twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 75, $this->source); })()), "events", [], "any", false, false, true, 75), 75, $this->source)) > 0);
        // line 76
        echo "    <h2>Performance metrics</h2>

    <div class=\"metrics\">
        <div class=\"metric-group\">
            <div class=\"metric\">
                <span class=\"value\">";
        // line 81
        echo twig_escape_filter($this->env, twig_sprintf("%.0f", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 81, $this->source); })()), "duration", [], "any", false, false, true, 81), 81, $this->source)), "html", null, true);
        echo " <span class=\"unit\">ms</span></span>
                <span class=\"label\">Total execution time</span>
            </div>

            <div class=\"metric\">
                <span class=\"value\">";
        // line 86
        echo twig_escape_filter($this->env, twig_sprintf("%.0f", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 86, $this->source); })()), "inittime", [], "any", false, false, true, 86), 86, $this->source)), "html", null, true);
        echo " <span class=\"unit\">ms</span></span>
                <span class=\"label\">Symfony initialization</span>
            </div>
        </div>

        ";
        // line 91
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 91, $this->source); })()), "collectors", [], "any", false, false, true, 91), "memory", [], "any", false, false, true, 91)) {
            // line 92
            echo "            <div class=\"metric-divider\"></div>

            <div class=\"metric\">
                <span class=\"value\">";
            // line 95
            echo twig_escape_filter($this->env, twig_sprintf("%.2f", ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 95, $this->source); })()), "collectors", [], "any", false, false, true, 95), "memory", [], "any", false, false, true, 95), "memory", [], "any", false, false, true, 95) / 1024) / 1024)), "html", null, true);
            echo " <span class=\"unit\">MiB</span></span>
                <span class=\"label\">Peak memory usage</span>
            </div>
        ";
        }
        // line 99
        echo "
        ";
        // line 100
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 100, $this->source); })()), "children", [], "any", false, false, true, 100)) > 0)) {
            // line 101
            echo "            <div class=\"metric-divider\"></div>

            <div class=\"metric-group\">
                <div class=\"metric\">
                    <span class=\"value\">";
            // line 105
            echo twig_escape_filter($this->env, twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 105, $this->source); })()), "children", [], "any", false, false, true, 105), 105, $this->source)), "html", null, true);
            echo "</span>
                    <span class=\"label\">Sub-";
            // line 106
            echo twig_escape_filter($this->env, twig_title_string_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 106, $this->source); })()), 106, $this->source)), "html", null, true);
            echo (((twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 106, $this->source); })()), "children", [], "any", false, false, true, 106), 106, $this->source)) > 1)) ? ("s") : (""));
            echo "</span>
                </div>

                ";
            // line 109
            $context["subrequests_time"] = (((isset($context["has_time_events"]) || array_key_exists("has_time_events", $context) ? $context["has_time_events"] : (function () { throw new RuntimeError('Variable "has_time_events" does not exist.', 109, $this->source); })())) ? (twig_array_reduce($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source,             // line 110
(isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 110, $this->source); })()), "children", [], "any", false, false, true, 110), 110, $this->source), function ($__total__, $__child__) use ($context, $macros) { $context["total"] = $__total__; $context["child"] = $__child__; return ((isset($context["total"]) || array_key_exists("total", $context) ? $context["total"] : (function () { throw new RuntimeError('Variable "total" does not exist.', 110, $this->source); })()) + twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["child"]) || array_key_exists("child", $context) ? $context["child"] : (function () { throw new RuntimeError('Variable "child" does not exist.', 110, $this->source); })()), "getcollector", ["time"], "method", false, false, true, 110), "events", [], "any", false, false, true, 110), "__section__", [], "any", false, false, true, 110), "duration", [], "any", false, false, true, 110)); }, 0)) : ("n/a"));
            // line 112
            echo "
                <div class=\"metric\">
                    <span class=\"value\">";
            // line 114
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["subrequests_time"]) || array_key_exists("subrequests_time", $context) ? $context["subrequests_time"] : (function () { throw new RuntimeError('Variable "subrequests_time" does not exist.', 114, $this->source); })()), 114, $this->source), "html", null, true);
            echo " <span class=\"unit\">ms</span></span>
                    <span class=\"label\">Sub-";
            // line 115
            echo twig_escape_filter($this->env, twig_title_string_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 115, $this->source); })()), 115, $this->source)), "html", null, true);
            echo (((twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 115, $this->source); })()), "children", [], "any", false, false, true, 115), 115, $this->source)) > 1)) ? ("s") : (""));
            echo " time</span>
                </div>
            </div>
        ";
        }
        // line 119
        echo "    </div>

    <h2>Execution timeline</h2>

    ";
        // line 123
        if ( !twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 123, $this->source); })()), "isStopwatchInstalled", [], "method", false, false, true, 123)) {
            // line 124
            echo "        <div class=\"empty empty-panel\">
            <p>The Stopwatch component is not installed. If you want to see timing events, run: <code>composer require symfony/stopwatch</code>.</p>
        </div>
    ";
        } elseif (twig_test_empty(twig_get_attribute($this->env, $this->source,         // line 127
(isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 127, $this->source); })()), "events", [], "any", false, false, true, 127))) {
            // line 128
            echo "        <div class=\"empty\">
            <p>No timing events have been recorded. Check that symfony/stopwatch is installed and debugging enabled in the kernel.</p>
        </div>
    ";
        } else {
            // line 132
            echo "        ";
            $this->displayBlock("panelContent", $context, $blocks);
            echo "
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 136
    public function block_panelContent($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panelContent"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panelContent"));

        // line 137
        echo "    <form id=\"timeline-control\" action=\"\" method=\"get\">
        <input type=\"hidden\" name=\"panel\" value=\"time\">
        <label for=\"threshold\">Threshold</label>
        <input type=\"number\" name=\"threshold\" id=\"threshold\" value=\"1\" min=\"0\" placeholder=\"1.1\"> ms
        <span class=\"help\">(timeline only displays events with a duration longer than this threshold)</span>
    </form>

    ";
        // line 144
        if (twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 144, $this->source); })()), "parent", [], "any", false, false, true, 144)) {
            // line 145
            echo "        <h3 class=\"dump-inline\">
            Sub-";
            // line 146
            echo twig_escape_filter($this->env, twig_title_string_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 146, $this->source); })()), 146, $this->source)), "html", null, true);
            echo " ";
            echo $this->extensions['Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension']->dumpData($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 146, $this->source); })()), "getcollector", ["request"], "method", false, false, true, 146), "requestattributes", [], "any", false, false, true, 146), "get", ["_controller"], "method", false, false, true, 146), 146, $this->source));
            echo "
            <small>
                ";
            // line 148
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 148, $this->source); })()), "events", [], "any", false, false, true, 148), "__section__", [], "any", false, false, true, 148), "duration", [], "any", false, false, true, 148), 148, $this->source), "html", null, true);
            echo " ms
                <a class=\"newline\" href=\"";
            // line 149
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler", ["token" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 149, $this->source); })()), "parent", [], "any", false, false, true, 149), "token", [], "any", false, false, true, 149), "panel" => "time"]), "html", null, true);
            echo "\">Return to parent ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 149, $this->source); })()), 149, $this->source), "html", null, true);
            echo "</a>
            </small>
        </h3>
    ";
        } elseif ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source,         // line 152
(isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 152, $this->source); })()), "children", [], "any", false, false, true, 152)) > 0)) {
            // line 153
            echo "        <h3>
            Main ";
            // line 154
            echo twig_escape_filter($this->env, twig_title_string_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 154, $this->source); })()), 154, $this->source)), "html", null, true);
            echo " <small>";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 154, $this->source); })()), "events", [], "any", false, false, true, 154), "__section__", [], "any", false, false, true, 154), "duration", [], "any", false, false, true, 154), 154, $this->source), "html", null, true);
            echo " ms</small>
        </h3>
    ";
        }
        // line 157
        echo "
    ";
        // line 158
        echo twig_call_macro($macros["_self"], "macro_display_timeline", [(isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 158, $this->source); })()), twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 158, $this->source); })()), "events", [], "any", false, false, true, 158), twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 158, $this->source); })()), "events", [], "any", false, false, true, 158), "__section__", [], "any", false, false, true, 158), "origin", [], "any", false, false, true, 158)], 158, $context, $this->getSourceContext());
        echo "

    ";
        // line 160
        if (twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 160, $this->source); })()), "children", [], "any", false, false, true, 160))) {
            // line 161
            echo "        <p class=\"help\">Note: sections with a striped background correspond to sub-";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 161, $this->source); })()), 161, $this->source), "html", null, true);
            echo "s.</p>

        <h3>Sub-";
            // line 163
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 163, $this->source); })()), 163, $this->source), "html", null, true);
            echo "s <small>(";
            echo twig_escape_filter($this->env, twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 163, $this->source); })()), "children", [], "any", false, false, true, 163), 163, $this->source)), "html", null, true);
            echo ")</small></h3>

        ";
            // line 165
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 165, $this->source); })()), "children", [], "any", false, false, true, 165));
            foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                // line 166
                echo "            ";
                $context["events"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["child"], "getcollector", ["time"], "method", false, false, true, 166), "events", [], "any", false, false, true, 166);
                // line 167
                echo "            <h4>
                <a href=\"";
                // line 168
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler", ["token" => twig_get_attribute($this->env, $this->source, $context["child"], "token", [], "any", false, false, true, 168), "panel" => "time"]), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["child"], "getcollector", ["request"], "method", false, false, true, 168), "identifier", [], "any", false, false, true, 168), 168, $this->source), "html", null, true);
                echo "</a>
                <small>";
                // line 169
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["events"]) || array_key_exists("events", $context) ? $context["events"] : (function () { throw new RuntimeError('Variable "events" does not exist.', 169, $this->source); })()), "__section__", [], "any", false, false, true, 169), "duration", [], "any", false, false, true, 169), 169, $this->source), "html", null, true);
                echo " ms</small>
            </h4>

            ";
                // line 172
                echo twig_call_macro($macros["_self"], "macro_display_timeline", [twig_get_attribute($this->env, $this->source, $context["child"], "token", [], "any", false, false, true, 172), (isset($context["events"]) || array_key_exists("events", $context) ? $context["events"] : (function () { throw new RuntimeError('Variable "events" does not exist.', 172, $this->source); })()), twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 172, $this->source); })()), "events", [], "any", false, false, true, 172), "__section__", [], "any", false, false, true, 172), "origin", [], "any", false, false, true, 172)], 172, $context, $this->getSourceContext());
                echo "
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 174
            echo "    ";
        }
        // line 175
        echo "
<svg id=\"timeline-template\" width=\"0\" height=\"0\">
  <defs>
    <pattern id=\"subrequest\" class=\"timeline-subrequest-pattern\" patternUnits=\"userSpaceOnUse\" width=\"20\" height=\"20\" viewBox=\"0 0 40 40\">
        <path d=\"M0 40L40 0H20L0 20M40 40V20L20 40\"/>
    </pattern>
  </defs>
</svg>
<style>
";
        // line 184
        echo twig_include($this->env, $context, "@WebProfiler/Collector/time.css.twig");
        echo "
</style>
<script>
";
        // line 187
        echo twig_source($this->env, "@WebProfiler/Collector/time.js");
        echo "
</script>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 191
    public function macro_dump_request_data($__token__ = null, $__events__ = null, $__origin__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "token" => $__token__,
            "events" => $__events__,
            "origin" => $__origin__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start();
        try {
            $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "dump_request_data"));

            $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "dump_request_data"));

            // line 193
            echo "{
    id: \"";
            // line 194
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 194, $this->source); })()), 194, $this->source), "js", null, true);
            echo "\",
    left: ";
            // line 195
            echo twig_escape_filter($this->env, twig_sprintf("%F", (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["events"]) || array_key_exists("events", $context) ? $context["events"] : (function () { throw new RuntimeError('Variable "events" does not exist.', 195, $this->source); })()), "__section__", [], "any", false, false, true, 195), "origin", [], "any", false, false, true, 195) - (isset($context["origin"]) || array_key_exists("origin", $context) ? $context["origin"] : (function () { throw new RuntimeError('Variable "origin" does not exist.', 195, $this->source); })()))), "js", null, true);
            echo ",
    end: \"";
            // line 196
            echo twig_escape_filter($this->env, twig_sprintf("%F", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["events"]) || array_key_exists("events", $context) ? $context["events"] : (function () { throw new RuntimeError('Variable "events" does not exist.', 196, $this->source); })()), "__section__", [], "any", false, false, true, 196), "endtime", [], "any", false, false, true, 196), 196, $this->source)), "js", null, true);
            echo "\",
    events: [ ";
            // line 197
            echo twig_call_macro($macros["_self"], "macro_dump_events", [(isset($context["events"]) || array_key_exists("events", $context) ? $context["events"] : (function () { throw new RuntimeError('Variable "events" does not exist.', 197, $this->source); })())], 197, $context, $this->getSourceContext());
            echo " ],
}
";
            
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

            
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);


            return ('' === $tmp = ob_get_contents()) ? '' : new Markup($tmp, $this->env->getCharset());
        } finally {
            ob_end_clean();
        }
    }

    // line 202
    public function macro_dump_events($__events__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "events" => $__events__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start();
        try {
            $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "dump_events"));

            $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "dump_events"));

            // line 204
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["events"]) || array_key_exists("events", $context) ? $context["events"] : (function () { throw new RuntimeError('Variable "events" does not exist.', 204, $this->source); })()));
            foreach ($context['_seq'] as $context["name"] => $context["event"]) {
                // line 205
                if (("__section__" != $context["name"])) {
                    // line 206
                    echo "{
    name: \"";
                    // line 207
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["name"], 207, $this->source), "js", null, true);
                    echo "\",
    category: \"";
                    // line 208
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["event"], "category", [], "any", false, false, true, 208), 208, $this->source), "js", null, true);
                    echo "\",
    origin: ";
                    // line 209
                    echo twig_escape_filter($this->env, twig_sprintf("%F", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["event"], "origin", [], "any", false, false, true, 209), 209, $this->source)), "js", null, true);
                    echo ",
    starttime: ";
                    // line 210
                    echo twig_escape_filter($this->env, twig_sprintf("%F", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["event"], "starttime", [], "any", false, false, true, 210), 210, $this->source)), "js", null, true);
                    echo ",
    endtime: ";
                    // line 211
                    echo twig_escape_filter($this->env, twig_sprintf("%F", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["event"], "endtime", [], "any", false, false, true, 211), 211, $this->source)), "js", null, true);
                    echo ",
    duration: ";
                    // line 212
                    echo twig_escape_filter($this->env, twig_sprintf("%F", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["event"], "duration", [], "any", false, false, true, 212), 212, $this->source)), "js", null, true);
                    echo ",
    memory: ";
                    // line 213
                    echo twig_escape_filter($this->env, twig_sprintf("%.1F", ((twig_get_attribute($this->env, $this->source, $context["event"], "memory", [], "any", false, false, true, 213) / 1024) / 1024)), "js", null, true);
                    echo ",
    elements: {},
    periods: [";
                    // line 216
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["event"], "periods", [], "any", false, false, true, 216));
                    foreach ($context['_seq'] as $context["_key"] => $context["period"]) {
                        // line 217
                        echo "{
            start: ";
                        // line 218
                        echo twig_escape_filter($this->env, twig_sprintf("%F", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["period"], "starttime", [], "any", false, false, true, 218), 218, $this->source)), "js", null, true);
                        echo ",
            end: ";
                        // line 219
                        echo twig_escape_filter($this->env, twig_sprintf("%F", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["period"], "endtime", [], "any", false, false, true, 219), 219, $this->source)), "js", null, true);
                        echo ",
            duration: ";
                        // line 220
                        echo twig_escape_filter($this->env, twig_sprintf("%F", $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["period"], "duration", [], "any", false, false, true, 220), 220, $this->source)), "js", null, true);
                        echo ",
            elements: {}
        },";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['period'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 224
                    echo "],
},
";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['event'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

            
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);


            return ('' === $tmp = ob_get_contents()) ? '' : new Markup($tmp, $this->env->getCharset());
        } finally {
            ob_end_clean();
        }
    }

    // line 231
    public function macro_display_timeline($__token__ = null, $__events__ = null, $__origin__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "token" => $__token__,
            "events" => $__events__,
            "origin" => $__origin__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start();
        try {
            $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "display_timeline"));

            $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "display_timeline"));

            // line 232
            echo "    <div class=\"sf-profiler-timeline\">
        <div id=\"legend-";
            // line 233
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 233, $this->source); })()), 233, $this->source), "html", null, true);
            echo "\" class=\"legends\"></div>
        <svg id=\"timeline-";
            // line 234
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 234, $this->source); })()), 234, $this->source), "html", null, true);
            echo "\" class=\"timeline-graph\"></svg>
        <script>";
            // line 236
            echo "            window.addEventListener('load', function onLoad() {
                const theme = new Theme();

                new TimelineEngine(
                    theme,
                    new SvgRenderer(document.getElementById('timeline-";
            // line 241
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 241, $this->source); })()), 241, $this->source), "js", null, true);
            echo "')),
                    new Legend(document.getElementById('legend-";
            // line 242
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 242, $this->source); })()), 242, $this->source), "js", null, true);
            echo "'), theme),
                    document.getElementById('threshold'),
                    ";
            // line 244
            echo twig_call_macro($macros["_self"], "macro_dump_request_data", [(isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 244, $this->source); })()), (isset($context["events"]) || array_key_exists("events", $context) ? $context["events"] : (function () { throw new RuntimeError('Variable "events" does not exist.', 244, $this->source); })()), (isset($context["origin"]) || array_key_exists("origin", $context) ? $context["origin"] : (function () { throw new RuntimeError('Variable "origin" does not exist.', 244, $this->source); })())], 244, $context, $this->getSourceContext());
            echo "
                );
            });
        ";
            // line 247
            echo "</script>
    </div>
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
        return "@WebProfiler/Collector/time.html.twig";
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
        return array (  703 => 247,  697 => 244,  692 => 242,  688 => 241,  681 => 236,  677 => 234,  673 => 233,  670 => 232,  649 => 231,  627 => 224,  618 => 220,  614 => 219,  610 => 218,  607 => 217,  603 => 216,  598 => 213,  594 => 212,  590 => 211,  586 => 210,  582 => 209,  578 => 208,  574 => 207,  571 => 206,  569 => 205,  565 => 204,  546 => 202,  528 => 197,  524 => 196,  520 => 195,  516 => 194,  513 => 193,  492 => 191,  479 => 187,  473 => 184,  462 => 175,  459 => 174,  451 => 172,  445 => 169,  439 => 168,  436 => 167,  433 => 166,  429 => 165,  422 => 163,  416 => 161,  414 => 160,  409 => 158,  406 => 157,  398 => 154,  395 => 153,  393 => 152,  385 => 149,  381 => 148,  374 => 146,  371 => 145,  369 => 144,  360 => 137,  350 => 136,  336 => 132,  330 => 128,  328 => 127,  323 => 124,  321 => 123,  315 => 119,  307 => 115,  303 => 114,  299 => 112,  297 => 110,  296 => 109,  289 => 106,  285 => 105,  279 => 101,  277 => 100,  274 => 99,  267 => 95,  262 => 92,  260 => 91,  252 => 86,  244 => 81,  237 => 76,  234 => 75,  224 => 74,  210 => 69,  207 => 68,  197 => 67,  185 => 64,  182 => 63,  176 => 60,  169 => 56,  165 => 54,  163 => 53,  160 => 52,  154 => 49,  149 => 48,  147 => 47,  144 => 46,  141 => 45,  138 => 44,  135 => 43,  132 => 42,  122 => 41,  75 => 4,  65 => 3,  42 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block head %}
    {{ parent() }}

    <style>
        #timeline-control {
            background: var(--table-background);
            box-shadow: var(--shadow);
            margin: 1em 0;
            padding: 10px;
        }
        #timeline-control label {
            font-weight: bold;
            margin-right: 1em;
        }
        #timeline-control input {
            background: var(--metric-value-background);
            font-size: 16px;
            padding: 4px;
            text-align: right;
            width: 5em;
        }
        #timeline-control .help {
            margin-left: 1em;
        }

        .sf-profiler-timeline .legends {
            font-size: 12px;
            line-height: 1.5em;
        }
        .sf-profiler-timeline .legends button {
            color: var(--color-text);
        }
        .sf-profiler-timeline + p.help {
            margin-top: 0;
        }
    </style>
{% endblock %}

{% block toolbar %}
    {% set has_time_events = collector.events|length > 0 %}
    {% set total_time = has_time_events ? '%.0f'|format(collector.duration) : 'n/a' %}
    {% set initialization_time = collector.events|length ? '%.0f'|format(collector.inittime) : 'n/a' %}
    {% set status_color = has_time_events and collector.duration > 1000 ? 'yellow' %}

    {% set icon %}
        {{ source('@WebProfiler/Icon/time.svg') }}
        <span class=\"sf-toolbar-value\">{{ total_time }}</span>
        <span class=\"sf-toolbar-label\">ms</span>
    {% endset %}

    {% set text %}
        <div class=\"sf-toolbar-info-piece\">
            <b>Total time</b>
            <span>{{ total_time }} ms</span>
        </div>
        <div class=\"sf-toolbar-info-piece\">
            <b>Initialization time</b>
            <span>{{ initialization_time }} ms</span>
        </div>
    {% endset %}

    {{ include('@WebProfiler/Profiler/toolbar_item.html.twig', { link: profiler_url, status: status_color }) }}
{% endblock %}

{% block menu %}
    <span class=\"label\">
        <span class=\"icon\">{{ source('@WebProfiler/Icon/time.svg') }}</span>
        <strong>Performance</strong>
    </span>
{% endblock %}

{% block panel %}
    {% set has_time_events = collector.events|length > 0 %}
    <h2>Performance metrics</h2>

    <div class=\"metrics\">
        <div class=\"metric-group\">
            <div class=\"metric\">
                <span class=\"value\">{{ '%.0f'|format(collector.duration) }} <span class=\"unit\">ms</span></span>
                <span class=\"label\">Total execution time</span>
            </div>

            <div class=\"metric\">
                <span class=\"value\">{{ '%.0f'|format(collector.inittime) }} <span class=\"unit\">ms</span></span>
                <span class=\"label\">Symfony initialization</span>
            </div>
        </div>

        {% if profile.collectors.memory %}
            <div class=\"metric-divider\"></div>

            <div class=\"metric\">
                <span class=\"value\">{{ '%.2f'|format(profile.collectors.memory.memory / 1024 / 1024) }} <span class=\"unit\">MiB</span></span>
                <span class=\"label\">Peak memory usage</span>
            </div>
        {% endif %}

        {% if profile.children|length > 0 %}
            <div class=\"metric-divider\"></div>

            <div class=\"metric-group\">
                <div class=\"metric\">
                    <span class=\"value\">{{ profile.children|length }}</span>
                    <span class=\"label\">Sub-{{ profile_type|title }}{{ profile.children|length > 1 ? 's' }}</span>
                </div>

                {% set subrequests_time = has_time_events
                    ? profile.children|reduce((total, child) => total + child.getcollector('time').events.__section__.duration, 0)
                    : 'n/a' %}

                <div class=\"metric\">
                    <span class=\"value\">{{ subrequests_time }} <span class=\"unit\">ms</span></span>
                    <span class=\"label\">Sub-{{ profile_type|title }}{{ profile.children|length > 1 ? 's' }} time</span>
                </div>
            </div>
        {% endif %}
    </div>

    <h2>Execution timeline</h2>

    {% if not collector.isStopwatchInstalled() %}
        <div class=\"empty empty-panel\">
            <p>The Stopwatch component is not installed. If you want to see timing events, run: <code>composer require symfony/stopwatch</code>.</p>
        </div>
    {% elseif collector.events is empty %}
        <div class=\"empty\">
            <p>No timing events have been recorded. Check that symfony/stopwatch is installed and debugging enabled in the kernel.</p>
        </div>
    {% else %}
        {{ block('panelContent') }}
    {% endif %}
{% endblock %}

{% block panelContent %}
    <form id=\"timeline-control\" action=\"\" method=\"get\">
        <input type=\"hidden\" name=\"panel\" value=\"time\">
        <label for=\"threshold\">Threshold</label>
        <input type=\"number\" name=\"threshold\" id=\"threshold\" value=\"1\" min=\"0\" placeholder=\"1.1\"> ms
        <span class=\"help\">(timeline only displays events with a duration longer than this threshold)</span>
    </form>

    {% if profile.parent %}
        <h3 class=\"dump-inline\">
            Sub-{{ profile_type|title }} {{ profiler_dump(profile.getcollector('request').requestattributes.get('_controller')) }}
            <small>
                {{ collector.events.__section__.duration }} ms
                <a class=\"newline\" href=\"{{ path('_profiler', { token: profile.parent.token, panel: 'time' }) }}\">Return to parent {{ profile_type }}</a>
            </small>
        </h3>
    {% elseif profile.children|length > 0 %}
        <h3>
            Main {{ profile_type|title }} <small>{{ collector.events.__section__.duration }} ms</small>
        </h3>
    {% endif %}

    {{ _self.display_timeline(token, collector.events, collector.events.__section__.origin) }}

    {% if profile.children|length %}
        <p class=\"help\">Note: sections with a striped background correspond to sub-{{ profile_type }}s.</p>

        <h3>Sub-{{ profile_type }}s <small>({{ profile.children|length }})</small></h3>

        {% for child in profile.children %}
            {% set events = child.getcollector('time').events %}
            <h4>
                <a href=\"{{ path('_profiler', { token: child.token, panel: 'time' }) }}\">{{ child.getcollector('request').identifier }}</a>
                <small>{{ events.__section__.duration }} ms</small>
            </h4>

            {{ _self.display_timeline(child.token, events, collector.events.__section__.origin) }}
        {% endfor %}
    {% endif %}

<svg id=\"timeline-template\" width=\"0\" height=\"0\">
  <defs>
    <pattern id=\"subrequest\" class=\"timeline-subrequest-pattern\" patternUnits=\"userSpaceOnUse\" width=\"20\" height=\"20\" viewBox=\"0 0 40 40\">
        <path d=\"M0 40L40 0H20L0 20M40 40V20L20 40\"/>
    </pattern>
  </defs>
</svg>
<style>
{{ include('@WebProfiler/Collector/time.css.twig') }}
</style>
<script>
{{ source('@WebProfiler/Collector/time.js') }}
</script>
{% endblock %}

{% macro dump_request_data(token, events, origin) %}
{% autoescape 'js' %}
{
    id: \"{{ token }}\",
    left: {{ \"%F\"|format(events.__section__.origin - origin) }},
    end: \"{{ '%F'|format(events.__section__.endtime) }}\",
    events: [ {{ _self.dump_events(events) }} ],
}
{% endautoescape %}
{% endmacro %}

{% macro dump_events(events) %}
{% autoescape 'js' %}
{% for name, event in events %}
{% if '__section__' != name %}
{
    name: \"{{ name }}\",
    category: \"{{ event.category }}\",
    origin: {{ \"%F\"|format(event.origin) }},
    starttime: {{ \"%F\"|format(event.starttime) }},
    endtime: {{ \"%F\"|format(event.endtime) }},
    duration: {{ \"%F\"|format(event.duration) }},
    memory: {{ \"%.1F\"|format(event.memory / 1024 / 1024) }},
    elements: {},
    periods: [
        {%- for period in event.periods -%}
        {
            start: {{ \"%F\"|format(period.starttime) }},
            end: {{ \"%F\"|format(period.endtime) }},
            duration: {{ \"%F\"|format(period.duration) }},
            elements: {}
        },
        {%- endfor -%}
    ],
},
{% endif %}
{% endfor %}
{% endautoescape %}
{% endmacro %}

{% macro display_timeline(token, events, origin) %}
    <div class=\"sf-profiler-timeline\">
        <div id=\"legend-{{ token }}\" class=\"legends\"></div>
        <svg id=\"timeline-{{ token }}\" class=\"timeline-graph\"></svg>
        <script>{% autoescape 'js' %}
            window.addEventListener('load', function onLoad() {
                const theme = new Theme();

                new TimelineEngine(
                    theme,
                    new SvgRenderer(document.getElementById('timeline-{{ token }}')),
                    new Legend(document.getElementById('legend-{{ token }}'), theme),
                    document.getElementById('threshold'),
                    {{ _self.dump_request_data(token, events, origin) }}
                );
            });
        {% endautoescape %}</script>
    </div>
{% endmacro %}
", "@WebProfiler/Collector/time.html.twig", "/var/www/iwapim/vendor/symfony/web-profiler-bundle/Resources/views/Collector/time.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 42, "if" => 91, "for" => 165, "macro" => 191, "autoescape" => 192);
        static $filters = array("length" => 42, "format" => 43, "escape" => 49, "title" => 106, "reduce" => 110);
        static $functions = array("source" => 48, "include" => 64, "profiler_dump" => 146, "path" => 149);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'for', 'macro', 'autoescape', 'import'],
                ['length', 'format', 'escape', 'title', 'reduce'],
                ['source', 'include', 'profiler_dump', 'path']
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
