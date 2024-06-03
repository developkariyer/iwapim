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

/* @PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig */
class __TwigTemplate_4fe1e2697b90c5b6c1d77a12bf32dc9c extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig"));

        // line 2
        $context["language"] = twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 2, $this->source); })()), "getProperty", ["language"], "method", false, false, true, 2);
        // line 3
        echo "<div class=\"data-table ";
        (((array_key_exists("cls", $context) &&  !(null === (isset($context["cls"]) || array_key_exists("cls", $context) ? $context["cls"] : (function () { throw new RuntimeError('Variable "cls" does not exist.', 3, $this->source); })())))) ? (print (twig_escape_filter($this->env, (isset($context["cls"]) || array_key_exists("cls", $context) ? $context["cls"] : (function () { throw new RuntimeError('Variable "cls" does not exist.', 3, $this->source); })()), "html", null, true))) : (print ("")));
        echo "\">
    <table>
        ";
        // line 5
        if ($this->env->getTest('instanceof')->getCallable()((isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 5, $this->source); })()), "\\Pimcore\\Model\\DataObject\\Concrete")) {
            // line 6
            echo "            <tr>
                <th>";
            // line 7
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("class", [], "admin"), "html", null, true);
            echo "</th>
                <td>";
            // line 8
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 8, $this->source); })()), "getClassName", [], "method", false, false, true, 8), 8, $this->source), "html", null, true);
            echo " [";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 8, $this->source); })()), "getClassId", [], "method", false, false, true, 8), 8, $this->source), "html", null, true);
            echo "]</td>
            </tr>
        ";
        }
        // line 11
        echo "
        ";
        // line 12
        if ($this->env->getTest('instanceof')->getCallable()((isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 12, $this->source); })()), "\\Pimcore\\Model\\Asset")) {
            // line 13
            echo "            <tr>
                <th>";
            // line 14
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("mimetype", [], "admin"), "html", null, true);
            echo "</th>
                <td>";
            // line 15
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 15, $this->source); })()), "getMimeType", [], "method", false, false, true, 15), 15, $this->source), "html", null, true);
            echo "</td>
            </tr>
        ";
        }
        // line 18
        echo "
        ";
        // line 19
        if ( !twig_test_empty((isset($context["language"]) || array_key_exists("language", $context) ? $context["language"] : (function () { throw new RuntimeError('Variable "language" does not exist.', 19, $this->source); })()))) {
            // line 20
            echo "            <tr>
                <th>";
            // line 21
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("language", [], "admin"), "html", null, true);
            echo "</th>
                <td style=\"padding-left: 40px; background: url(";
            // line 22
            echo twig_escape_filter($this->env, Pimcore\Bundle\AdminBundle\Tool::getLanguageFlagFile($this->sandbox->ensureToStringAllowed((isset($context["language"]) || array_key_exists("language", $context) ? $context["language"] : (function () { throw new RuntimeError('Variable "language" does not exist.', 22, $this->source); })()), 22, $this->source), false), "html", null, true);
            echo ") left top no-repeat; background-size: 31px 21px;\">
                    ";
            // line 23
            $context["locales"] = Pimcore\Tool::getSupportedLocales();
            // line 24
            echo "                    ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["locales"]) || array_key_exists("locales", $context) ? $context["locales"] : (function () { throw new RuntimeError('Variable "locales" does not exist.', 24, $this->source); })()), (isset($context["language"]) || array_key_exists("language", $context) ? $context["language"] : (function () { throw new RuntimeError('Variable "language" does not exist.', 24, $this->source); })()), [], "array", false, false, true, 24), 24, $this->source), "html", null, true);
            echo "
                </td>
            </tr>
        ";
        }
        // line 28
        echo "
        ";
        // line 29
        if ($this->env->getTest('instanceof')->getCallable()((isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 29, $this->source); })()), "\\Pimcore\\Model\\Document\\Page")) {
            // line 30
            echo "            ";
            if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 30, $this->source); })()), "title", [], "any", false, false, true, 30))) {
                // line 31
                echo "            <tr>
                <th>";
                // line 32
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("title", [], "admin"), "html", null, true);
                echo "</th>
                <td>";
                // line 33
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 33, $this->source); })()), "title", [], "any", false, false, true, 33), 33, $this->source), "html", null, true);
                echo "</td>
            </tr>
            ";
            }
            // line 36
            echo "
            ";
            // line 37
            if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 37, $this->source); })()), "description", [], "any", false, false, true, 37))) {
                // line 38
                echo "                <tr>
                    <th>";
                // line 39
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("description", [], "admin"), "html", null, true);
                echo "</th>
                    <td>";
                // line 40
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 40, $this->source); })()), "description", [], "any", false, false, true, 40), 40, $this->source), "html", null, true);
                echo "</td>
                </tr>
            ";
            }
            // line 43
            echo "
            ";
            // line 44
            if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 44, $this->source); })()), "getProperty", ["navigation_name"], "method", false, false, true, 44))) {
                // line 45
                echo "                <tr>
                    <th>";
                // line 46
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("name", [], "admin"), "html", null, true);
                echo "</th>
                    <td>";
                // line 47
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 47, $this->source); })()), "getProperty", ["navigation_name"], "method", false, false, true, 47), 47, $this->source), "html", null, true);
                echo "</td>
                </tr>
            ";
            }
            // line 50
            echo "        ";
        }
        // line 51
        echo "
        ";
        // line 52
        $context["userOwnerId"] = twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 52, $this->source); })()), "getUserOwner", [], "method", false, false, true, 52);
        // line 53
        echo "        ";
        $context["owner"] = (( !(null === (isset($context["userOwnerId"]) || array_key_exists("userOwnerId", $context) ? $context["userOwnerId"] : (function () { throw new RuntimeError('Variable "userOwnerId" does not exist.', 53, $this->source); })()))) ? (Pimcore\Model\User::getById($this->sandbox->ensureToStringAllowed((isset($context["userOwnerId"]) || array_key_exists("userOwnerId", $context) ? $context["userOwnerId"] : (function () { throw new RuntimeError('Variable "userOwnerId" does not exist.', 53, $this->source); })()), 53, $this->source))) : (null));
        // line 54
        echo "        ";
        if ($this->env->getTest('instanceof')->getCallable()((isset($context["owner"]) || array_key_exists("owner", $context) ? $context["owner"] : (function () { throw new RuntimeError('Variable "owner" does not exist.', 54, $this->source); })()), "\\Pimcore\\Model\\User")) {
            // line 55
            echo "            <tr>
                <th>";
            // line 56
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("owner", [], "admin"), "html", null, true);
            echo "</th>
                <td>";
            // line 57
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["owner"]) || array_key_exists("owner", $context) ? $context["owner"] : (function () { throw new RuntimeError('Variable "owner" does not exist.', 57, $this->source); })()), "name", [], "any", false, false, true, 57), 57, $this->source), "html", null, true);
            echo "</td>
            </tr>
        ";
        }
        // line 60
        echo "
        ";
        // line 61
        $context["userModificationId"] = twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 61, $this->source); })()), "getUserModification", [], "method", false, false, true, 61);
        // line 62
        echo "        ";
        $context["editor"] = (( !(null === (isset($context["userModificationId"]) || array_key_exists("userModificationId", $context) ? $context["userModificationId"] : (function () { throw new RuntimeError('Variable "userModificationId" does not exist.', 62, $this->source); })()))) ? (Pimcore\Model\User::getById($this->sandbox->ensureToStringAllowed((isset($context["userModificationId"]) || array_key_exists("userModificationId", $context) ? $context["userModificationId"] : (function () { throw new RuntimeError('Variable "userModificationId" does not exist.', 62, $this->source); })()), 62, $this->source))) : (null));
        // line 63
        echo "        ";
        if ($this->env->getTest('instanceof')->getCallable()((isset($context["editor"]) || array_key_exists("editor", $context) ? $context["editor"] : (function () { throw new RuntimeError('Variable "editor" does not exist.', 63, $this->source); })()), "\\Pimcore\\Model\\User")) {
            // line 64
            echo "            <tr>
                <th>";
            // line 65
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("usermodification", [], "admin"), "html", null, true);
            echo "</th>
                <td>";
            // line 66
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["editor"]) || array_key_exists("editor", $context) ? $context["editor"] : (function () { throw new RuntimeError('Variable "editor" does not exist.', 66, $this->source); })()), "name", [], "any", false, false, true, 66), 66, $this->source), "html", null, true);
            echo "</td>
            </tr>
        ";
        }
        // line 69
        echo "
        <tr>
            <th>";
        // line 71
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("creationdate", [], "admin"), "html", null, true);
        echo "</th>
            <td>";
        // line 72
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 72, $this->source); })()), "getCreationDate", [], "method", false, false, true, 72), 72, $this->source), "Y-m-d H:i"), "html", null, true);
        echo "</td>
        </tr>
        <tr>
            <th>";
        // line 75
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("modificationdate", [], "admin"), "html", null, true);
        echo "</th>
            <td>";
        // line 76
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 76, $this->source); })()), "getModificationDate", [], "method", false, false, true, 76), 76, $this->source), "Y-m-d H:i"), "html", null, true);
        echo "</td>
        </tr>
    </table>
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
        return "@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig";
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
        return array (  240 => 76,  236 => 75,  230 => 72,  226 => 71,  222 => 69,  216 => 66,  212 => 65,  209 => 64,  206 => 63,  203 => 62,  201 => 61,  198 => 60,  192 => 57,  188 => 56,  185 => 55,  182 => 54,  179 => 53,  177 => 52,  174 => 51,  171 => 50,  165 => 47,  161 => 46,  158 => 45,  156 => 44,  153 => 43,  147 => 40,  143 => 39,  140 => 38,  138 => 37,  135 => 36,  129 => 33,  125 => 32,  122 => 31,  119 => 30,  117 => 29,  114 => 28,  106 => 24,  104 => 23,  100 => 22,  96 => 21,  93 => 20,  91 => 19,  88 => 18,  82 => 15,  78 => 14,  75 => 13,  73 => 12,  70 => 11,  62 => 8,  58 => 7,  55 => 6,  53 => 5,  47 => 3,  45 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("{# @var \$element \\Pimcore\\Model\\Element\\AbstractElement #}
{% set language = element.getProperty('language') %}
<div class=\"data-table {{ cls ?? '' }}\">
    <table>
        {% if element is instanceof ('\\\\Pimcore\\\\Model\\\\DataObject\\\\Concrete') %}
            <tr>
                <th>{{ 'class'|trans([],'admin') }}</th>
                <td>{{ element.getClassName() }} [{{ element.getClassId() }}]</td>
            </tr>
        {% endif %}

        {% if element is instanceof ('\\\\Pimcore\\\\Model\\\\Asset') %}
            <tr>
                <th>{{ 'mimetype'|trans([],'admin') }}</th>
                <td>{{ element.getMimeType() }}</td>
            </tr>
        {% endif %}

        {% if language is not empty %}
            <tr>
                <th>{{ 'language'|trans([],'admin') }}</th>
                <td style=\"padding-left: 40px; background: url({{ pimcore_language_flag(language, false) }}) left top no-repeat; background-size: 31px 21px;\">
                    {% set locales = pimcore_supported_locales() %}
                    {{ locales[language] }}
                </td>
            </tr>
        {% endif %}

        {% if element is instanceof('\\\\Pimcore\\\\Model\\\\Document\\\\Page') %}
            {% if element.title is not empty %}
            <tr>
                <th>{{ 'title'|trans([],'admin') }}</th>
                <td>{{ element.title }}</td>
            </tr>
            {% endif %}

            {% if element.description is not empty %}
                <tr>
                    <th>{{ 'description'|trans([],'admin') }}</th>
                    <td>{{ element.description }}</td>
                </tr>
            {% endif %}

            {% if element.getProperty('navigation_name') is not empty %}
                <tr>
                    <th>{{ 'name'|trans([],'admin') }}</th>
                    <td>{{ element.getProperty('navigation_name') }}</td>
                </tr>
            {% endif %}
        {% endif %}

        {% set userOwnerId = element.getUserOwner() %}
        {% set owner = userOwnerId is not null ? pimcore_user(userOwnerId) : null %}
        {% if owner is instanceof('\\\\Pimcore\\\\Model\\\\User') %}
            <tr>
                <th>{{ 'owner'|trans([],'admin') }}</th>
                <td>{{ owner.name }}</td>
            </tr>
        {% endif %}

        {% set userModificationId = element.getUserModification() %}
        {% set editor = userModificationId is not null ? pimcore_user(userModificationId) : null %}
        {% if editor is instanceof('\\\\Pimcore\\\\Model\\\\User') %}
            <tr>
                <th>{{ 'usermodification'|trans([],'admin') }}</th>
                <td>{{ editor.name }}</td>
            </tr>
        {% endif %}

        <tr>
            <th>{{ 'creationdate'|trans([],'admin') }}</th>
            <td>{{ element.getCreationDate()|date('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <th>{{ 'modificationdate'|trans([],'admin') }}</th>
            <td>{{ element.getModificationDate()|date('Y-m-d H:i') }}</td>
        </tr>
    </table>
</div>
", "@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/searchadmin/search/quicksearch/info_table.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 2, "if" => 5);
        static $filters = array("escape" => 3, "trans" => 7, "date" => 72);
        static $functions = array("pimcore_language_flag" => 22, "pimcore_supported_locales" => 23, "pimcore_user" => 53);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['escape', 'trans', 'date'],
                ['pimcore_language_flag', 'pimcore_supported_locales', 'pimcore_user']
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
