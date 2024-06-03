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

/* @WebProfiler/Collector/events.html.twig */
class __TwigTemplate_c788aa8b8bec5e66629112b131ea093b extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'menu' => [$this, 'block_menu'],
            'panel' => [$this, 'block_panel'],
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/events.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/events.html.twig"));

        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/events.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_menu($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        // line 4
        echo "<span class=\"label\">
    <span class=\"icon\">";
        // line 5
        echo twig_source($this->env, "@WebProfiler/Icon/event.svg");
        echo "</span>
    <strong>Events</strong>
</span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 10
    public function block_panel($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        // line 11
        echo "    <h2>Dispatched Events</h2>

    <div class=\"sf-tabs\">
        ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 14, $this->source); })()), "data", [], "any", false, false, true, 14));
        foreach ($context['_seq'] as $context["dispatcherName"] => $context["dispatcherData"]) {
            // line 15
            echo "            <div class=\"tab\">
                <h3 class=\"tab-title\">";
            // line 16
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["dispatcherName"], 16, $this->source), "html", null, true);
            echo "</h3>
                <div class=\"tab-content\">
                    ";
            // line 18
            if (twig_test_empty(twig_get_attribute($this->env, $this->source, $context["dispatcherData"], "called_listeners", [], "array", false, false, true, 18))) {
                // line 19
                echo "                        <div class=\"empty empty-panel\">
                            <p>No events have been recorded. Check that debugging is enabled in the kernel.</p>
                        </div>
                    ";
            } else {
                // line 23
                echo "                        <div class=\"sf-tabs\">
                            <div class=\"tab\">
                                <h3 class=\"tab-title\">Called Listeners <span class=\"badge\">";
                // line 25
                echo twig_escape_filter($this->env, twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["dispatcherData"], "called_listeners", [], "array", false, false, true, 25), 25, $this->source)), "html", null, true);
                echo "</span></h3>

                                <div class=\"tab-content\">
                                    ";
                // line 28
                echo twig_call_macro($macros["_self"], "macro_render_table", [twig_get_attribute($this->env, $this->source, $context["dispatcherData"], "called_listeners", [], "array", false, false, true, 28)], 28, $context, $this->getSourceContext());
                echo "
                                </div>
                            </div>

                            <div class=\"tab\">
                                <h3 class=\"tab-title\">Not Called Listeners <span class=\"badge\">";
                // line 33
                echo twig_escape_filter($this->env, twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["dispatcherData"], "not_called_listeners", [], "array", false, false, true, 33), 33, $this->source)), "html", null, true);
                echo "</span></h3>
                                <div class=\"tab-content\">
                                    ";
                // line 35
                if (twig_test_empty(twig_get_attribute($this->env, $this->source, $context["dispatcherData"], "not_called_listeners", [], "array", false, false, true, 35))) {
                    // line 36
                    echo "                                        <div class=\"empty\">
                                            <p>
                                                <strong>There are no uncalled listeners</strong>.
                                            </p>
                                            <p>
                                                All listeners were called or an error occurred
                                                when trying to collect uncalled listeners (in which case check the
                                                logs to get more information).
                                            </p>
                                        </div>
                                    ";
                } else {
                    // line 47
                    echo "                                        ";
                    echo twig_call_macro($macros["_self"], "macro_render_table", [twig_get_attribute($this->env, $this->source, $context["dispatcherData"], "not_called_listeners", [], "array", false, false, true, 47)], 47, $context, $this->getSourceContext());
                    echo "
                                    ";
                }
                // line 49
                echo "                                </div>
                            </div>

                            <div class=\"tab\">
                                <h3 class=\"tab-title\">Orphaned Events <span class=\"badge\">";
                // line 53
                echo twig_escape_filter($this->env, twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["dispatcherData"], "orphaned_events", [], "array", false, false, true, 53), 53, $this->source)), "html", null, true);
                echo "</span></h3>
                                <div class=\"tab-content\">
                                    ";
                // line 55
                if (twig_test_empty(twig_get_attribute($this->env, $this->source, $context["dispatcherData"], "orphaned_events", [], "array", false, false, true, 55))) {
                    // line 56
                    echo "                                        <div class=\"empty\">
                                            <p>
                                                <strong>There are no orphaned events</strong>.
                                            </p>
                                            <p>
                                                All dispatched events were handled or an error occurred
                                                when trying to collect orphaned events (in which case check the
                                                logs to get more information).
                                            </p>
                                        </div>
                                    ";
                } else {
                    // line 67
                    echo "                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Event</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ";
                    // line 74
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["dispatcherData"], "orphaned_events", [], "array", false, false, true, 74));
                    foreach ($context['_seq'] as $context["_key"] => $context["event"]) {
                        // line 75
                        echo "                                                    <tr>
                                                        <td class=\"font-normal\">";
                        // line 76
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["event"], 76, $this->source), "html", null, true);
                        echo "</td>
                                                    </tr>
                                                ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['event'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 79
                    echo "                                            </tbody>
                                        </table>
                                    ";
                }
                // line 82
                echo "                                </div>
                            </div>
                        </div>
                    ";
            }
            // line 86
            echo "                </div>
            </div>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['dispatcherName'], $context['dispatcherData'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 89
        echo "    </div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 92
    public function macro_render_table($__listeners__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "listeners" => $__listeners__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start();
        try {
            $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
            $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "render_table"));

            $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
            $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "macro", "render_table"));

            // line 93
            echo "    <table>
        <thead>
            <tr>
                <th class=\"text-right\">Priority</th>
                <th>Listener</th>
            </tr>
        </thead>

        ";
            // line 101
            $context["previous_event"] = twig_get_attribute($this->env, $this->source, twig_first($this->env, $this->sandbox->ensureToStringAllowed((isset($context["listeners"]) || array_key_exists("listeners", $context) ? $context["listeners"] : (function () { throw new RuntimeError('Variable "listeners" does not exist.', 101, $this->source); })()), 101, $this->source)), "event", [], "any", false, false, true, 101);
            // line 102
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["listeners"]) || array_key_exists("listeners", $context) ? $context["listeners"] : (function () { throw new RuntimeError('Variable "listeners" does not exist.', 102, $this->source); })()));
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
            foreach ($context['_seq'] as $context["_key"] => $context["listener"]) {
                // line 103
                echo "            ";
                if ((twig_get_attribute($this->env, $this->source, $context["loop"], "first", [], "any", false, false, true, 103) || (twig_get_attribute($this->env, $this->source, $context["listener"], "event", [], "any", false, false, true, 103) != (isset($context["previous_event"]) || array_key_exists("previous_event", $context) ? $context["previous_event"] : (function () { throw new RuntimeError('Variable "previous_event" does not exist.', 103, $this->source); })())))) {
                    // line 104
                    echo "                ";
                    if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "first", [], "any", false, false, true, 104)) {
                        // line 105
                        echo "                    </tbody>
                ";
                    }
                    // line 107
                    echo "
                <tbody>
                    <tr>
                        <th colspan=\"2\" class=\"colored font-normal\">";
                    // line 110
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["listener"], "event", [], "any", false, false, true, 110), 110, $this->source), "html", null, true);
                    echo "</th>
                    </tr>

                ";
                    // line 113
                    $context["previous_event"] = twig_get_attribute($this->env, $this->source, $context["listener"], "event", [], "any", false, false, true, 113);
                    // line 114
                    echo "            ";
                }
                // line 115
                echo "
            <tr>
                <td class=\"text-right nowrap\">";
                // line 117
                echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, $context["listener"], "priority", [], "any", true, true, true, 117)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["listener"], "priority", [], "any", false, false, true, 117), 117, $this->source), "-")) : ("-")), "html", null, true);
                echo "</td>
                <td class=\"font-normal\">";
                // line 118
                echo $this->extensions['Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension']->dumpData($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["listener"], "stub", [], "any", false, false, true, 118), 118, $this->source));
                echo "</td>
            </tr>

            ";
                // line 121
                if (twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, true, 121)) {
                    // line 122
                    echo "                </tbody>
            ";
                }
                // line 124
                echo "        ";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['listener'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 125
            echo "    </table>
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
        return "@WebProfiler/Collector/events.html.twig";
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
        return array (  358 => 125,  344 => 124,  340 => 122,  338 => 121,  332 => 118,  328 => 117,  324 => 115,  321 => 114,  319 => 113,  313 => 110,  308 => 107,  304 => 105,  301 => 104,  298 => 103,  280 => 102,  278 => 101,  268 => 93,  249 => 92,  238 => 89,  230 => 86,  224 => 82,  219 => 79,  210 => 76,  207 => 75,  203 => 74,  194 => 67,  181 => 56,  179 => 55,  174 => 53,  168 => 49,  162 => 47,  149 => 36,  147 => 35,  142 => 33,  134 => 28,  128 => 25,  124 => 23,  118 => 19,  116 => 18,  111 => 16,  108 => 15,  104 => 14,  99 => 11,  89 => 10,  75 => 5,  72 => 4,  62 => 3,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block menu %}
<span class=\"label\">
    <span class=\"icon\">{{ source('@WebProfiler/Icon/event.svg') }}</span>
    <strong>Events</strong>
</span>
{% endblock %}

{% block panel %}
    <h2>Dispatched Events</h2>

    <div class=\"sf-tabs\">
        {% for dispatcherName, dispatcherData in collector.data %}
            <div class=\"tab\">
                <h3 class=\"tab-title\">{{ dispatcherName }}</h3>
                <div class=\"tab-content\">
                    {% if dispatcherData['called_listeners'] is empty %}
                        <div class=\"empty empty-panel\">
                            <p>No events have been recorded. Check that debugging is enabled in the kernel.</p>
                        </div>
                    {% else %}
                        <div class=\"sf-tabs\">
                            <div class=\"tab\">
                                <h3 class=\"tab-title\">Called Listeners <span class=\"badge\">{{ dispatcherData['called_listeners']|length }}</span></h3>

                                <div class=\"tab-content\">
                                    {{ _self.render_table(dispatcherData['called_listeners']) }}
                                </div>
                            </div>

                            <div class=\"tab\">
                                <h3 class=\"tab-title\">Not Called Listeners <span class=\"badge\">{{ dispatcherData['not_called_listeners']|length }}</span></h3>
                                <div class=\"tab-content\">
                                    {% if dispatcherData['not_called_listeners'] is empty %}
                                        <div class=\"empty\">
                                            <p>
                                                <strong>There are no uncalled listeners</strong>.
                                            </p>
                                            <p>
                                                All listeners were called or an error occurred
                                                when trying to collect uncalled listeners (in which case check the
                                                logs to get more information).
                                            </p>
                                        </div>
                                    {% else %}
                                        {{ _self.render_table(dispatcherData['not_called_listeners']) }}
                                    {% endif %}
                                </div>
                            </div>

                            <div class=\"tab\">
                                <h3 class=\"tab-title\">Orphaned Events <span class=\"badge\">{{ dispatcherData['orphaned_events']|length }}</span></h3>
                                <div class=\"tab-content\">
                                    {% if dispatcherData['orphaned_events'] is empty %}
                                        <div class=\"empty\">
                                            <p>
                                                <strong>There are no orphaned events</strong>.
                                            </p>
                                            <p>
                                                All dispatched events were handled or an error occurred
                                                when trying to collect orphaned events (in which case check the
                                                logs to get more information).
                                            </p>
                                        </div>
                                    {% else %}
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Event</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {% for event in dispatcherData['orphaned_events'] %}
                                                    <tr>
                                                        <td class=\"font-normal\">{{ event }}</td>
                                                    </tr>
                                                {% endfor %}
                                            </tbody>
                                        </table>
                                    {% endif %}
                                </div>
                            </div>
                        </div>
                    {% endif %}
                </div>
            </div>
        {% endfor %}
    </div>
{% endblock %}

{% macro render_table(listeners) %}
    <table>
        <thead>
            <tr>
                <th class=\"text-right\">Priority</th>
                <th>Listener</th>
            </tr>
        </thead>

        {% set previous_event = (listeners|first).event %}
        {% for listener in listeners %}
            {% if loop.first or listener.event != previous_event %}
                {% if not loop.first %}
                    </tbody>
                {% endif %}

                <tbody>
                    <tr>
                        <th colspan=\"2\" class=\"colored font-normal\">{{ listener.event }}</th>
                    </tr>

                {% set previous_event = listener.event %}
            {% endif %}

            <tr>
                <td class=\"text-right nowrap\">{{ listener.priority|default('-') }}</td>
                <td class=\"font-normal\">{{ profiler_dump(listener.stub) }}</td>
            </tr>

            {% if loop.last %}
                </tbody>
            {% endif %}
        {% endfor %}
    </table>
{% endmacro %}
", "@WebProfiler/Collector/events.html.twig", "/var/www/iwapim/vendor/symfony/web-profiler-bundle/Resources/views/Collector/events.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("for" => 14, "if" => 18, "macro" => 92, "set" => 101);
        static $filters = array("escape" => 16, "length" => 25, "first" => 101, "default" => 117);
        static $functions = array("source" => 5, "profiler_dump" => 118);

        try {
            $this->sandbox->checkSecurity(
                ['for', 'if', 'macro', 'set', 'import'],
                ['escape', 'length', 'first', 'default'],
                ['source', 'profiler_dump']
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
