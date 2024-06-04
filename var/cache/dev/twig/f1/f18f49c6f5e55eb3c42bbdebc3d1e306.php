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

/* products/detail.html.twig */
class __TwigTemplate_c99d29c6c3173f37cda58121632039b9 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "products/detail.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "products/detail.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "products/detail.html.twig", 2);
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

        echo "Ürün Detayı";
        
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
        echo "    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h1>";
        // line 9
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 9, $this->source); })()), "key", [], "any", false, false, true, 9), 9, $this->source), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 9, $this->source); })()), "name", [], "any", false, false, true, 9), 9, $this->source), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 9, $this->source); })()), "productClass", [], "any", false, false, true, 9), 9, $this->source), "html", null, true);
        echo ")</h1>
            <div class=\"row\">
                <div class=\"col-md-6\">
                    <p><strong>Ürün Kodu:</strong> ";
        // line 12
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 12, $this->source); })()), "productCode", [], "any", false, false, true, 12), 12, $this->source), "html", null, true);
        echo "</p>
                    <p><strong>IWASKU:</strong> ";
        // line 13
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 13, $this->source); })()), "iwasku", [], "any", false, false, true, 13), 13, $this->source), "html", null, true);
        echo "</p>
                    <p><strong>Aktif:</strong> ";
        // line 14
        echo ((twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 14, $this->source); })()), "iwaskuActive", [], "any", false, false, true, 14)) ? ("Evet") : ("Hayır"));
        echo "</p>
                </div>
                <div class=\"col-md-6\">
                    <p><strong>SEO Başlığı:</strong> ";
        // line 17
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 17, $this->source); })()), "seoTitle", [], "any", false, false, true, 17), 17, $this->source), "html", null, true);
        echo "</p>
                    <p><strong>SEO Açıklaması:</strong> ";
        // line 18
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 18, $this->source); })()), "seoDescription", [], "any", false, false, true, 18), 18, $this->source), "html", null, true);
        echo "</p>
                    <p><strong>SEO Anahtar Kelimeler:</strong> ";
        // line 19
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 19, $this->source); })()), "seoKeywords", [], "any", false, false, true, 19), 19, $this->source), "html", null, true);
        echo "</p>
                </div>
            </div>
            <p><strong>Açıklama:</strong> ";
        // line 22
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 22, $this->source); })()), "description", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
        echo "</p>
        </div>
    </div>

   ";
        // line 26
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 26, $this->source); })()), "album", [], "any", false, false, true, 26)) > 0)) {
            // line 27
            echo "        <div class=\"card card-custom mb-3\">
            <div class=\"card-body\">
                <h2>Ürün Görselleri</h2>
                <div class=\"row\">
                    ";
            // line 31
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 31, $this->source); })()), "album", [], "any", false, false, true, 31));
            foreach ($context['_seq'] as $context["_key"] => $context["image"]) {
                // line 32
                echo "                        ";
                if ($context["image"]) {
                    // line 33
                    echo "                            <div class=\"col-md-3\">
                                <a href=\"";
                    // line 34
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["image"], "image", [], "any", false, false, true, 34), "getFullPath", [], "method", false, false, true, 34), 34, $this->source), "html", null, true);
                    echo "\" target=\"_blank\">
                                    <img src=\"";
                    // line 35
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["image"], "image", [], "any", false, false, true, 35), "getThumbnail", ["default"], "method", false, false, true, 35), 35, $this->source), "html", null, true);
                    echo "\" class=\"img-thumbnail mb-3\" alt=\"Ürün Görseli\">
                                </a>
                            </div>
                        ";
                }
                // line 39
                echo "                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['image'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 40
            echo "                </div>
            </div>
        </div>
    ";
        }
        // line 44
        echo "
    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Ebatlar</h2>
            <table class=\"table\">
                <thead>
                    <tr>
                        <th></th>
                        <th>En</th>
                        <th>Boy</th>
                        <th>Derinlik</th>
                        <th>Ağırlık</th>
                        <th>Kara Desi</th>
                        <th>Hava/Deniz Desi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Ürün</strong></td>
                        <td>";
        // line 63
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 63, $this->source); })()), "productWidth", [], "any", false, false, true, 63), 63, $this->source), "html", null, true);
        echo "</td>
                        <td>";
        // line 64
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 64, $this->source); })()), "productHeight", [], "any", false, false, true, 64), 64, $this->source), "html", null, true);
        echo "</td>
                        <td>";
        // line 65
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 65, $this->source); })()), "productDepth", [], "any", false, false, true, 65), 65, $this->source), "html", null, true);
        echo "</td>
                        <td>";
        // line 66
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 66, $this->source); })()), "productWeight", [], "any", false, false, true, 66), 66, $this->source), "html", null, true);
        echo "</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><strong>Paket (Kutu)</strong></td>
                        <td>";
        // line 72
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 72, $this->source); })()), "packageWidth", [], "any", false, false, true, 72), 72, $this->source), "html", null, true);
        echo "</td>
                        <td>";
        // line 73
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 73, $this->source); })()), "packegeHeight", [], "any", false, false, true, 73), 73, $this->source), "html", null, true);
        echo "</td>
                        <td>";
        // line 74
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 74, $this->source); })()), "packageDepth", [], "any", false, false, true, 74), 74, $this->source), "html", null, true);
        echo "</td>
                        <td>";
        // line 75
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 75, $this->source); })()), "packageWeight", [], "any", false, false, true, 75), 75, $this->source), "html", null, true);
        echo "</td>
                        <td>";
        // line 76
        echo twig_escape_filter($this->env, (max($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 76, $this->source); })()), "packageWeight", [], "any", false, false, true, 76), 76, $this->source), ((twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 76, $this->source); })()), "packageWidth", [], "any", false, false, true, 76) * twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 76, $this->source); })()), "packegeHeight", [], "any", false, false, true, 76)) * twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 76, $this->source); })()), "packageDepth", [], "any", false, false, true, 76))) / 3000), "html", null, true);
        echo "</td>
                        <td>";
        // line 77
        echo twig_escape_filter($this->env, (max($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 77, $this->source); })()), "packageWeight", [], "any", false, false, true, 77), 77, $this->source), ((twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 77, $this->source); })()), "packageWidth", [], "any", false, false, true, 77) * twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 77, $this->source); })()), "packegeHeight", [], "any", false, false, true, 77)) * twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 77, $this->source); })()), "packageDepth", [], "any", false, false, true, 77))) / 5000), "html", null, true);
        echo "</td>
                    </tr>
                    ";
        // line 79
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 79, $this->source); })()), "bundleItems", [], "any", false, false, true, 79)) > 0)) {
            // line 80
            echo "                        <tr><td colspan=\"7\"><h4>Set İçindeki Ürünlerin Paket (Kutu) Ebatları</h4></td></tr>
                            ";
            // line 81
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 81, $this->source); })()), "bundleItems", [], "any", false, false, true, 81));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 82
                echo "                                <tr>
                                    <td><strong>";
                // line 83
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "name", [], "any", false, false, true, 83), 83, $this->source), "html", null, true);
                echo "</strong></td>
                                    <td>";
                // line 84
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "packageWidth", [], "any", false, false, true, 84), 84, $this->source), "html", null, true);
                echo "</td>
                                    <td>";
                // line 85
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "packegeHeight", [], "any", false, false, true, 85), 85, $this->source), "html", null, true);
                echo "</td>
                                    <td>";
                // line 86
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "packageDepth", [], "any", false, false, true, 86), 86, $this->source), "html", null, true);
                echo "</td>
                                    <td>";
                // line 87
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "packageWeight", [], "any", false, false, true, 87), 87, $this->source), "html", null, true);
                echo "</td>
                                    <td>";
                // line 88
                echo twig_escape_filter($this->env, (max($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "packageWeight", [], "any", false, false, true, 88), 88, $this->source), ((twig_get_attribute($this->env, $this->source, $context["item"], "packageWidth", [], "any", false, false, true, 88) * twig_get_attribute($this->env, $this->source, $context["item"], "packegeHeight", [], "any", false, false, true, 88)) * twig_get_attribute($this->env, $this->source, $context["item"], "packageDepth", [], "any", false, false, true, 88))) / 3000), "html", null, true);
                echo "</td>
                                    <td>";
                // line 89
                echo twig_escape_filter($this->env, (max($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "packageWeight", [], "any", false, false, true, 89), 89, $this->source), ((twig_get_attribute($this->env, $this->source, $context["item"], "packageWidth", [], "any", false, false, true, 89) * twig_get_attribute($this->env, $this->source, $context["item"], "packegeHeight", [], "any", false, false, true, 89)) * twig_get_attribute($this->env, $this->source, $context["item"], "packageDepth", [], "any", false, false, true, 89))) / 5000), "html", null, true);
                echo "</td>
                                </tr>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 92
            echo "                        </ul>
                    ";
        }
        // line 94
        echo "                </tbody>
            </table>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Renk Varyasyonları</h2>
            <div class=\"row\">
                ";
        // line 103
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 103, $this->source); })()), "children", [], "any", false, false, true, 103));
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 104
            echo "                    ";
            if ((twig_get_attribute($this->env, $this->source, $context["child"], "variationType", [], "any", false, false, true, 104) == "color")) {
                // line 105
                echo "                        <div class=\"col-md-3\">
                            <div class=\"card card-custom mb-3\">
                                <div class=\"card-body\">
                                    <h5>";
                // line 108
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["child"], "key", [], "any", false, false, true, 108), 108, $this->source), "html", null, true);
                echo "</h5>
                                    ";
                // line 109
                if (twig_get_attribute($this->env, $this->source, $context["child"], "picture", [], "any", false, false, true, 109)) {
                    // line 110
                    echo "                                        <a href=\"";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["child"], "picture", [], "any", false, false, true, 110), "getFullPath", [], "method", false, false, true, 110), 110, $this->source), "html", null, true);
                    echo "\" target=\"_blank\">
                                            <img src=\"";
                    // line 111
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["child"], "picture", [], "any", false, false, true, 111), "getThumbnail", ["default"], "method", false, false, true, 111), 111, $this->source), "html", null, true);
                    echo "\" class=\"img-thumbnail mb-3\" alt=\"";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["child"], "name", [], "any", false, false, true, 111), 111, $this->source), "html", null, true);
                    echo "\" style=\"max-width: 150px;\">
                                        </a>
                                    ";
                }
                // line 114
                echo "                                    <p><strong>Ürün Kodu:</strong> ";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["child"], "productCode", [], "any", false, false, true, 114), 114, $this->source), "html", null, true);
                echo "</p>
                                    <p><strong>IWASKU:</strong> ";
                // line 115
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["child"], "iwasku", [], "any", false, false, true, 115), 115, $this->source), "html", null, true);
                echo "</p>
                                    <p><strong>Aktif:</strong> ";
                // line 116
                echo ((twig_get_attribute($this->env, $this->source, $context["child"], "iwaskuActive", [], "any", false, false, true, 116)) ? ("Evet") : ("Hayır"));
                echo "</p>
                                </div>
                            </div>
                        </div>
                    ";
            }
            // line 121
            echo "                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 122
        echo "                <div class=\"col-md-3\">
                    <div class=\"card card-custom mb-3\">
                        <div class=\"card-body\">
                            <h5>Yeni Renk Ekle</h5>
                            <form action=\"/path/to/your/handler\" method=\"post\">
                                <div class=\"mb-3\">
                                    <label for=\"color\" class=\"form-label\">Renk</label>
                                    <input type=\"text\" class=\"form-control\" id=\"color\" name=\"color\">
                                </div>
                                <button type=\"submit\" class=\"btn btn-primary\">Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Ebat Varyasyonları</h2>
            <div class=\"row\">
                ";
        // line 144
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 144, $this->source); })()), "children", [], "any", false, false, true, 144));
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 145
            echo "                    ";
            if ((twig_get_attribute($this->env, $this->source, $context["child"], "variationType", [], "any", false, false, true, 145) == "size")) {
                // line 146
                echo "                        <div class=\"col-md-3\">
                            <div class=\"card card-custom mb-3\">
                                <div class=\"card-body\">
                                    <h5>";
                // line 149
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["child"], "key", [], "any", false, false, true, 149), 149, $this->source), "html", null, true);
                echo "</h5>
                                    ";
                // line 150
                if (twig_get_attribute($this->env, $this->source, $context["child"], "picture", [], "any", false, false, true, 150)) {
                    // line 151
                    echo "                                        <a href=\"";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["child"], "picture", [], "any", false, false, true, 151), "getFullPath", [], "method", false, false, true, 151), 151, $this->source), "html", null, true);
                    echo "\" target=\"_blank\">
                                            <img src=\"";
                    // line 152
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["child"], "picture", [], "any", false, false, true, 152), "getThumbnail", ["default"], "method", false, false, true, 152), 152, $this->source), "html", null, true);
                    echo "\" class=\"img-thumbnail mb-3\" alt=\"";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["child"], "name", [], "any", false, false, true, 152), 152, $this->source), "html", null, true);
                    echo "\" style=\"max-width: 150px;\">
                                        </a>
                                    ";
                }
                // line 155
                echo "                                    <p><strong>Ürün Kodu:</strong> ";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["child"], "productCode", [], "any", false, false, true, 155), 155, $this->source), "html", null, true);
                echo "</p>
                                    <p><strong>IWASKU:</strong> ";
                // line 156
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["child"], "iwasku", [], "any", false, false, true, 156), 156, $this->source), "html", null, true);
                echo "</p>
                                    <p><strong>Aktif:</strong> ";
                // line 157
                echo ((twig_get_attribute($this->env, $this->source, $context["child"], "iwaskuActive", [], "any", false, false, true, 157)) ? ("Evet") : ("Hayır"));
                echo "</p>
                                </div>
                            </div>
                        </div>
                    ";
            }
            // line 162
            echo "                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 163
        echo "                <div class=\"col-md-3\">
                    <div class=\"card card-custom mb-3\">
                        <div class=\"card-body\">
                            <h5>Yeni Ebat Ekle</h5>
                            <form action=\"/path/to/your/handler\" method=\"post\">
                                <div class=\"mb-3\">
                                    <label for=\"size\" class=\"form-label\">Ebat İsmi</label>
                                    <input type=\"text\" class=\"form-control\" id=\"size\" name=\"size\">
                                </div>
                                <button type=\"submit\" class=\"btn btn-primary\">Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Set İçeriği</h2>
            ";
        // line 184
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 184, $this->source); })()), "bundleItems", [], "any", false, false, true, 184)) > 0)) {
            // line 185
            echo "                <ul>
                    ";
            // line 186
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 186, $this->source); })()), "bundleItems", [], "any", false, false, true, 186));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 187
                echo "                        <li>";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "name", [], "any", false, false, true, 187), 187, $this->source), "html", null, true);
                echo "</li>
                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 189
            echo "                </ul>
            ";
        } else {
            // line 191
            echo "                <p>Set içeriği yok.</p>
            ";
        }
        // line 193
        echo "        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Listing</h2>
            <p>Listing henüz hazır değil.</p>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Stok Takip</h2>
            <p>Stok henüz hazır değil.</p>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Maliyet</h2>
            <p>Maliyet henüz hazır değil.</p>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Reklam</h2>
            ";
        // line 220
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 220, $this->source); })()), "marketingMaterials", [], "any", false, false, true, 220)) > 0)) {
            // line 221
            echo "                <ul>
                    ";
            // line 222
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new RuntimeError('Variable "product" does not exist.', 222, $this->source); })()), "marketingMaterials", [], "any", false, false, true, 222));
            foreach ($context['_seq'] as $context["_key"] => $context["material"]) {
                // line 223
                echo "                        <li>
                            <strong>Başlık:</strong> ";
                // line 224
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["material"], "title", [], "any", false, false, true, 224), 224, $this->source), "html", null, true);
                echo "<br>
                            <strong>Açıklama:</strong> ";
                // line 225
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["material"], "description", [], "any", false, false, true, 225), 225, $this->source), "html", null, true);
                echo "<br>
                            <strong>Kampanya Adı:</strong> ";
                // line 226
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["material"], "campaignName", [], "any", false, false, true, 226), 226, $this->source), "html", null, true);
                echo "<br>
                            <strong>Durum:</strong> ";
                // line 227
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["material"], "status", [], "any", false, false, true, 227), 227, $this->source), "html", null, true);
                echo "<br>
                            <strong>Varlık:</strong>
                            ";
                // line 229
                if (twig_get_attribute($this->env, $this->source, $context["material"], "asset", [], "any", false, false, true, 229)) {
                    // line 230
                    echo "                                <a href=\"";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["material"], "asset", [], "any", false, false, true, 230), "getFullPath", [], "method", false, false, true, 230), 230, $this->source), "html", null, true);
                    echo "\" target=\"_blank\">";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["material"], "asset", [], "any", false, false, true, 230), "getFilename", [], "method", false, false, true, 230), 230, $this->source), "html", null, true);
                    echo "</a>
                            ";
                } else {
                    // line 232
                    echo "                                Yok
                            ";
                }
                // line 234
                echo "                        </li>
                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['material'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 236
            echo "                </ul>
            ";
        } else {
            // line 238
            echo "                <p>Reklam materyalleri yok.</p>
            ";
        }
        // line 240
        echo "        </div>
    </div>

