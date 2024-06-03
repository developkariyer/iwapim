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

/* @WebProfiler/Profiler/layout.html.twig */
class __TwigTemplate_4878a49445a1bf5154352faf6cedbfbf extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'body' => [$this, 'block_body'],
            'summary' => [$this, 'block_summary'],
            'sidebar' => [$this, 'block_sidebar'],
            'sidebar_shortcuts_links' => [$this, 'block_sidebar_shortcuts_links'],
            'panel' => [$this, 'block_panel'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "@WebProfiler/Profiler/base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/layout.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/layout.html.twig"));

        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/base.html.twig", "@WebProfiler/Profiler/layout.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_body($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "    <div class=\"container\">
        ";
        // line 5
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/header.html.twig", ["profile_type" => (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 5, $this->source); })())], false);
        echo "

        <div id=\"summary\">
        ";
        // line 8
        $this->displayBlock('summary', $context, $blocks);
        // line 21
        echo "    </div>

        <div id=\"content\">
            <div id=\"main\">
                <div id=\"sidebar\">
                    ";
        // line 26
        $this->displayBlock('sidebar', $context, $blocks);
        // line 71
        echo "                </div>

                <div id=\"collector-wrapper\">
                    <div id=\"collector-content\">
                        ";
        // line 75
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/base_js.html.twig");
        echo "
                        ";
        // line 76
        $this->displayBlock('panel', $context, $blocks);
        // line 77
        echo "                    </div>
                </div>
            </div>
        </div>
    </div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 8
    public function block_summary($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "summary"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "summary"));

        // line 9
        echo "            ";
        if (array_key_exists("profile", $context)) {
            // line 10
            echo "                ";
            $context["request_collector"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["profile"] ?? null), "collectors", [], "any", false, true, true, 10), "request", [], "any", true, true, true, 10)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["profile"] ?? null), "collectors", [], "any", false, true, true, 10), "request", [], "any", false, false, true, 10), 10, $this->source), false)) : (false));
            // line 11
            echo "
                ";
            // line 12
            echo twig_include($this->env, $context, twig_sprintf("@WebProfiler/Profiler/_%s_summary.html.twig", $this->sandbox->ensureToStringAllowed((isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 12, $this->source); })()), 12, $this->source)), ["profile" =>             // line 13
(isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 13, $this->source); })()), "command_collector" => ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 14
($context["profile"] ?? null), "collectors", [], "any", false, true, true, 14), "command", [], "any", true, true, true, 14)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["profile"] ?? null), "collectors", [], "any", false, true, true, 14), "command", [], "any", false, false, true, 14), 14, $this->source), false)) : (false)), "request_collector" =>             // line 15
(isset($context["request_collector"]) || array_key_exists("request_collector", $context) ? $context["request_collector"] : (function () { throw new RuntimeError('Variable "request_collector" does not exist.', 15, $this->source); })()), "request" =>             // line 16
(isset($context["request"]) || array_key_exists("request", $context) ? $context["request"] : (function () { throw new RuntimeError('Variable "request" does not exist.', 16, $this->source); })()), "token" =>             // line 17
(isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 17, $this->source); })())], false);
            // line 18
            echo "
            ";
        }
        // line 20
        echo "        ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 26
    public function block_sidebar($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar"));

        // line 27
        echo "                        <div id=\"sidebar-contents\">
                            <div id=\"sidebar-shortcuts\">
                                ";
        // line 29
        $this->displayBlock('sidebar_shortcuts_links', $context, $blocks);
        // line 39
        echo "
                                ";
        // line 40
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("web_profiler.controller.profiler::searchBarAction", array(), twig_array_merge(["type" => (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 40, $this->source); })())], $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["request"]) || array_key_exists("request", $context) ? $context["request"] : (function () { throw new RuntimeError('Variable "request" does not exist.', 40, $this->source); })()), "query", [], "any", false, false, true, 40), "all", [], "any", false, false, true, 40), 40, $this->source))));
        echo "
                            </div>

                            ";
        // line 43
        if (array_key_exists("templates", $context)) {
            // line 44
            echo "                                <ul id=\"menu-profiler\">
                                    ";
            // line 45
            if (("request" === (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 45, $this->source); })()))) {
                // line 46
                echo "                                        ";
                $context["excludes"] = ["command"];
                // line 47
                echo "                                    ";
            } elseif (("command" === (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 47, $this->source); })()))) {
                // line 48
                echo "                                        ";
                $context["excludes"] = ["request", "router"];
                // line 49
                echo "                                    ";
            }
            // line 50
            echo "
                                    ";
            // line 51
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_array_filter($this->env, (isset($context["templates"]) || array_key_exists("templates", $context) ? $context["templates"] : (function () { throw new RuntimeError('Variable "templates" does not exist.', 51, $this->source); })()), function ($__t__, $__n__) use ($context, $macros) { $context["t"] = $__t__; $context["n"] = $__n__; return !twig_in_filter((isset($context["n"]) || array_key_exists("n", $context) ? $context["n"] : (function () { throw new RuntimeError('Variable "n" does not exist.', 51, $this->source); })()), (isset($context["excludes"]) || array_key_exists("excludes", $context) ? $context["excludes"] : (function () { throw new RuntimeError('Variable "excludes" does not exist.', 51, $this->source); })())); }));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["name"] => $context["template"]) {
                // line 52
                echo "                                        ";
                ob_start();
                // line 53
                if (                $this->loadTemplate($context["template"], "@WebProfiler/Profiler/layout.html.twig", 53)->hasBlock("menu", $context)) {
                    // line 54
                    $__internal_compile_0 = $context;
                    $__internal_compile_1 = ["collector" => twig_get_attribute($this->env, $this->source, (isset($context["profile"]) || array_key_exists("profile", $context) ? $context["profile"] : (function () { throw new RuntimeError('Variable "profile" does not exist.', 54, $this->source); })()), "getcollector", [$context["name"]], "method", false, false, true, 54), "profiler_markup_version" => (isset($context["profiler_markup_version"]) || array_key_exists("profiler_markup_version", $context) ? $context["profiler_markup_version"] : (function () { throw new RuntimeError('Variable "profiler_markup_version" does not exist.', 54, $this->source); })())];
                    if (!is_iterable($__internal_compile_1)) {
                        throw new RuntimeError('Variables passed to the "with" tag must be a hash.', 54, $this->getSourceContext());
                    }
                    $__internal_compile_1 = twig_to_array($__internal_compile_1);
                    $context = $this->env->mergeGlobals(array_merge($context, $__internal_compile_1));
                    // line 55
                    $this->loadTemplate($context["template"], "@WebProfiler/Profiler/layout.html.twig", 55)->displayBlock("menu", $context);
                    $context = $__internal_compile_0;
                }
                $context["menu"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
                // line 59
                echo "                                        ";
                if ( !twig_test_empty((isset($context["menu"]) || array_key_exists("menu", $context) ? $context["menu"] : (function () { throw new RuntimeError('Variable "menu" does not exist.', 59, $this->source); })()))) {
                    // line 60
                    echo "                                            <li class=\"";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["name"], 60, $this->source), "html", null, true);
                    echo " ";
                    echo ((($context["name"] == (isset($context["panel"]) || array_key_exists("panel", $context) ? $context["panel"] : (function () { throw new RuntimeError('Variable "panel" does not exist.', 60, $this->source); })()))) ? ("selected") : (""));
                    echo "\">
                                                <a href=\"";
                    // line 61
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler", ["token" => (isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 61, $this->source); })()), "panel" => $context["name"], "type" => (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 61, $this->source); })())]), "html", null, true);
                    echo "\">";
                    echo $this->sandbox->ensureToStringAllowed((isset($context["menu"]) || array_key_exists("menu", $context) ? $context["menu"] : (function () { throw new RuntimeError('Variable "menu" does not exist.', 61, $this->source); })()), 61, $this->source);
                    echo "</a>
                                            </li>
                                        ";
                }
                // line 64
                echo "                                    ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['template'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 65
            echo "                                </ul>
                            ";
        }
        // line 67
        echo "                        </div>

                        ";
        // line 69
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/settings.html.twig");
        echo "
                    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 29
    public function block_sidebar_shortcuts_links($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar_shortcuts_links"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar_shortcuts_links"));

        // line 30
        echo "                                    <div class=\"shortcuts\">
                                        <a class=\"btn btn-link\" href=\"";
        // line 31
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler_search", ["limit" => 10, "type" => (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 31, $this->source); })())]), "html", null, true);
        echo "\">Last 10</a>
                                        <a class=\"btn btn-link\" href=\"";
        // line 32
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler", twig_array_merge(["token" => "latest", "type" => (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 32, $this->source); })())], $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["request"]) || array_key_exists("request", $context) ? $context["request"] : (function () { throw new RuntimeError('Variable "request" does not exist.', 32, $this->source); })()), "query", [], "any", false, false, true, 32), "all", [], "any", false, false, true, 32), 32, $this->source))), "html", null, true);
        echo "\">Latest</a>

                                        <a class=\"sf-toggle btn btn-link\" data-toggle-selector=\"#sidebar-search\" ";
        // line 34
        if ((array_key_exists("tokens", $context) || array_key_exists("about", $context))) {
            echo "data-toggle-initial=\"display\"";
        }
        echo ">
                                            ";
        // line 35
        echo twig_source($this->env, "@WebProfiler/Icon/search.svg");
        echo " <span class=\"hidden-small\">Search</span>
                                        </a>
                                    </div>
                                ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 76
    public function block_panel($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        echo "";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@WebProfiler/Profiler/layout.html.twig";
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
        return array (  337 => 76,  323 => 35,  317 => 34,  312 => 32,  308 => 31,  305 => 30,  295 => 29,  283 => 69,  279 => 67,  275 => 65,  261 => 64,  253 => 61,  246 => 60,  243 => 59,  238 => 55,  230 => 54,  228 => 53,  225 => 52,  208 => 51,  205 => 50,  202 => 49,  199 => 48,  196 => 47,  193 => 46,  191 => 45,  188 => 44,  186 => 43,  180 => 40,  177 => 39,  175 => 29,  171 => 27,  161 => 26,  151 => 20,  147 => 18,  145 => 17,  144 => 16,  143 => 15,  142 => 14,  141 => 13,  140 => 12,  137 => 11,  134 => 10,  131 => 9,  121 => 8,  106 => 77,  104 => 76,  100 => 75,  94 => 71,  92 => 26,  85 => 21,  83 => 8,  77 => 5,  74 => 4,  64 => 3,  41 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends '@WebProfiler/Profiler/base.html.twig' %}

{% block body %}
    <div class=\"container\">
        {{ include('@WebProfiler/Profiler/header.html.twig', {profile_type: profile_type}, with_context = false) }}

        <div id=\"summary\">
        {% block summary %}
            {% if profile is defined %}
                {% set request_collector = profile.collectors.request|default(false) %}

                {{ include('@WebProfiler/Profiler/_%s_summary.html.twig'|format(profile_type), {
                    profile: profile,
                    command_collector: profile.collectors.command|default(false) ,
                    request_collector: request_collector,
                    request: request,
                    token: token
                }, with_context=false) }}
            {% endif %}
        {% endblock %}
    </div>

        <div id=\"content\">
            <div id=\"main\">
                <div id=\"sidebar\">
                    {% block sidebar %}
                        <div id=\"sidebar-contents\">
                            <div id=\"sidebar-shortcuts\">
                                {% block sidebar_shortcuts_links %}
                                    <div class=\"shortcuts\">
                                        <a class=\"btn btn-link\" href=\"{{ path('_profiler_search', { limit: 10, type: profile_type }) }}\">Last 10</a>
                                        <a class=\"btn btn-link\" href=\"{{ path('_profiler', { token: 'latest', type: profile_type }|merge(request.query.all)) }}\">Latest</a>

                                        <a class=\"sf-toggle btn btn-link\" data-toggle-selector=\"#sidebar-search\" {% if tokens is defined or about is defined %}data-toggle-initial=\"display\"{% endif %}>
                                            {{ source('@WebProfiler/Icon/search.svg') }} <span class=\"hidden-small\">Search</span>
                                        </a>
                                    </div>
                                {% endblock sidebar_shortcuts_links %}

                                {{ render(controller('web_profiler.controller.profiler::searchBarAction', query={type: profile_type }|merge(request.query.all))) }}
                            </div>

                            {% if templates is defined %}
                                <ul id=\"menu-profiler\">
                                    {% if 'request' is same as(profile_type) %}
                                        {% set excludes = ['command'] %}
                                    {% elseif 'command' is same as(profile_type) %}
                                        {% set excludes = ['request', 'router'] %}
                                    {% endif %}

                                    {% for name, template in templates|filter((t, n) => n not in excludes) %}
                                        {% set menu -%}
                                            {%- if block('menu', template) is defined -%}
                                                {% with { collector: profile.getcollector(name), profiler_markup_version: profiler_markup_version } %}
                                                    {{- block('menu', template) -}}
                                                {% endwith %}
                                            {%- endif -%}
                                        {%- endset %}
                                        {% if menu is not empty %}
                                            <li class=\"{{ name }} {{ name == panel ? 'selected' }}\">
                                                <a href=\"{{ path('_profiler', { token: token, panel: name, type: profile_type }) }}\">{{ menu|raw }}</a>
                                            </li>
                                        {% endif %}
                                    {% endfor %}
                                </ul>
                            {% endif %}
                        </div>

                        {{ include('@WebProfiler/Profiler/settings.html.twig') }}
                    {% endblock sidebar %}
                </div>

                <div id=\"collector-wrapper\">
                    <div id=\"collector-content\">
                        {{ include('@WebProfiler/Profiler/base_js.html.twig') }}
                        {% block panel '' %}
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
", "@WebProfiler/Profiler/layout.html.twig", "/var/www/iwapim/vendor/symfony/web-profiler-bundle/Resources/views/Profiler/layout.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("block" => 8, "if" => 9, "set" => 10, "for" => 51, "with" => 54);
        static $filters = array("default" => 10, "format" => 12, "merge" => 40, "filter" => 51, "escape" => 60, "raw" => 61);
        static $functions = array("include" => 5, "render" => 40, "controller" => 40, "path" => 61, "source" => 35);

        try {
            $this->sandbox->checkSecurity(
                ['block', 'if', 'set', 'for', 'with'],
                ['default', 'format', 'merge', 'filter', 'escape', 'raw'],
                ['include', 'render', 'controller', 'path', 'source']
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
