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

/* @PimcoreCore/Profiler/key_value_table.html.twig */
class __TwigTemplate_78bf23478c0b4c71281eb0dcde15ab28 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreCore/Profiler/key_value_table.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreCore/Profiler/key_value_table.html.twig"));

        // line 1
        echo "<table class=\"";
        echo twig_escape_filter($this->env, ((array_key_exists("class", $context)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed((isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 1, $this->source); })()), 1, $this->source), "")) : ("")), "html", null, true);
        echo "\">
    <thead>
    <tr>
        <th scope=\"col\" class=\"key\">";
        // line 4
        ((array_key_exists("labels", $context)) ? (print (twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["labels"]) || array_key_exists("labels", $context) ? $context["labels"] : (function () { throw new RuntimeError('Variable "labels" does not exist.', 4, $this->source); })()), 0, [], "array", false, false, true, 4), "html", null, true))) : (print ("Key")));
        echo "</th>
        <th scope=\"col\">";
        // line 5
        ((array_key_exists("labels", $context)) ? (print (twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["labels"]) || array_key_exists("labels", $context) ? $context["labels"] : (function () { throw new RuntimeError('Variable "labels" does not exist.', 5, $this->source); })()), 1, [], "array", false, false, true, 5), "html", null, true))) : (print ("Value")));
        echo "</th>
    </tr>
    </thead>
    <tbody>
    ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_array_keys_filter((isset($context["data"]) || array_key_exists("data", $context) ? $context["data"] : (function () { throw new RuntimeError('Variable "data" does not exist.', 9, $this->source); })())));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["key"]) {
            // line 10
            echo "        <tr>
            <th>";
            // line 11
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["key"], 11, $this->source), "html", null, true);
            echo "</th>
            <td>";
            // line 12
            echo $this->extensions['Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension']->dumpData($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["data"]) || array_key_exists("data", $context) ? $context["data"] : (function () { throw new RuntimeError('Variable "data" does not exist.', 12, $this->source); })()), $context["key"], [], "array", false, false, true, 12), 12, $this->source), ((array_key_exists("maxDepth", $context)) ? (_twig_default_filter($this->sandbox->ensureToStringAllowed((isset($context["maxDepth"]) || array_key_exists("maxDepth", $context) ? $context["maxDepth"] : (function () { throw new RuntimeError('Variable "maxDepth" does not exist.', 12, $this->source); })()), 12, $this->source), 0)) : (0)));
            echo "</td>
        </tr>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 15
            echo "        <tr>
            <td colspan=\"2\">(no data)</td>
        </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['key'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 19
        echo "    </tbody>
</table>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreCore/Profiler/key_value_table.html.twig";
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
        return array (  92 => 19,  83 => 15,  75 => 12,  71 => 11,  68 => 10,  63 => 9,  56 => 5,  52 => 4,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<table class=\"{{ class|default('') }}\">
    <thead>
    <tr>
        <th scope=\"col\" class=\"key\">{{ labels is defined ? labels[0] : 'Key' }}</th>
        <th scope=\"col\">{{ labels is defined ? labels[1] : 'Value' }}</th>
    </tr>
    </thead>
    <tbody>
    {% for key in data|keys %}
        <tr>
            <th>{{ key }}</th>
            <td>{{ profiler_dump(data[key], maxDepth=maxDepth|default(0)) }}</td>
        </tr>
    {% else %}
        <tr>
            <td colspan=\"2\">(no data)</td>
        </tr>
    {% endfor %}
    </tbody>
</table>
", "@PimcoreCore/Profiler/key_value_table.html.twig", "/var/www/iwapim/vendor/pimcore/pimcore/bundles/CoreBundle/templates/Profiler/key_value_table.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("for" => 9);
        static $filters = array("escape" => 1, "default" => 1, "keys" => 9);
        static $functions = array("profiler_dump" => 12);

        try {
            $this->sandbox->checkSecurity(
                ['for'],
                ['escape', 'default', 'keys'],
                ['profiler_dump']
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
