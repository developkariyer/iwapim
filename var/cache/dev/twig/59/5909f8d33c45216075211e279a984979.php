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

/* @PimcoreAdmin/admin/login/lost_password.html.twig */
class __TwigTemplate_691d6bb0e4ac7218544779690526acc4 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "@PimcoreAdmin/admin/login/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/login/lost_password.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/login/lost_password.html.twig"));

        $this->parent = $this->loadTemplate("@PimcoreAdmin/admin/login/layout.html.twig", "@PimcoreAdmin/admin/login/lost_password.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        // line 4
        echo "
    ";
        // line 5
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 5, $this->source); })()), "request", [], "any", false, false, true, 5), "method", [], "any", false, false, true, 5) == "POST")) {
            // line 6
            echo "        ";
            if ( !array_key_exists("reset_error", $context)) {
                // line 7
                echo "            <div class=\"text success\">
                ";
                // line 8
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("A temporary login link has been sent to your email address.", [], "admin"), "html", null, true);
                echo "
                <br/>
                ";
                // line 10
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("Please check your mailbox.", [], "admin"), "html", null, true);
                echo "
            </div>
        ";
            } else {
                // line 13
                echo "            <div class=\"text error\">
                ";
                // line 14
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("An error occured when resetting password:", [], "admin"), "html", null, true);
                echo "
                ";
                // line 15
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed((isset($context["reset_error"]) || array_key_exists("reset_error", $context) ? $context["reset_error"] : (function () { throw new RuntimeError('Variable "reset_error" does not exist.', 15, $this->source); })()), 15, $this->source), [], "admin"), "html", null, true);
                echo "
                <br/>
                ";
                // line 17
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("Please retry again later or contact an administrator.", [], "admin"), "html", null, true);
                echo "
            </div>
        ";
            }
            // line 20
            echo "    ";
        } else {
            // line 21
            echo "        <div class=\"text info\">
            ";
            // line 22
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("Enter your username and pimcore will send a login link to your email address", [], "admin"), "html", null, true);
            echo "
        </div>

        <form method=\"post\" action=\"";
            // line 25
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_login_lostpassword");
            echo "\">
            <input type=\"text\" name=\"username\" autocomplete=\"username\" placeholder=\"";
            // line 26
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("username", [], "admin"), "html", null, true);
            echo "\" required autofocus>
            <input type=\"hidden\" name=\"csrfToken\" value=\"";
            // line 27
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csrf"]) || array_key_exists("pimcore_csrf", $context) ? $context["pimcore_csrf"] : (function () { throw new RuntimeError('Variable "pimcore_csrf" does not exist.', 27, $this->source); })()), "getCsrfToken", [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 27, $this->source); })()), "request", [], "any", false, false, true, 27), "session", [], "any", false, false, true, 27)], "method", false, false, true, 27), 27, $this->source), "html", null, true);
            echo "\">

            <button type=\"submit\" name=\"submit\">";
            // line 29
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("submit", [], "admin"), "html", null, true);
            echo "</button>
        </form>
    ";
        }
        // line 32
        echo "
    <a href=\"";
        // line 33
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_login");
        echo "\">";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("Back to Login", [], "admin"), "html", null, true);
        echo "</a>

    ";
        // line 35
        echo $this->extensions['Pimcore\Twig\Extension\HelpersExtension']->breachAttackRandomContent();
        echo "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreAdmin/admin/login/lost_password.html.twig";
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
        return array (  151 => 35,  144 => 33,  141 => 32,  135 => 29,  130 => 27,  126 => 26,  122 => 25,  116 => 22,  113 => 21,  110 => 20,  104 => 17,  99 => 15,  95 => 14,  92 => 13,  86 => 10,  81 => 8,  78 => 7,  75 => 6,  73 => 5,  70 => 4,  60 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends '@PimcoreAdmin/admin/login/layout.html.twig' %}

{% block content %}

    {% if app.request.method == 'POST' %}
        {% if reset_error is not defined %}
            <div class=\"text success\">
                {{ 'A temporary login link has been sent to your email address.'|trans([],'admin') }}
                <br/>
                {{ 'Please check your mailbox.'|trans([],'admin') }}
            </div>
        {% else %}
            <div class=\"text error\">
                {{ 'An error occured when resetting password:'|trans([],'admin') }}
                {{ reset_error|trans([],'admin') }}
                <br/>
                {{ 'Please retry again later or contact an administrator.'|trans([],'admin') }}
            </div>
        {% endif %}
    {% else %}
        <div class=\"text info\">
            {{ 'Enter your username and pimcore will send a login link to your email address'|trans([],'admin') }}
        </div>

        <form method=\"post\" action=\"{{ path('pimcore_admin_login_lostpassword') }}\">
            <input type=\"text\" name=\"username\" autocomplete=\"username\" placeholder=\"{{ 'username'|trans([], 'admin') }}\" required autofocus>
            <input type=\"hidden\" name=\"csrfToken\" value=\"{{ pimcore_csrf.getCsrfToken(app.request.session) }}\">

            <button type=\"submit\" name=\"submit\">{{ 'submit'|trans([],'admin') }}</button>
        </form>
    {% endif %}

    <a href=\"{{ path('pimcore_admin_login') }}\">{{ 'Back to Login'|trans([],'admin') }}</a>

    {{ pimcore_breach_attack_random_content() }}
{% endblock %}


", "@PimcoreAdmin/admin/login/lost_password.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/login/lost_password.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 5);
        static $filters = array("escape" => 8, "trans" => 8);
        static $functions = array("path" => 25, "pimcore_breach_attack_random_content" => 35);

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape', 'trans'],
                ['path', 'pimcore_breach_attack_random_content']
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
