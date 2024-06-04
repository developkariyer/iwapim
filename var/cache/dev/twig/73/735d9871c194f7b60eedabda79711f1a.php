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

/* products/list.html.twig */
class __TwigTemplate_ad943ce446adc185f331bdcfac414c56 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doGetParent(array $context)
    {
        // line 2
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "products/list.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "products/list.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "products/list.html.twig", 2);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 4
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        echo "Ürün Listesi";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 6
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        // line 7
        echo "    <h1>Ürünler</h1>

    <div class=\"mb-3\">
        <label for=\"productClassSelect\" class=\"form-label\">Ürün Sınıfını Seçin</label>
        <select id=\"productClassSelect\" class=\"form-select\" onchange=\"filterProductsByClass(this.value)\">
            <option value=\"\">Tüm Sınıflar</option>
            ";
        // line 13
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["productClasses"]) || array_key_exists("productClasses", $context) ? $context["productClasses"] : (function () { throw new RuntimeError('Variable "productClasses" does not exist.', 13, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["productClass"]) {
            // line 14
            echo "                <option value=\"";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["productClass"], "key", [], "any", false, false, true, 14), 14, $this->source), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["productClass"], "productClassName", [], "any", false, false, true, 14), 14, $this->source), "html", null, true);
            echo " (";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["productClass"], "key", [], "any", false, false, true, 14), 14, $this->source), "html", null, true);
            echo ")</option>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['productClass'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 16
        echo "        </select>
    </div>

    <div id=\"productsList\">
        <div class=\"row\">
            ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["products"]) || array_key_exists("products", $context) ? $context["products"] : (function () { throw new RuntimeError('Variable "products" does not exist.', 21, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 22
            echo "                <div class=\"product-item\" data-class=\"";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["product"], "getProductClass", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
            echo "\">
                    <div class=\"card mb-3 p-0\">
                        <a href=\"";
            // line 24
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("product_detail", ["id" => twig_get_attribute($this->env, $this->source, $context["product"], "id", [], "any", false, false, true, 24)]), "html", null, true);
            echo "\" target=\"_blank\" class=\"text-decoration-none text-dark\">
                        <div class=\"row g-0\">
                            <div class=\"col-md-5\">
                                <div class=\"card-body\">
                                    <h5 class=\"card-title\">";
            // line 28
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["product"], "key", [], "any", false, false, true, 28), 28, $this->source), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["product"], "name", [], "any", false, false, true, 28), 28, $this->source), "html", null, true);
            echo " (";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["product"], "productClass", [], "any", false, false, true, 28), 28, $this->source), "html", null, true);
            echo ")</h5>
                                    <strong>IWASKU:</strong> ";
            // line 29
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["product"], "iwasku", [], "any", false, false, true, 29), 29, $this->source), "html", null, true);
            echo "<br>
                                    <strong>Ürün Kodu:</strong> ";
            // line 30
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["product"], "productCode", [], "any", false, false, true, 30), 30, $this->source), "html", null, true);
            echo "<br>
                                    ";
            // line 31
            echo twig_escape_filter($this->env, twig_slice($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["product"], "description", [], "any", false, false, true, 31), 31, $this->source), 0, 100), "html", null, true);
            echo "...
                                </div>
                            </div>
                            <div class=\"col-md-5\">
                                <div class=\"card-body\">
                                </div>
                            </div>
                            ";
            // line 38
            if (twig_get_attribute($this->env, $this->source, $context["product"], "picture", [], "any", false, false, true, 38)) {
                // line 39
                echo "                                <div class=\"col-md-2 text-end\">
                                    <img src=\"";
                // line 40
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["product"], "picture", [], "any", false, false, true, 40), "getThumbnail", ["listpage"], "method", false, false, true, 40), 40, $this->source), "html", null, true);
                echo "\" class=\"img-fluid rounded-end\" alt=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["product"], "name", [], "any", false, false, true, 40), 40, $this->source), "html", null, true);
                echo "\">
                                </div>
                            ";
            }
            // line 43
            echo "                        </div>
                        </a>
                    </div>
                </div>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 48
        echo "        </div>
    </div>

    <script>
        function filterProductsByClass(classKey) {
            var items = document.querySelectorAll('.product-item');
            items.forEach(function(item) {
                if (classKey === \"\" || item.getAttribute('data-class') === classKey) {
                    item.style.display = \"block\";
                } else {
                    item.style.display = \"none\";
                }
            });
        }
    </script>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "products/list.html.twig";
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
        return array (  188 => 48,  178 => 43,  170 => 40,  167 => 39,  165 => 38,  155 => 31,  151 => 30,  147 => 29,  139 => 28,  132 => 24,  126 => 22,  122 => 21,  115 => 16,  102 => 14,  98 => 13,  90 => 7,  80 => 6,  61 => 4,  38 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("{# templates/products/list.html.twig #}
{% extends 'base.html.twig' %}

{% block title %}Ürün Listesi{% endblock %}

{% block content %}
    <h1>Ürünler</h1>

    <div class=\"mb-3\">
        <label for=\"productClassSelect\" class=\"form-label\">Ürün Sınıfını Seçin</label>
        <select id=\"productClassSelect\" class=\"form-select\" onchange=\"filterProductsByClass(this.value)\">
            <option value=\"\">Tüm Sınıflar</option>
            {% for productClass in productClasses %}
                <option value=\"{{ productClass.key }}\">{{ productClass.productClassName }} ({{ productClass.key }})</option>
            {% endfor %}
        </select>
    </div>

    <div id=\"productsList\">
        <div class=\"row\">
            {% for product in products %}
                <div class=\"product-item\" data-class=\"{{ product.getProductClass }}\">
                    <div class=\"card mb-3 p-0\">
                        <a href=\"{{ path('product_detail', {'id': product.id}) }}\" target=\"_blank\" class=\"text-decoration-none text-dark\">
                        <div class=\"row g-0\">
                            <div class=\"col-md-5\">
                                <div class=\"card-body\">
                                    <h5 class=\"card-title\">{{ product.key }} {{ product.name }} ({{ product.productClass }})</h5>
                                    <strong>IWASKU:</strong> {{ product.iwasku }}<br>
                                    <strong>Ürün Kodu:</strong> {{ product.productCode }}<br>
                                    {{ product.description|slice(0, 100) }}...
                                </div>
                            </div>
                            <div class=\"col-md-5\">
                                <div class=\"card-body\">
                                </div>
                            </div>
                            {% if product.picture %}
                                <div class=\"col-md-2 text-end\">
                                    <img src=\"{{ product.picture.getThumbnail('listpage') }}\" class=\"img-fluid rounded-end\" alt=\"{{ product.name }}\">
                                </div>
                            {% endif %}
                        </div>
                        </a>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>

    <script>
        function filterProductsByClass(classKey) {
            var items = document.querySelectorAll('.product-item');
            items.forEach(function(item) {
                if (classKey === \"\" || item.getAttribute('data-class') === classKey) {
                    item.style.display = \"block\";
                } else {
                    item.style.display = \"none\";
                }
            });
        }
    </script>
{% endblock %}
", "products/list.html.twig", "/var/www/iwapim/templates/products/list.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("for" => 13, "if" => 38);
        static $filters = array("escape" => 14, "slice" => 31);
        static $functions = array("path" => 24);

        try {
            $this->sandbox->checkSecurity(
                ['for', 'if'],
                ['escape', 'slice'],
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
