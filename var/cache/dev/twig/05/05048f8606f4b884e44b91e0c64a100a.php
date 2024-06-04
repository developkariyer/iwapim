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

/* @WebProfiler/Profiler/search.html.twig */
class __TwigTemplate_f3260d830e3aea8e34ef69697d15e1ce extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/search.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Profiler/search.html.twig"));

        // line 1
        echo "<div id=\"sidebar-search\" class=\"";
        echo (((($context["render_hidden_by_default"]) ?? (true))) ? ("hidden") : (""));
        echo "\">
    <form action=\"";
        // line 2
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("_profiler_search");
        echo "\" method=\"get\">
        <div class=\"form-group\">
            <label for=\"ip\">
                ";
        // line 5
        if (("command" == (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 5, $this->source); })()))) {
            // line 6
            echo "                    Application
                ";
        } else {
            // line 8
            echo "                    IP
                ";
        }
        // line 10
        echo "            </label>
            <input type=\"text\" name=\"ip\" id=\"ip\" value=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["ip"]) || array_key_exists("ip", $context) ? $context["ip"] : (function () { throw new RuntimeError('Variable "ip" does not exist.', 11, $this->source); })()), 11, $this->source), "html", null, true);
        echo "\">
        </div>

        <div class=\"form-group-row\">
            <div class=\"form-group\">
                <label for=\"method\">
                    ";
        // line 17
        if (("command" == (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 17, $this->source); })()))) {
            // line 18
            echo "                        Mode
                    ";
        } else {
            // line 20
            echo "                        Method
                    ";
        }
        // line 22
        echo "                </label>
                <select name=\"method\" id=\"method\">
                    <option value=\"\">Any</option>
                    ";
        // line 25
        if (("command" == (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 25, $this->source); })()))) {
            // line 26
            echo "                        ";
            $context["methods"] = ["BATCH", "INTERACTIVE"];
            // line 27
            echo "                    ";
        } else {
            // line 28
            echo "                        ";
            $context["methods"] = ["DELETE", "GET", "HEAD", "PATCH", "POST", "PUT"];
            // line 29
            echo "                    ";
        }
        // line 30
        echo "                    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["methods"]) || array_key_exists("methods", $context) ? $context["methods"] : (function () { throw new RuntimeError('Variable "methods" does not exist.', 30, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["m"]) {
            // line 31
            echo "                        <option ";
            echo ((($context["m"] == (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 31, $this->source); })()))) ? ("selected=\"selected\"") : (""));
            echo ">";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["m"], 31, $this->source), "html", null, true);
            echo "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['m'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 33
        echo "                </select>
            </div>

            <div class=\"form-group\">
                <label for=\"status_code\">
                    ";
        // line 38
        if (("command" == (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 38, $this->source); })()))) {
            // line 39
            echo "                        Exit code
                        ";
            // line 40
            $context["min_and_max"] = twig_sprintf("min=%d max=%d", 0, 255);
            // line 41
            echo "                    ";
        } else {
            // line 42
            echo "                        Status
                        ";
            // line 43
            $context["min_and_max"] = twig_sprintf("min=%d max=%d", 100, 599);
            // line 44
            echo "                    ";
        }
        // line 45
        echo "                </label>
                <input type=\"number\" name=\"status_code\" id=\"status_code\" ";
        // line 46
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["min_and_max"]) || array_key_exists("min_and_max", $context) ? $context["min_and_max"] : (function () { throw new RuntimeError('Variable "min_and_max" does not exist.', 46, $this->source); })()), 46, $this->source), "html", null, true);
        echo " value=\"";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["status_code"]) || array_key_exists("status_code", $context) ? $context["status_code"] : (function () { throw new RuntimeError('Variable "status_code" does not exist.', 46, $this->source); })()), 46, $this->source), "html", null, true);
        echo "\">
            </div>
        </div>

        <div class=\"form-group\">
            <label for=\"url\">
                ";
        // line 52
        if (("command" == (isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 52, $this->source); })()))) {
            // line 53
            echo "                    Command
                ";
        } else {
            // line 55
            echo "                    URL
                ";
        }
        // line 57
        echo "            </label>
            <input type=\"text\" name=\"url\" id=\"url\" value=\"";
        // line 58
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["url"]) || array_key_exists("url", $context) ? $context["url"] : (function () { throw new RuntimeError('Variable "url" does not exist.', 58, $this->source); })()), 58, $this->source), "html", null, true);
        echo "\">
        </div>

        <div class=\"form-group\">
            <label for=\"token\">Token</label>
            <input type=\"text\" name=\"token\" id=\"token\" size=\"8\" value=\"";
        // line 63
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new RuntimeError('Variable "token" does not exist.', 63, $this->source); })()), 63, $this->source), "html", null, true);
        echo "\">
        </div>

        <div class=\"form-group\">
            <label for=\"start\">From</label>
            <input type=\"date\" name=\"start\" id=\"start\" value=\"";
        // line 68
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["start"]) || array_key_exists("start", $context) ? $context["start"] : (function () { throw new RuntimeError('Variable "start" does not exist.', 68, $this->source); })()), 68, $this->source), "html", null, true);
        echo "\">
        </div>

        <div class=\"form-group\">
            <label for=\"end\">Until</label>
            <input type=\"date\" name=\"end\" id=\"end\" value=\"";
        // line 73
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["end"]) || array_key_exists("end", $context) ? $context["end"] : (function () { throw new RuntimeError('Variable "end" does not exist.', 73, $this->source); })()), 73, $this->source), "html", null, true);
        echo "\">
        </div>

        <div class=\"form-group-row form-group-row-search-button\">
            <div class=\"form-group\">
                <label for=\"limit\">Results</label>
                <select name=\"limit\" id=\"limit\">
                    ";
        // line 80
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable([10, 50, 100]);
        foreach ($context['_seq'] as $context["_key"] => $context["l"]) {
            // line 81
            echo "                        <option ";
            echo ((($context["l"] == (isset($context["limit"]) || array_key_exists("limit", $context) ? $context["limit"] : (function () { throw new RuntimeError('Variable "limit" does not exist.', 81, $this->source); })()))) ? ("selected=\"selected\"") : (""));
            echo ">";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["l"], 81, $this->source), "html", null, true);
            echo "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['l'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 83
        echo "                </select>
            </div>

            <input type=\"hidden\" name=\"type\" value=\"";
        // line 86
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["profile_type"]) || array_key_exists("profile_type", $context) ? $context["profile_type"] : (function () { throw new RuntimeError('Variable "profile_type" does not exist.', 86, $this->source); })()), 86, $this->source), "html", null, true);
        echo "\">

            <div class=\"form-group\">
                <button type=\"submit\" class=\"btn btn-sm\">Search</button>
            </div>
        </div>
    </form>
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
        return "@WebProfiler/Profiler/search.html.twig";
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
        return array (  229 => 86,  224 => 83,  213 => 81,  209 => 80,  199 => 73,  191 => 68,  183 => 63,  175 => 58,  172 => 57,  168 => 55,  164 => 53,  162 => 52,  151 => 46,  148 => 45,  145 => 44,  143 => 43,  140 => 42,  137 => 41,  135 => 40,  132 => 39,  130 => 38,  123 => 33,  112 => 31,  107 => 30,  104 => 29,  101 => 28,  98 => 27,  95 => 26,  93 => 25,  88 => 22,  84 => 20,  80 => 18,  78 => 17,  69 => 11,  66 => 10,  62 => 8,  58 => 6,  56 => 5,  50 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<div id=\"sidebar-search\" class=\"{{ (render_hidden_by_default ?? true) ? 'hidden' }}\">
    <form action=\"{{ path('_profiler_search') }}\" method=\"get\">
        <div class=\"form-group\">
            <label for=\"ip\">
                {% if 'command' == profile_type %}
                    Application
                {% else %}
                    IP
                {% endif %}
            </label>
            <input type=\"text\" name=\"ip\" id=\"ip\" value=\"{{ ip }}\">
        </div>

        <div class=\"form-group-row\">
            <div class=\"form-group\">
                <label for=\"method\">
                    {% if 'command' == profile_type %}
                        Mode
                    {% else %}
                        Method
                    {% endif %}
                </label>
                <select name=\"method\" id=\"method\">
                    <option value=\"\">Any</option>
                    {% if 'command' == profile_type %}
                        {% set methods = ['BATCH', 'INTERACTIVE'] %}
                    {% else %}
                        {% set methods = ['DELETE', 'GET', 'HEAD', 'PATCH', 'POST', 'PUT'] %}
                    {% endif %}
                    {% for m in methods %}
                        <option {{ m == method ? 'selected=\"selected\"' }}>{{ m }}</option>
                    {% endfor %}
                </select>
            </div>

            <div class=\"form-group\">
                <label for=\"status_code\">
                    {% if 'command' == profile_type %}
                        Exit code
                        {% set min_and_max = 'min=%d max=%d'|format(0, 255) %}
                    {% else %}
                        Status
                        {% set min_and_max = 'min=%d max=%d'|format(100, 599) %}
                    {% endif %}
                </label>
                <input type=\"number\" name=\"status_code\" id=\"status_code\" {{ min_and_max }} value=\"{{ status_code }}\">
            </div>
        </div>

        <div class=\"form-group\">
            <label for=\"url\">
                {% if 'command' == profile_type %}
                    Command
                {% else %}
                    URL
                {% endif %}
            </label>
            <input type=\"text\" name=\"url\" id=\"url\" value=\"{{ url }}\">
        </div>

        <div class=\"form-group\">
            <label for=\"token\">Token</label>
            <input type=\"text\" name=\"token\" id=\"token\" size=\"8\" value=\"{{ token }}\">
        </div>

        <div class=\"form-group\">
            <label for=\"start\">From</label>
            <input type=\"date\" name=\"start\" id=\"start\" value=\"{{ start }}\">
        </div>

        <div class=\"form-group\">
            <label for=\"end\">Until</label>
            <input type=\"date\" name=\"end\" id=\"end\" value=\"{{ end }}\">
        </div>

        <div class=\"form-group-row form-group-row-search-button\">
            <div class=\"form-group\">
                <label for=\"limit\">Results</label>
                <select name=\"limit\" id=\"limit\">
                    {% for l in [10, 50, 100] %}
                        <option {{ l == limit ? 'selected=\"selected\"' }}>{{ l }}</option>
                    {% endfor %}
                </select>
            </div>

            <input type=\"hidden\" name=\"type\" value=\"{{ profile_type }}\">

            <div class=\"form-group\">
                <button type=\"submit\" class=\"btn btn-sm\">Search</button>
            </div>
        </div>
    </form>
</div>
", "@WebProfiler/Profiler/search.html.twig", "/var/www/iwapim/vendor/symfony/web-profiler-bundle/Resources/views/Profiler/search.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 5, "set" => 26, "for" => 30);
        static $filters = array("escape" => 11, "format" => 40);
        static $functions = array("path" => 2);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'set', 'for'],
                ['escape', 'format'],
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