";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "products/detail.html.twig";
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
        return array (  575 => 240,  571 => 238,  567 => 236,  560 => 234,  556 => 232,  548 => 230,  546 => 229,  541 => 227,  537 => 226,  533 => 225,  529 => 224,  526 => 223,  522 => 222,  519 => 221,  517 => 220,  488 => 193,  484 => 191,  480 => 189,  471 => 187,  467 => 186,  464 => 185,  462 => 184,  439 => 163,  433 => 162,  425 => 157,  421 => 156,  416 => 155,  408 => 152,  403 => 151,  401 => 150,  397 => 149,  392 => 146,  389 => 145,  385 => 144,  361 => 122,  355 => 121,  347 => 116,  343 => 115,  338 => 114,  330 => 111,  325 => 110,  323 => 109,  319 => 108,  314 => 105,  311 => 104,  307 => 103,  296 => 94,  292 => 92,  283 => 89,  279 => 88,  275 => 87,  271 => 86,  267 => 85,  263 => 84,  259 => 83,  256 => 82,  252 => 81,  249 => 80,  247 => 79,  242 => 77,  238 => 76,  234 => 75,  230 => 74,  226 => 73,  222 => 72,  213 => 66,  209 => 65,  205 => 64,  201 => 63,  180 => 44,  174 => 40,  168 => 39,  161 => 35,  157 => 34,  154 => 33,  151 => 32,  147 => 31,  141 => 27,  139 => 26,  132 => 22,  126 => 19,  122 => 18,  118 => 17,  112 => 14,  108 => 13,  104 => 12,  94 => 9,  90 => 7,  80 => 6,  61 => 4,  38 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("{# templates/products/detail.html.twig #}
{% extends 'base.html.twig' %}

{% block title %}Ürün Detayı{% endblock %}

{% block content %}
    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h1>{{ product.key }} {{ product.name }} ({{ product.productClass }})</h1>
            <div class=\"row\">
                <div class=\"col-md-6\">
                    <p><strong>Ürün Kodu:</strong> {{ product.productCode }}</p>
                    <p><strong>IWASKU:</strong> {{ product.iwasku }}</p>
                    <p><strong>Aktif:</strong> {{ product.iwaskuActive ? 'Evet' : 'Hayır' }}</p>
                </div>
                <div class=\"col-md-6\">
                    <p><strong>SEO Başlığı:</strong> {{ product.seoTitle }}</p>
                    <p><strong>SEO Açıklaması:</strong> {{ product.seoDescription }}</p>
                    <p><strong>SEO Anahtar Kelimeler:</strong> {{ product.seoKeywords }}</p>
                </div>
            </div>
            <p><strong>Açıklama:</strong> {{ product.description }}</p>
        </div>
    </div>

   {% if product.album|length > 0 %}
        <div class=\"card card-custom mb-3\">
            <div class=\"card-body\">
                <h2>Ürün Görselleri</h2>
                <div class=\"row\">
                    {% for image in product.album %}
                        {% if image %}
                            <div class=\"col-md-3\">
                                <a href=\"{{ image.image.getFullPath() }}\" target=\"_blank\">
                                    <img src=\"{{ image.image.getThumbnail('default') }}\" class=\"img-thumbnail mb-3\" alt=\"Ürün Görseli\">
                                </a>
                            </div>
                        {% endif %}
                    {% endfor %}
                </div>
            </div>
        </div>
    {% endif %}

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Ebatlar</h2>
            <table class=\"table\">
                <thead>
                    <tr>
                        <th></th>
                        <th>En</th>
                        <th>Boy</th>
                        <th>Derinlik</th>
                        <th>Ağırlık</th>
                        <th>Kara Desi</th>
                        <th>Hava/Deniz Desi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Ürün</strong></td>
                        <td>{{ product.productWidth }}</td>
                        <td>{{ product.productHeight }}</td>
                        <td>{{ product.productDepth }}</td>
                        <td>{{ product.productWeight }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><strong>Paket (Kutu)</strong></td>
                        <td>{{ product.packageWidth }}</td>
                        <td>{{ product.packegeHeight }}</td>
                        <td>{{ product.packageDepth }}</td>
                        <td>{{ product.packageWeight }}</td>
                        <td>{{ max(product.packageWeight, (product.packageWidth * product.packegeHeight * product.packageDepth)) / 3000 }}</td>
                        <td>{{ max(product.packageWeight, (product.packageWidth * product.packegeHeight * product.packageDepth)) / 5000 }}</td>
                    </tr>
                    {% if product.bundleItems|length > 0 %}
                        <tr><td colspan=\"7\"><h4>Set İçindeki Ürünlerin Paket (Kutu) Ebatları</h4></td></tr>
                            {% for item in product.bundleItems %}
                                <tr>
                                    <td><strong>{{ item.name }}</strong></td>
                                    <td>{{ item.packageWidth }}</td>
                                    <td>{{ item.packegeHeight }}</td>
                                    <td>{{ item.packageDepth }}</td>
                                    <td>{{ item.packageWeight }}</td>
                                    <td>{{ max(item.packageWeight, (item.packageWidth * item.packegeHeight * item.packageDepth)) / 3000 }}</td>
                                    <td>{{ max(item.packageWeight, (item.packageWidth * item.packegeHeight * item.packageDepth)) / 5000 }}</td>
                                </tr>
                            {% endfor %}
                        </ul>
                    {% endif %}
                </tbody>
            </table>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Renk Varyasyonları</h2>
            <div class=\"row\">
                {% for child in product.children %}
                    {% if child.variationType == 'color' %}
                        <div class=\"col-md-3\">
                            <div class=\"card card-custom mb-3\">
                                <div class=\"card-body\">
                                    <h5>{{ child.key }}</h5>
                                    {% if child.picture %}
                                        <a href=\"{{ child.picture.getFullPath() }}\" target=\"_blank\">
                                            <img src=\"{{ child.picture.getThumbnail('default') }}\" class=\"img-thumbnail mb-3\" alt=\"{{ child.name }}\" style=\"max-width: 150px;\">
                                        </a>
                                    {% endif %}
                                    <p><strong>Ürün Kodu:</strong> {{ child.productCode }}</p>
                                    <p><strong>IWASKU:</strong> {{ child.iwasku }}</p>
                                    <p><strong>Aktif:</strong> {{ child.iwaskuActive ? 'Evet' : 'Hayır' }}</p>
                                </div>
                            </div>
                        </div>
                    {% endif %}
                {% endfor %}
                <div class=\"col-md-3\">
                    <div class=\"card card-custom mb-3\">
                        <div class=\"card-body\">
                            <h5>Yeni Renk Ekle</h5>
                            <form action=\"/path/to/your/handler\" method=\"post\">
                                <div class=\"mb-3\">
                                    <label for=\"color\" class=\"form-label\">Renk</label>
                                    <input type=\"text\" class=\"form-control\" id=\"color\" name=\"color\">
                                </div>
                                <button type=\"submit\" class=\"btn btn-primary\">Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Ebat Varyasyonları</h2>
            <div class=\"row\">
                {% for child in product.children %}
                    {% if child.variationType == 'size' %}
                        <div class=\"col-md-3\">
                            <div class=\"card card-custom mb-3\">
                                <div class=\"card-body\">
                                    <h5>{{ child.key }}</h5>
                                    {% if child.picture %}
                                        <a href=\"{{ child.picture.getFullPath() }}\" target=\"_blank\">
                                            <img src=\"{{ child.picture.getThumbnail('default') }}\" class=\"img-thumbnail mb-3\" alt=\"{{ child.name }}\" style=\"max-width: 150px;\">
                                        </a>
                                    {% endif %}
                                    <p><strong>Ürün Kodu:</strong> {{ child.productCode }}</p>
                                    <p><strong>IWASKU:</strong> {{ child.iwasku }}</p>
                                    <p><strong>Aktif:</strong> {{ child.iwaskuActive ? 'Evet' : 'Hayır' }}</p>
                                </div>
                            </div>
                        </div>
                    {% endif %}
                {% endfor %}
                <div class=\"col-md-3\">
                    <div class=\"card card-custom mb-3\">
                        <div class=\"card-body\">
                            <h5>Yeni Ebat Ekle</h5>
                            <form action=\"/path/to/your/handler\" method=\"post\">
                                <div class=\"mb-3\">
                                    <label for=\"size\" class=\"form-label\">Ebat İsmi</label>
                                    <input type=\"text\" class=\"form-control\" id=\"size\" name=\"size\">
                                </div>
                                <button type=\"submit\" class=\"btn btn-primary\">Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Set İçeriği</h2>
            {% if product.bundleItems|length > 0 %}
                <ul>
                    {% for item in product.bundleItems %}
                        <li>{{ item.name }}</li>
                    {% endfor %}
                </ul>
            {% else %}
                <p>Set içeriği yok.</p>
            {% endif %}
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Listing</h2>
            <p>Listing henüz hazır değil.</p>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Stok Takip</h2>
            <p>Stok henüz hazır değil.</p>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Maliyet</h2>
            <p>Maliyet henüz hazır değil.</p>
        </div>
    </div>

    <div class=\"card card-custom mb-3\">
        <div class=\"card-body\">
            <h2>Reklam</h2>
            {% if product.marketingMaterials|length > 0 %}
                <ul>
                    {% for material in product.marketingMaterials %}
                        <li>
                            <strong>Başlık:</strong> {{ material.title }}<br>
                            <strong>Açıklama:</strong> {{ material.description }}<br>
                            <strong>Kampanya Adı:</strong> {{ material.campaignName }}<br>
                            <strong>Durum:</strong> {{ material.status }}<br>
                            <strong>Varlık:</strong>
                            {% if material.asset %}
                                <a href=\"{{ material.asset.getFullPath() }}\" target=\"_blank\">{{ material.asset.getFilename() }}</a>
                            {% else %}
                                Yok
                            {% endif %}
                        </li>
                    {% endfor %}
                </ul>
            {% else %}
                <p>Reklam materyalleri yok.</p>
            {% endif %}
        </div>
    </div>

{% endblock %}
", "products/detail.html.twig", "/var/www/iwapim/templates/products/detail.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 26, "for" => 31);
        static $filters = array("escape" => 9, "length" => 26);
        static $functions = array("max" => 76);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'for'],
                ['escape', 'length'],
                ['max']
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
