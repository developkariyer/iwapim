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

/* @PimcoreAdmin/admin/data_object/data_object/diff_versions.html.twig */
class __TwigTemplate_5fd85ffcddb7482cb567eb1dccad7c2a extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/data_object/data_object/diff_versions.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/data_object/data_object/diff_versions.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"/bundles/pimcoreadmin/css/object_versions.css\"/>
</head>

<body>

";
        // line 10
        $context["fields"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 10, $this->source); })()), "getClass", [], "method", false, false, true, 10), "getFieldDefinitions", [], "method", false, false, true, 10);
        // line 11
        echo "
<table class=\"preview\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
    <tr>
        <th>Name</th>
        <th>Key</th>
        ";
        // line 16
        if (array_key_exists("isImportPreview", $context)) {
            // line 17
            echo "            ";
            if (array_key_exists("isNew", $context)) {
                // line 18
                echo "                <th>New Object or unable to resolve</th>
            ";
            } else {
                // line 20
                echo "                <th>Before</th>
                <th>After</th>
            ";
            }
            // line 23
            echo "        ";
        } else {
            // line 24
            echo "            <th>Version 1</th>
            <th>Version 2</th>
        ";
        }
        // line 27
        echo "    </tr>
    <tr class=\"system\">
        <td>Date</td>
        <td>modificationDate</td>
        ";
        // line 31
        if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
            // line 32
            echo "            <td>";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 32, $this->source); })()), "getModificationDate", [], "method", false, false, true, 32), 32, $this->source), "Y-m-d H:i:s"), "html", null, true);
            echo "</td>
        ";
        }
        // line 34
        echo "        <td>";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 34, $this->source); })()), "getModificationDate", [], "method", false, false, true, 34), 34, $this->source), "Y-m-d H:i:s"), "html", null, true);
        echo "</td>
    </tr>
    <tr class=\"system\">
        <td>Path</td>
        <td>path</td>
        ";
        // line 39
        if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
            // line 40
            echo "            <td> ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 40, $this->source); })()), "getRealFullPath", [], "method", false, false, true, 40), 40, $this->source), "html", null, true);
            echo " </td>
        ";
        }
        // line 42
        echo "        ";
        $context["modifiedPathClass"] = (( !(twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 42, $this->source); })()), "getRealFullPath", [], "method", false, false, true, 42) === twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 42, $this->source); })()), "getRealFullPath", [], "method", false, false, true, 42))) ? ("modified") : (""));
        // line 43
        echo "        <td class=\"";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["modifiedPathClass"]) || array_key_exists("modifiedPathClass", $context) ? $context["modifiedPathClass"] : (function () { throw new RuntimeError('Variable "modifiedPathClass" does not exist.', 43, $this->source); })()), 43, $this->source), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 43, $this->source); })()), "getRealFullPath", [], "method", false, false, true, 43), 43, $this->source), "html", null, true);
        echo "</td>
    </tr>
    <tr class=\"system\">
        <td>Published</td>
        <td>published</td>
        ";
        // line 48
        if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
            // line 49
            echo "            <td> ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 49, $this->source); })()), "getPublished", [], "method", false, false, true, 49), 49, $this->source), "html", null, true);
            echo " </td>
        ";
        }
        // line 51
        echo "        ";
        $context["modifiedPublishedClass"] = (( !(twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 51, $this->source); })()), "getPublished", [], "method", false, false, true, 51) === twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 51, $this->source); })()), "getPublished", [], "method", false, false, true, 51))) ? ("modified") : (""));
        // line 52
        echo "        <td class=\"";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["modifiedPublishedClass"]) || array_key_exists("modifiedPublishedClass", $context) ? $context["modifiedPublishedClass"] : (function () { throw new RuntimeError('Variable "modifiedPublishedClass" does not exist.', 52, $this->source); })()), 52, $this->source), "html", null, true);
        echo "\">";
        echo ((twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 52, $this->source); })()), "getPublished", [], "method", false, false, true, 52)) ? ("true") : ("false"));
        echo "</td>
    </tr>
    ";
        // line 54
        if (((isset($context["versionNote1"]) || array_key_exists("versionNote1", $context) ? $context["versionNote1"] : (function () { throw new RuntimeError('Variable "versionNote1" does not exist.', 54, $this->source); })()) || (isset($context["versionNote2"]) || array_key_exists("versionNote2", $context) ? $context["versionNote2"] : (function () { throw new RuntimeError('Variable "versionNote2" does not exist.', 54, $this->source); })()))) {
            // line 55
            echo "        <tr class=\"system\">
            <td>Version Note</td>
            <td>&nbsp;</td>
            <td>";
            // line 58
            echo twig_nl2br(twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["versionNote1"]) || array_key_exists("versionNote1", $context) ? $context["versionNote1"] : (function () { throw new RuntimeError('Variable "versionNote1" does not exist.', 58, $this->source); })()), 58, $this->source), "html", null, true));
            echo "</td>
            <td>";
            // line 59
            echo twig_nl2br(twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["versionNote2"]) || array_key_exists("versionNote2", $context) ? $context["versionNote2"] : (function () { throw new RuntimeError('Variable "versionNote2" does not exist.', 59, $this->source); })()), 59, $this->source), "html", null, true));
            echo "</td>
        </tr>
    ";
        }
        // line 62
        echo "    <tr class=\"system\">
        <td>Id</td>
        <td>id</td>
        ";
        // line 65
        if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
            // line 66
            echo "            <td> ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 66, $this->source); })()), "getId", [], "method", false, false, true, 66), 66, $this->source), "html", null, true);
            echo " </td>
        ";
        }
        // line 68
        echo "        <td>";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 68, $this->source); })()), "getId", [], "method", false, false, true, 68), 68, $this->source), "html", null, true);
        echo "</td>
    </tr>


    <tr class=\"\">
        <td colspan=\"3\">&nbsp;</td>
    </tr>

    ";
        // line 76
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["fields"]) || array_key_exists("fields", $context) ? $context["fields"] : (function () { throw new RuntimeError('Variable "fields" does not exist.', 76, $this->source); })()));
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
        foreach ($context['_seq'] as $context["fieldName"] => $context["definition"]) {
            // line 77
            echo "        ";
            if ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Localizedfields")) {
                // line 78
                echo "            ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 78, $this->source); })()));
                foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                    // line 79
                    echo "                ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["definition"], "getFieldDefinitions", [], "method", false, false, true, 79));
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
                    foreach ($context['_seq'] as $context["_key"] => $context["lfd"]) {
                        // line 80
                        echo "                    ";
                        $context["vlContainer"] = twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 80, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 80);
                        // line 81
                        echo "                    ";
                        $context["keyData1"] = (( !twig_test_empty((isset($context["vlContainer"]) || array_key_exists("vlContainer", $context) ? $context["vlContainer"] : (function () { throw new RuntimeError('Variable "vlContainer" does not exist.', 81, $this->source); })()))) ? (twig_get_attribute($this->env, $this->source, (isset($context["vlContainer"]) || array_key_exists("vlContainer", $context) ? $context["vlContainer"] : (function () { throw new RuntimeError('Variable "vlContainer" does not exist.', 81, $this->source); })()), "getLocalizedValue", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 81), $context["language"]], "method", false, false, true, 81)) : (null));
                        // line 82
                        echo "                    ";
                        $context["v1"] = twig_get_attribute($this->env, $this->source, $context["lfd"], "getVersionPreview", [(isset($context["keyData1"]) || array_key_exists("keyData1", $context) ? $context["keyData1"] : (function () { throw new RuntimeError('Variable "keyData1" does not exist.', 82, $this->source); })())], "method", false, false, true, 82);
                        // line 83
                        echo "
                    ";
                        // line 84
                        $context["v2Container"] = twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 84, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 84);
                        // line 85
                        echo "                    ";
                        $context["keyData2"] = (( !twig_test_empty((isset($context["v2Container"]) || array_key_exists("v2Container", $context) ? $context["v2Container"] : (function () { throw new RuntimeError('Variable "v2Container" does not exist.', 85, $this->source); })()))) ? (twig_get_attribute($this->env, $this->source, (isset($context["v2Container"]) || array_key_exists("v2Container", $context) ? $context["v2Container"] : (function () { throw new RuntimeError('Variable "v2Container" does not exist.', 85, $this->source); })()), "getLocalizedValue", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 85), $context["language"]], "method", false, false, true, 85)) : (null));
                        // line 86
                        echo "                    ";
                        $context["v2"] = (((isset($context["v2Container"]) || array_key_exists("v2Container", $context) ? $context["v2Container"] : (function () { throw new RuntimeError('Variable "v2Container" does not exist.', 86, $this->source); })())) ? (twig_get_attribute($this->env, $this->source, $context["lfd"], "getVersionPreview", [(isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 86, $this->source); })())], "method", false, false, true, 86)) : (""));
                        // line 87
                        echo "
                    <tr ";
                        // line 88
                        if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 88) % 2 != 0)) {
                            echo "class=\"odd\"";
                        }
                        echo ">
                    <td>";
                        // line 89
                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 89), 89, $this->source), [], "admin"), "html", null, true);
                        echo " (";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 89, $this->source), "html", null, true);
                        echo ")</td>
                    <td>";
                        // line 90
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 90), 90, $this->source), "html", null, true);
                        echo "</td>
                        ";
                        // line 91
                        if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
                            // line 92
                            echo "                        <td>";
                            echo $this->sandbox->ensureToStringAllowed((isset($context["v1"]) || array_key_exists("v1", $context) ? $context["v1"] : (function () { throw new RuntimeError('Variable "v1" does not exist.', 92, $this->source); })()), 92, $this->source);
                            echo "</td>
                    ";
                        }
                        // line 94
                        echo "                        ";
                        $context["fieldIsEqual"] = ((($this->env->getTest('instanceof')->getCallable()($context["lfd"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\EqualComparisonInterface") &&  !twig_get_attribute($this->env, $this->source, $context["lfd"], "isEqual", [(isset($context["keyData1"]) || array_key_exists("keyData1", $context) ? $context["keyData1"] : (function () { throw new RuntimeError('Variable "keyData1" does not exist.', 94, $this->source); })()), (isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 94, $this->source); })())], "method", false, false, true, 94))) ? ("modified") : (""));
                        // line 95
                        echo "                        <td class=\"";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["fieldIsEqual"]) || array_key_exists("fieldIsEqual", $context) ? $context["fieldIsEqual"] : (function () { throw new RuntimeError('Variable "fieldIsEqual" does not exist.', 95, $this->source); })()), 95, $this->source), "html", null, true);
                        echo "\">";
                        echo $this->sandbox->ensureToStringAllowed((isset($context["v2"]) || array_key_exists("v2", $context) ? $context["v2"] : (function () { throw new RuntimeError('Variable "v2" does not exist.', 95, $this->source); })()), 95, $this->source);
                        echo "</td>
                    </tr>
                ";
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
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['lfd'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 98
                    echo "            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 99
                echo "        ";
            } elseif ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Classificationstore")) {
                // line 100
                echo "            ";
                $context["storedata1"] = twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 100, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 100);
                // line 101
                echo "            ";
                $context["storedata2"] = twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 101, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 101);
                // line 102
                echo "
            ";
                // line 103
                $context["existingGroups"] = [];
                // line 104
                echo "
            ";
                // line 105
                $context["activeGroups1"] = [];
                // line 106
                echo "            ";
                if ((isset($context["storedata1"]) || array_key_exists("storedata1", $context) ? $context["storedata1"] : (function () { throw new RuntimeError('Variable "storedata1" does not exist.', 106, $this->source); })())) {
                    // line 107
                    echo "                ";
                    $context["activeGroups1"] = twig_get_attribute($this->env, $this->source, (isset($context["storedata1"]) || array_key_exists("storedata1", $context) ? $context["storedata1"] : (function () { throw new RuntimeError('Variable "storedata1" does not exist.', 107, $this->source); })()), "getActiveGroups", [], "method", false, false, true, 107);
                    // line 108
                    echo "            ";
                }
                // line 109
                echo "
            ";
                // line 110
                $context["activeGroups2"] = [];
                // line 111
                echo "            ";
                if ((isset($context["storedata2"]) || array_key_exists("storedata2", $context) ? $context["storedata2"] : (function () { throw new RuntimeError('Variable "storedata2" does not exist.', 111, $this->source); })())) {
                    // line 112
                    echo "                ";
                    $context["activeGroups2"] = twig_get_attribute($this->env, $this->source, (isset($context["storedata2"]) || array_key_exists("storedata2", $context) ? $context["storedata2"] : (function () { throw new RuntimeError('Variable "storedata2" does not exist.', 112, $this->source); })()), "getActiveGroups", [], "method", false, false, true, 112);
                    // line 113
                    echo "            ";
                }
                // line 114
                echo "
            ";
                // line 115
                $context["activeGroups"] = ((isset($context["activeGroups1"]) || array_key_exists("activeGroups1", $context) ? $context["activeGroups1"] : (function () { throw new RuntimeError('Variable "activeGroups1" does not exist.', 115, $this->source); })()) + (isset($context["activeGroups2"]) || array_key_exists("activeGroups2", $context) ? $context["activeGroups2"] : (function () { throw new RuntimeError('Variable "activeGroups2" does not exist.', 115, $this->source); })()));
                // line 116
                echo "
            ";
                // line 117
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["activeGroups"]) || array_key_exists("activeGroups", $context) ? $context["activeGroups"] : (function () { throw new RuntimeError('Variable "activeGroups" does not exist.', 117, $this->source); })()));
                foreach ($context['_seq'] as $context["activeGroupId"] => $context["enabled"]) {
                    // line 118
                    echo "                ";
                    $context["existingGroups"] = ((isset($context["existingGroups"]) || array_key_exists("existingGroups", $context) ? $context["existingGroups"] : (function () { throw new RuntimeError('Variable "existingGroups" does not exist.', 118, $this->source); })()) + [$context["activeGroupId"] => $context["enabled"]]);
                    // line 119
                    echo "            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['activeGroupId'], $context['enabled'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 120
                echo "
            ";
                // line 121
                if ( !twig_test_empty((isset($context["existingGroups"]) || array_key_exists("existingGroups", $context) ? $context["existingGroups"] : (function () { throw new RuntimeError('Variable "existingGroups" does not exist.', 121, $this->source); })()))) {
                    // line 122
                    echo "                ";
                    $context["languages"] = ["default"];
                    // line 123
                    echo "                ";
                    if (twig_get_attribute($this->env, $this->source, $context["definition"], "isLocalized", [], "method", false, false, true, 123)) {
                        // line 124
                        echo "                    ";
                        $context["languages"] = twig_array_merge($this->sandbox->ensureToStringAllowed((isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 124, $this->source); })()), 124, $this->source), $this->sandbox->ensureToStringAllowed((isset($context["languages"]) || array_key_exists("languages", $context) ? $context["languages"] : (function () { throw new RuntimeError('Variable "languages" does not exist.', 124, $this->source); })()), 124, $this->source));
                        // line 125
                        echo "                ";
                    }
                    // line 126
                    echo "
                ";
                    // line 127
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable((isset($context["existingGroups"]) || array_key_exists("existingGroups", $context) ? $context["existingGroups"] : (function () { throw new RuntimeError('Variable "existingGroups" does not exist.', 127, $this->source); })()));
                    foreach ($context['_seq'] as $context["activeGroupId"] => $context["enabled"]) {
                        // line 128
                        echo "                    ";
                        if ((twig_get_attribute($this->env, $this->source, ($context["activeGroups1"] ?? null), $context["activeGroupId"], [], "array", true, true, true, 128) && twig_get_attribute($this->env, $this->source, ($context["activeGroups2"] ?? null), $context["activeGroupId"], [], "array", true, true, true, 128))) {
                            // line 129
                            echo "                        ";
                            $context["groupDefinition"] = Pimcore\Model\DataObject\Classificationstore\GroupConfig::getById($this->sandbox->ensureToStringAllowed($context["activeGroupId"], 129, $this->source));
                            // line 130
                            echo "                        ";
                            if ( !twig_test_empty((isset($context["groupDefinition"]) || array_key_exists("groupDefinition", $context) ? $context["groupDefinition"] : (function () { throw new RuntimeError('Variable "groupDefinition" does not exist.', 130, $this->source); })()))) {
                                // line 131
                                echo "                            ";
                                $context["keyGroupRelations"] = twig_get_attribute($this->env, $this->source, (isset($context["groupDefinition"]) || array_key_exists("groupDefinition", $context) ? $context["groupDefinition"] : (function () { throw new RuntimeError('Variable "groupDefinition" does not exist.', 131, $this->source); })()), "getRelations", [], "method", false, false, true, 131);
                                // line 132
                                echo "
                            ";
                                // line 133
                                $context['_parent'] = $context;
                                $context['_seq'] = twig_ensure_traversable((isset($context["keyGroupRelations"]) || array_key_exists("keyGroupRelations", $context) ? $context["keyGroupRelations"] : (function () { throw new RuntimeError('Variable "keyGroupRelations" does not exist.', 133, $this->source); })()));
                                foreach ($context['_seq'] as $context["_key"] => $context["keyGroupRelation"]) {
                                    // line 134
                                    echo "                                ";
                                    $context["keyDef"] = $this->extensions['Pimcore\Twig\Extension\PimcoreObjectExtension']->getFieldDefinitionFromJson($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getDefinition", [], "method", false, false, true, 134), 134, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getType", [], "method", false, false, true, 134), 134, $this->source));
                                    // line 135
                                    echo "
                                ";
                                    // line 136
                                    if ( !twig_test_empty((isset($context["keyDef"]) || array_key_exists("keyDef", $context) ? $context["keyDef"] : (function () { throw new RuntimeError('Variable "keyDef" does not exist.', 136, $this->source); })()))) {
                                        // line 137
                                        echo "                                    ";
                                        $context['_parent'] = $context;
                                        $context['_seq'] = twig_ensure_traversable((isset($context["languages"]) || array_key_exists("languages", $context) ? $context["languages"] : (function () { throw new RuntimeError('Variable "languages" does not exist.', 137, $this->source); })()));
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
                                        foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                                            // line 138
                                            echo "                                        ";
                                            $context["keyData1"] = (((isset($context["storedata1"]) || array_key_exists("storedata1", $context) ? $context["storedata1"] : (function () { throw new RuntimeError('Variable "storedata1" does not exist.', 138, $this->source); })())) ? (twig_get_attribute($this->env, $this->source, (isset($context["storedata1"]) || array_key_exists("storedata1", $context) ? $context["storedata1"] : (function () { throw new RuntimeError('Variable "storedata1" does not exist.', 138, $this->source); })()), "getLocalizedKeyValue", [$context["activeGroupId"], twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getKeyId", [], "method", false, false, true, 138), $context["language"], true, true], "method", false, false, true, 138)) : (null));
                                            // line 139
                                            echo "                                        ";
                                            $context["preview1"] = twig_get_attribute($this->env, $this->source, (isset($context["keyDef"]) || array_key_exists("keyDef", $context) ? $context["keyDef"] : (function () { throw new RuntimeError('Variable "keyDef" does not exist.', 139, $this->source); })()), "getVersionPreview", [(isset($context["keyData1"]) || array_key_exists("keyData1", $context) ? $context["keyData1"] : (function () { throw new RuntimeError('Variable "keyData1" does not exist.', 139, $this->source); })())], "method", false, false, true, 139);
                                            // line 140
                                            echo "                                        ";
                                            $context["keyData2"] = (((isset($context["storedata2"]) || array_key_exists("storedata2", $context) ? $context["storedata2"] : (function () { throw new RuntimeError('Variable "storedata2" does not exist.', 140, $this->source); })())) ? (twig_get_attribute($this->env, $this->source, (isset($context["storedata2"]) || array_key_exists("storedata2", $context) ? $context["storedata2"] : (function () { throw new RuntimeError('Variable "storedata2" does not exist.', 140, $this->source); })()), "getLocalizedKeyValue", [$context["activeGroupId"], twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getKeyId", [], "method", false, false, true, 140), $context["language"], true, true], "method", false, false, true, 140)) : (null));
                                            // line 141
                                            echo "                                        ";
                                            $context["preview2"] = twig_get_attribute($this->env, $this->source, (isset($context["keyDef"]) || array_key_exists("keyDef", $context) ? $context["keyDef"] : (function () { throw new RuntimeError('Variable "keyDef" does not exist.', 141, $this->source); })()), "getVersionPreview", [(isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 141, $this->source); })())], "method", false, false, true, 141);
                                            // line 142
                                            echo "
                                        <tr ";
                                            // line 143
                                            if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 143) % 2 != 0)) {
                                                echo "class=\"odd\"";
                                            }
                                            echo ">
                                            <td>";
                                            // line 144
                                            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getTitle", [], "method", false, false, true, 144), 144, $this->source), [], "admin"), "html", null, true);
                                            echo "</td>
                                            <td>";
                                            // line 145
                                            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["groupDefinition"]) || array_key_exists("groupDefinition", $context) ? $context["groupDefinition"] : (function () { throw new RuntimeError('Variable "groupDefinition" does not exist.', 145, $this->source); })()), "getName", [], "method", false, false, true, 145), 145, $this->source), "html", null, true);
                                            echo " - ";
                                            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getName", [], "method", false, false, true, 145), 145, $this->source), "html", null, true);
                                            echo " ";
                                            ((twig_get_attribute($this->env, $this->source, $context["definition"], "isLocalized", [], "method", false, false, true, 145)) ? (print (twig_escape_filter($this->env, ("/ " . $context["language"]), "html", null, true))) : (print ("")));
                                            echo "</td>
                                            ";
                                            // line 146
                                            if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
                                                // line 147
                                                echo "                                                <td>";
                                                echo $this->sandbox->ensureToStringAllowed((isset($context["preview1"]) || array_key_exists("preview1", $context) ? $context["preview1"] : (function () { throw new RuntimeError('Variable "preview1" does not exist.', 147, $this->source); })()), 147, $this->source);
                                                echo "</td>
                                            ";
                                            }
                                            // line 149
                                            echo "                                            ";
                                            $context["fieldIsEqual"] = ((($this->env->getTest('instanceof')->getCallable()((isset($context["keyDef"]) || array_key_exists("keyDef", $context) ? $context["keyDef"] : (function () { throw new RuntimeError('Variable "keyDef" does not exist.', 149, $this->source); })()), "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\EqualComparisonInterface") &&  !twig_get_attribute($this->env, $this->source, (isset($context["keyDef"]) || array_key_exists("keyDef", $context) ? $context["keyDef"] : (function () { throw new RuntimeError('Variable "keyDef" does not exist.', 149, $this->source); })()), "isEqual", [(isset($context["keyData1"]) || array_key_exists("keyData1", $context) ? $context["keyData1"] : (function () { throw new RuntimeError('Variable "keyData1" does not exist.', 149, $this->source); })()), (isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 149, $this->source); })())], "method", false, false, true, 149))) ? ("modified") : (""));
                                            // line 150
                                            echo "                                            <td class=\"";
                                            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["fieldIsEqual"]) || array_key_exists("fieldIsEqual", $context) ? $context["fieldIsEqual"] : (function () { throw new RuntimeError('Variable "fieldIsEqual" does not exist.', 150, $this->source); })()), 150, $this->source), "html", null, true);
                                            echo "\">";
                                            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["preview2"]) || array_key_exists("preview2", $context) ? $context["preview2"] : (function () { throw new RuntimeError('Variable "preview2" does not exist.', 150, $this->source); })()), 150, $this->source), "html", null, true);
                                            echo "</td>
                                        </tr>
                                    ";
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
                                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
                                        $context = array_intersect_key($context, $_parent) + $_parent;
                                        // line 153
                                        echo "                                ";
                                    }
                                    // line 154
                                    echo "                            ";
                                }
                                $_parent = $context['_parent'];
                                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['keyGroupRelation'], $context['_parent'], $context['loop']);
                                $context = array_intersect_key($context, $_parent) + $_parent;
                                // line 155
                                echo "                        ";
                            }
                            // line 156
                            echo "                    ";
                        }
                        // line 157
                        echo "                ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['activeGroupId'], $context['enabled'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 158
                    echo "            ";
                }
                // line 159
                echo "        ";
            } elseif ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Objectbricks")) {
                // line 160
                echo "            ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["definition"], "getAllowedTypes", [], "method", false, false, true, 160));
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
                foreach ($context['_seq'] as $context["_key"] => $context["asAllowedType"]) {
                    // line 161
                    echo "                ";
                    $context["collectionDef"] = Pimcore\Model\DataObject\Objectbrick\Definition::getByKey($this->sandbox->ensureToStringAllowed($context["asAllowedType"], 161, $this->source));
                    // line 162
                    echo "
                ";
                    // line 163
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["collectionDef"]) || array_key_exists("collectionDef", $context) ? $context["collectionDef"] : (function () { throw new RuntimeError('Variable "collectionDef" does not exist.', 163, $this->source); })()), "getFieldDefinitions", [], "method", false, false, true, 163));
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
                    foreach ($context['_seq'] as $context["_key"] => $context["lfd"]) {
                        // line 164
                        echo "                    ";
                        $context["value"] = null;
                        // line 165
                        echo "
                    ";
                        // line 166
                        $context["bricks1"] = twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 166, $this->source); })()), "get", [$context["fieldName"]], "method", false, false, true, 166);
                        // line 167
                        echo "                    ";
                        $context["bricks2"] = twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 167, $this->source); })()), "get", [$context["fieldName"]], "method", false, false, true, 167);
                        // line 168
                        echo "
                    ";
                        // line 169
                        if (( !twig_test_empty((isset($context["bricks1"]) || array_key_exists("bricks1", $context) ? $context["bricks1"] : (function () { throw new RuntimeError('Variable "bricks1" does not exist.', 169, $this->source); })())) &&  !twig_test_empty((isset($context["bricks2"]) || array_key_exists("bricks2", $context) ? $context["bricks2"] : (function () { throw new RuntimeError('Variable "bricks2" does not exist.', 169, $this->source); })())))) {
                            // line 170
                            echo "                        ";
                            if ($this->env->getTest('instanceof')->getCallable()($context["lfd"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Localizedfields")) {
                                // line 171
                                echo "                            ";
                                $context['_parent'] = $context;
                                $context['_seq'] = twig_ensure_traversable((isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 171, $this->source); })()));
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
                                foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                                    // line 172
                                    echo "                                ";
                                    $context['_parent'] = $context;
                                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["lfd"], "getFieldDefinitions", [], "method", false, false, true, 172));
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
                                    foreach ($context['_seq'] as $context["_key"] => $context["localizedFieldDefinition"]) {
                                        // line 173
                                        echo "                                    <tr ";
                                        if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 173) % 2 != 0)) {
                                            echo "class=\"odd\"";
                                        }
                                        echo ">
                                        <td>";
                                        // line 174
                                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getTitle", [], "method", false, false, true, 174), 174, $this->source), [], "admin"), "html", null, true);
                                        echo " (";
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 174, $this->source), "html", null, true);
                                        echo ")</td>
                                        <td>";
                                        // line 175
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getName", [], "method", false, false, true, 175), 175, $this->source), "html", null, true);
                                        echo "</td>

                                        ";
                                        // line 177
                                        list($context["v1"], $context["localizedBrickValue1"]) =                                         [null, null];
                                        // line 178
                                        echo "                                        ";
                                        list($context["v2"], $context["localizedBrickValue2"]) =                                         [null, null];
                                        // line 179
                                        echo "
                                        ";
                                        // line 180
                                        if ((isset($context["bricks1"]) || array_key_exists("bricks1", $context) ? $context["bricks1"] : (function () { throw new RuntimeError('Variable "bricks1" does not exist.', 180, $this->source); })())) {
                                            // line 181
                                            echo "                                            ";
                                            $context["brickGetter"] = ("get" . $this->sandbox->ensureToStringAllowed($context["asAllowedType"], 181, $this->source));
                                            // line 182
                                            echo "                                            ";
                                            $context["brick1Value"] = twig_get_attribute($this->env, $this->source, (isset($context["bricks1"]) || array_key_exists("bricks1", $context) ? $context["bricks1"] : (function () { throw new RuntimeError('Variable "bricks1" does not exist.', 182, $this->source); })()), (isset($context["brickGetter"]) || array_key_exists("brickGetter", $context) ? $context["brickGetter"] : (function () { throw new RuntimeError('Variable "brickGetter" does not exist.', 182, $this->source); })()), [], "any", false, false, true, 182);
                                            // line 183
                                            echo "
                                            ";
                                            // line 184
                                            if ((isset($context["brick1Value"]) || array_key_exists("brick1Value", $context) ? $context["brick1Value"] : (function () { throw new RuntimeError('Variable "brick1Value" does not exist.', 184, $this->source); })())) {
                                                // line 185
                                                echo "                                                ";
                                                $context["localizedBrickValues"] = twig_get_attribute($this->env, $this->source, (isset($context["brick1Value"]) || array_key_exists("brick1Value", $context) ? $context["brick1Value"] : (function () { throw new RuntimeError('Variable "brick1Value" does not exist.', 185, $this->source); })()), "getLocalizedFields", [], "method", false, false, true, 185);
                                                // line 186
                                                echo "                                                ";
                                                $context["localizedBrickValue1"] = twig_get_attribute($this->env, $this->source, (isset($context["localizedBrickValues"]) || array_key_exists("localizedBrickValues", $context) ? $context["localizedBrickValues"] : (function () { throw new RuntimeError('Variable "localizedBrickValues" does not exist.', 186, $this->source); })()), "getLocalizedValue", [twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getName", [], "method", false, false, true, 186), $context["language"]], "method", false, false, true, 186);
                                                // line 187
                                                echo "                                                ";
                                                if (((isset($context["localizedBrickValue1"]) || array_key_exists("localizedBrickValue1", $context) ? $context["localizedBrickValue1"] : (function () { throw new RuntimeError('Variable "localizedBrickValue1" does not exist.', 187, $this->source); })()) != false)) {
                                                    // line 188
                                                    echo "                                                    ";
                                                    $context["v1"] = twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getVersionPreview", [(isset($context["localizedBrickValue1"]) || array_key_exists("localizedBrickValue1", $context) ? $context["localizedBrickValue1"] : (function () { throw new RuntimeError('Variable "localizedBrickValue1" does not exist.', 188, $this->source); })())], "method", false, false, true, 188);
                                                    // line 189
                                                    echo "                                                ";
                                                } else {
                                                    // line 190
                                                    echo "                                                    ";
                                                    $context["localizedBrickValue1"] = null;
                                                    // line 191
                                                    echo "                                                ";
                                                }
                                                // line 192
                                                echo "                                            ";
                                            }
                                            // line 193
                                            echo "                                        ";
                                        }
                                        // line 194
                                        echo "
                                        ";
                                        // line 195
                                        if ((isset($context["bricks2"]) || array_key_exists("bricks2", $context) ? $context["bricks2"] : (function () { throw new RuntimeError('Variable "bricks2" does not exist.', 195, $this->source); })())) {
                                            // line 196
                                            echo "                                            ";
                                            $context["brickGetter"] = ("get" . $this->sandbox->ensureToStringAllowed($context["asAllowedType"], 196, $this->source));
                                            // line 197
                                            echo "                                            ";
                                            $context["brick2Value"] = twig_get_attribute($this->env, $this->source, (isset($context["bricks2"]) || array_key_exists("bricks2", $context) ? $context["bricks2"] : (function () { throw new RuntimeError('Variable "bricks2" does not exist.', 197, $this->source); })()), (isset($context["brickGetter"]) || array_key_exists("brickGetter", $context) ? $context["brickGetter"] : (function () { throw new RuntimeError('Variable "brickGetter" does not exist.', 197, $this->source); })()), [], "any", false, false, true, 197);
                                            // line 198
                                            echo "
                                            ";
                                            // line 199
                                            if ((isset($context["brick2Value"]) || array_key_exists("brick2Value", $context) ? $context["brick2Value"] : (function () { throw new RuntimeError('Variable "brick2Value" does not exist.', 199, $this->source); })())) {
                                                // line 200
                                                echo "                                                ";
                                                $context["localizedBrickValues"] = twig_get_attribute($this->env, $this->source, (isset($context["brick2Value"]) || array_key_exists("brick2Value", $context) ? $context["brick2Value"] : (function () { throw new RuntimeError('Variable "brick2Value" does not exist.', 200, $this->source); })()), "getLocalizedFields", [], "method", false, false, true, 200);
                                                // line 201
                                                echo "                                                ";
                                                $context["localizedBrickValue2"] = twig_get_attribute($this->env, $this->source, (isset($context["localizedBrickValues"]) || array_key_exists("localizedBrickValues", $context) ? $context["localizedBrickValues"] : (function () { throw new RuntimeError('Variable "localizedBrickValues" does not exist.', 201, $this->source); })()), "getLocalizedValue", [twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getName", [], "method", false, false, true, 201), $context["language"]], "method", false, false, true, 201);
                                                // line 202
                                                echo "                                                ";
                                                if (((isset($context["localizedBrickValue2"]) || array_key_exists("localizedBrickValue2", $context) ? $context["localizedBrickValue2"] : (function () { throw new RuntimeError('Variable "localizedBrickValue2" does not exist.', 202, $this->source); })()) != false)) {
                                                    // line 203
                                                    echo "                                                    ";
                                                    $context["v2"] = twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getVersionPreview", [(isset($context["localizedBrickValue2"]) || array_key_exists("localizedBrickValue2", $context) ? $context["localizedBrickValue2"] : (function () { throw new RuntimeError('Variable "localizedBrickValue2" does not exist.', 203, $this->source); })())], "method", false, false, true, 203);
                                                    // line 204
                                                    echo "                                                ";
                                                } else {
                                                    // line 205
                                                    echo "                                                    ";
                                                    $context["localizedBrickValue2"] = null;
                                                    // line 206
                                                    echo "                                                ";
                                                }
                                                // line 207
                                                echo "                                            ";
                                            }
                                            // line 208
                                            echo "                                        ";
                                        }
                                        // line 209
                                        echo "
                                        ";
                                        // line 210
                                        if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
                                            // line 211
                                            echo "                                            <td>";
                                            echo $this->sandbox->ensureToStringAllowed((isset($context["v1"]) || array_key_exists("v1", $context) ? $context["v1"] : (function () { throw new RuntimeError('Variable "v1" does not exist.', 211, $this->source); })()), 211, $this->source);
                                            echo "</td>
                                        ";
                                        }
                                        // line 213
                                        echo "                                        ";
                                        $context["fieldIsEqual"] = ((($this->env->getTest('instanceof')->getCallable()($context["localizedFieldDefinition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\EqualComparisonInterface") &&  !twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "isEqual", [(isset($context["localizedBrickValue1"]) || array_key_exists("localizedBrickValue1", $context) ? $context["localizedBrickValue1"] : (function () { throw new RuntimeError('Variable "localizedBrickValue1" does not exist.', 213, $this->source); })()), (isset($context["localizedBrickValue2"]) || array_key_exists("localizedBrickValue2", $context) ? $context["localizedBrickValue2"] : (function () { throw new RuntimeError('Variable "localizedBrickValue2" does not exist.', 213, $this->source); })())], "method", false, false, true, 213))) ? ("modified1") : (""));
                                        // line 214
                                        echo "                                        <td class=\"";
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["fieldIsEqual"]) || array_key_exists("fieldIsEqual", $context) ? $context["fieldIsEqual"] : (function () { throw new RuntimeError('Variable "fieldIsEqual" does not exist.', 214, $this->source); })()), 214, $this->source), "html", null, true);
                                        echo "\">";
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["v2"]) || array_key_exists("v2", $context) ? $context["v2"] : (function () { throw new RuntimeError('Variable "v2" does not exist.', 214, $this->source); })()), 214, $this->source), "html", null, true);
                                        echo "</td>
                                    </tr>
                                ";
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
                                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['localizedFieldDefinition'], $context['_parent'], $context['loop']);
                                    $context = array_intersect_key($context, $_parent) + $_parent;
                                    // line 217
                                    echo "                                ";
                                    $context["break"] = true;
                                    // line 218
                                    echo "                            ";
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
                                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
                                $context = array_intersect_key($context, $_parent) + $_parent;
                                // line 219
                                echo "                        ";
                            } else {
                                // line 220
                                echo "                            ";
                                $context["v1"] = null;
                                // line 221
                                echo "
                            ";
                                // line 222
                                if ((isset($context["bricks1"]) || array_key_exists("bricks1", $context) ? $context["bricks1"] : (function () { throw new RuntimeError('Variable "bricks1" does not exist.', 222, $this->source); })())) {
                                    // line 223
                                    echo "                                ";
                                    $context["brickGetter"] = ("get" . $this->sandbox->ensureToStringAllowed($context["asAllowedType"], 223, $this->source));
                                    // line 224
                                    echo "                                ";
                                    $context["brick1Value"] = twig_get_attribute($this->env, $this->source, (isset($context["bricks1"]) || array_key_exists("bricks1", $context) ? $context["bricks1"] : (function () { throw new RuntimeError('Variable "bricks1" does not exist.', 224, $this->source); })()), (isset($context["brickGetter"]) || array_key_exists("brickGetter", $context) ? $context["brickGetter"] : (function () { throw new RuntimeError('Variable "brickGetter" does not exist.', 224, $this->source); })()), [], "any", false, false, true, 224);
                                    // line 225
                                    echo "
                                ";
                                    // line 226
                                    if ((isset($context["brick1Value"]) || array_key_exists("brick1Value", $context) ? $context["brick1Value"] : (function () { throw new RuntimeError('Variable "brick1Value" does not exist.', 226, $this->source); })())) {
                                        // line 227
                                        echo "                                    ";
                                        $context["brick1Value"] = twig_get_attribute($this->env, $this->source, (isset($context["brick1Value"]) || array_key_exists("brick1Value", $context) ? $context["brick1Value"] : (function () { throw new RuntimeError('Variable "brick1Value" does not exist.', 227, $this->source); })()), "getValueForFieldName", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 227)], "method", false, false, true, 227);
                                        // line 228
                                        echo "                                    ";
                                        if (((isset($context["brick1Value"]) || array_key_exists("brick1Value", $context) ? $context["brick1Value"] : (function () { throw new RuntimeError('Variable "brick1Value" does not exist.', 228, $this->source); })()) != false)) {
                                            // line 229
                                            echo "                                        ";
                                            $context["v1"] = twig_get_attribute($this->env, $this->source, $context["lfd"], "getVersionPreview", [(isset($context["brick1Value"]) || array_key_exists("brick1Value", $context) ? $context["brick1Value"] : (function () { throw new RuntimeError('Variable "brick1Value" does not exist.', 229, $this->source); })())], "method", false, false, true, 229);
                                            // line 230
                                            echo "                                    ";
                                        } else {
                                            // line 231
                                            echo "                                        ";
                                            $context["brick1Value"] = null;
                                            // line 232
                                            echo "                                    ";
                                        }
                                        // line 233
                                        echo "
                                ";
                                    }
                                    // line 235
                                    echo "                            ";
                                }
                                // line 236
                                echo "
                            ";
                                // line 237
                                $context["v2"] = null;
                                // line 238
                                echo "
                            ";
                                // line 239
                                if ((isset($context["bricks2"]) || array_key_exists("bricks2", $context) ? $context["bricks2"] : (function () { throw new RuntimeError('Variable "bricks2" does not exist.', 239, $this->source); })())) {
                                    // line 240
                                    echo "                                ";
                                    $context["brickGetter"] = ("get" . $this->sandbox->ensureToStringAllowed($context["asAllowedType"], 240, $this->source));
                                    // line 241
                                    echo "                                ";
                                    $context["brick2Value"] = twig_get_attribute($this->env, $this->source, (isset($context["bricks2"]) || array_key_exists("bricks2", $context) ? $context["bricks2"] : (function () { throw new RuntimeError('Variable "bricks2" does not exist.', 241, $this->source); })()), (isset($context["brickGetter"]) || array_key_exists("brickGetter", $context) ? $context["brickGetter"] : (function () { throw new RuntimeError('Variable "brickGetter" does not exist.', 241, $this->source); })()), [], "any", false, false, true, 241);
                                    // line 242
                                    echo "
                                ";
                                    // line 243
                                    if ((isset($context["brick2Value"]) || array_key_exists("brick2Value", $context) ? $context["brick2Value"] : (function () { throw new RuntimeError('Variable "brick2Value" does not exist.', 243, $this->source); })())) {
                                        // line 244
                                        echo "                                    ";
                                        $context["brick2Value"] = (((twig_get_attribute($this->env, $this->source, ($context["brick2Value"] ?? null), "getValueForFieldName", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 244)], "method", true, true, true, 244) &&  !(null === twig_get_attribute($this->env, $this->source, ($context["brick2Value"] ?? null), "getValueForFieldName", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 244)], "method", false, false, true, 244)))) ? (twig_get_attribute($this->env, $this->source, ($context["brick2Value"] ?? null), "getValueForFieldName", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 244)], "method", false, false, true, 244)) : (null));
                                        // line 245
                                        echo "                                    ";
                                        if (((isset($context["brick2Value"]) || array_key_exists("brick2Value", $context) ? $context["brick2Value"] : (function () { throw new RuntimeError('Variable "brick2Value" does not exist.', 245, $this->source); })()) != false)) {
                                            // line 246
                                            echo "                                        ";
                                            $context["v2"] = twig_get_attribute($this->env, $this->source, $context["lfd"], "getVersionPreview", [(isset($context["brick2Value"]) || array_key_exists("brick2Value", $context) ? $context["brick2Value"] : (function () { throw new RuntimeError('Variable "brick2Value" does not exist.', 246, $this->source); })())], "method", false, false, true, 246);
                                            // line 247
                                            echo "                                    ";
                                        } else {
                                            // line 248
                                            echo "                                        ";
                                            $context["brick2Value"] = null;
                                            // line 249
                                            echo "                                    ";
                                        }
                                        // line 250
                                        echo "                                ";
                                    }
                                    // line 251
                                    echo "                            ";
                                }
                                // line 252
                                echo "
                            <tr ";
                                // line 253
                                if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 253) % 2 != 0)) {
                                    echo "class=\"odd\"";
                                }
                                echo ">
                                <td>";
                                // line 254
                                ((((twig_capitalize_string_filter($this->env, $context["asAllowedType"]) . " - ") . twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 254))) ? (print (twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans(twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 254), [], "admin"), "html", null, true))) : (print ("")));
                                echo "</td>
                                <td>";
                                // line 255
                                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 255), 255, $this->source), "html", null, true);
                                echo "</td>
                                ";
                                // line 256
                                if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
                                    // line 257
                                    echo "                                    <td>";
                                    echo $this->sandbox->ensureToStringAllowed((isset($context["v1"]) || array_key_exists("v1", $context) ? $context["v1"] : (function () { throw new RuntimeError('Variable "v1" does not exist.', 257, $this->source); })()), 257, $this->source);
                                    echo "</td>
                                ";
                                }
                                // line 259
                                echo "                                ";
                                $context["fieldIsEqual"] = ((($this->env->getTest('instanceof')->getCallable()($context["lfd"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\EqualComparisonInterface") &&  !twig_get_attribute($this->env, $this->source, $context["lfd"], "isEqual", [(isset($context["brick1Value"]) || array_key_exists("brick1Value", $context) ? $context["brick1Value"] : (function () { throw new RuntimeError('Variable "brick1Value" does not exist.', 259, $this->source); })()), (isset($context["brick2Value"]) || array_key_exists("brick2Value", $context) ? $context["brick2Value"] : (function () { throw new RuntimeError('Variable "brick2Value" does not exist.', 259, $this->source); })())], "method", false, false, true, 259))) ? ("modified") : (""));
                                // line 260
                                echo "                                <td class=\"";
                                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["fieldIsEqual"]) || array_key_exists("fieldIsEqual", $context) ? $context["fieldIsEqual"] : (function () { throw new RuntimeError('Variable "fieldIsEqual" does not exist.', 260, $this->source); })()), 260, $this->source), "html", null, true);
                                echo "\">";
                                echo $this->sandbox->ensureToStringAllowed((isset($context["v2"]) || array_key_exists("v2", $context) ? $context["v2"] : (function () { throw new RuntimeError('Variable "v2" does not exist.', 260, $this->source); })()), 260, $this->source);
                                echo "</td>
                            </tr>
                        ";
                            }
                            // line 263
                            echo "                    ";
                        }
                        // line 264
                        echo "
                ";
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
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['lfd'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 266
                    echo "            ";
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['asAllowedType'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 267
                echo "        ";
            } elseif ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Fieldcollections")) {
                // line 268
                echo "            ";
                list($context["fields1"], $context["fields2"]) =                 [twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 268, $this->source); })()), "get", [$context["fieldName"]], "method", false, false, true, 268), twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 268, $this->source); })()), "get", [$context["fieldName"]], "method", false, false, true, 268)];
                // line 269
                echo "            ";
                list($context["fieldDefinitions1"], $context["fieldItems1"]) =                 [null, null];
                // line 270
                echo "            ";
                list($context["fieldDefinitions2"], $context["fieldItems2"]) =                 [null, null];
                // line 271
                echo "            ";
                $context["skipFieldItems2"] = [];
                // line 272
                echo "
            ";
                // line 273
                if ((isset($context["fields1"]) || array_key_exists("fields1", $context) ? $context["fields1"] : (function () { throw new RuntimeError('Variable "fields1" does not exist.', 273, $this->source); })())) {
                    // line 274
                    echo "                ";
                    $context["fieldDefinitions1"] = twig_get_attribute($this->env, $this->source, (isset($context["fields1"]) || array_key_exists("fields1", $context) ? $context["fields1"] : (function () { throw new RuntimeError('Variable "fields1" does not exist.', 274, $this->source); })()), "getItemDefinitions", [], "method", false, false, true, 274);
                    // line 275
                    echo "                ";
                    $context["fieldItems1"] = twig_get_attribute($this->env, $this->source, (isset($context["fields1"]) || array_key_exists("fields1", $context) ? $context["fields1"] : (function () { throw new RuntimeError('Variable "fields1" does not exist.', 275, $this->source); })()), "getItems", [], "method", false, false, true, 275);
                    // line 276
                    echo "            ";
                }
                // line 277
                echo "
            ";
                // line 278
                if ((isset($context["fields2"]) || array_key_exists("fields2", $context) ? $context["fields2"] : (function () { throw new RuntimeError('Variable "fields2" does not exist.', 278, $this->source); })())) {
                    // line 279
                    echo "                ";
                    $context["fieldDefinitions2"] = twig_get_attribute($this->env, $this->source, (isset($context["fields2"]) || array_key_exists("fields2", $context) ? $context["fields2"] : (function () { throw new RuntimeError('Variable "fields2" does not exist.', 279, $this->source); })()), "getItemDefinitions", [], "method", false, false, true, 279);
                    // line 280
                    echo "                ";
                    $context["fieldItems2"] = twig_get_attribute($this->env, $this->source, (isset($context["fields2"]) || array_key_exists("fields2", $context) ? $context["fields2"] : (function () { throw new RuntimeError('Variable "fields2" does not exist.', 280, $this->source); })()), "getItems", [], "method", false, false, true, 280);
                    // line 281
                    echo "            ";
                }
                // line 282
                echo "
            ";
                // line 283
                if ((is_iterable((isset($context["fieldItems1"]) || array_key_exists("fieldItems1", $context) ? $context["fieldItems1"] : (function () { throw new RuntimeError('Variable "fieldItems1" does not exist.', 283, $this->source); })())) && (twig_length_filter($this->env, (isset($context["fieldItems1"]) || array_key_exists("fieldItems1", $context) ? $context["fieldItems1"] : (function () { throw new RuntimeError('Variable "fieldItems1" does not exist.', 283, $this->source); })())) > 0))) {
                    // line 284
                    echo "                ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable((isset($context["fieldItems1"]) || array_key_exists("fieldItems1", $context) ? $context["fieldItems1"] : (function () { throw new RuntimeError('Variable "fieldItems1" does not exist.', 284, $this->source); })()));
                    foreach ($context['_seq'] as $context["fkey1"] => $context["fieldItem1"]) {
                        // line 285
                        echo "                    ";
                        $context["fieldKeys1"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fieldDefinitions1"]) || array_key_exists("fieldDefinitions1", $context) ? $context["fieldDefinitions1"] : (function () { throw new RuntimeError('Variable "fieldDefinitions1" does not exist.', 285, $this->source); })()), twig_get_attribute($this->env, $this->source, $context["fieldItem1"], "getType", [], "method", false, false, true, 285), [], "array", false, false, true, 285), "getFieldDefinitions", [], "method", false, false, true, 285);
                        // line 286
                        echo "
                    ";
                        // line 287
                        if ((twig_get_attribute($this->env, $this->source, ($context["fieldItems2"] ?? null), $context["fkey1"], [], "array", true, true, true, 287) && (twig_get_attribute($this->env, $this->source, $context["fieldItem1"], "getType", [], "method", false, false, true, 287) == twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fieldItems2"]) || array_key_exists("fieldItems2", $context) ? $context["fieldItems2"] : (function () { throw new RuntimeError('Variable "fieldItems2" does not exist.', 287, $this->source); })()), $context["fkey1"], [], "array", false, false, true, 287), "getType", [], "method", false, false, true, 287)))) {
                            // line 288
                            echo "                        ";
                            $context["ffkey2"] = twig_get_attribute($this->env, $this->source, (isset($context["fieldItems2"]) || array_key_exists("fieldItems2", $context) ? $context["fieldItems2"] : (function () { throw new RuntimeError('Variable "fieldItems2" does not exist.', 288, $this->source); })()), $context["fkey1"], [], "array", false, false, true, 288);
                            // line 289
                            echo "                        ";
                            $context["fieldKeys2"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fieldDefinitions2"]) || array_key_exists("fieldDefinitions2", $context) ? $context["fieldDefinitions2"] : (function () { throw new RuntimeError('Variable "fieldDefinitions2" does not exist.', 289, $this->source); })()), twig_get_attribute($this->env, $this->source, (isset($context["ffkey2"]) || array_key_exists("ffkey2", $context) ? $context["ffkey2"] : (function () { throw new RuntimeError('Variable "ffkey2" does not exist.', 289, $this->source); })()), "getType", [], "method", false, false, true, 289), [], "array", false, false, true, 289), "getFieldDefinitions", [], "method", false, false, true, 289);
                            // line 290
                            echo "                        ";
                            $context["skipFieldItems2"] = twig_array_merge($this->sandbox->ensureToStringAllowed((isset($context["skipFieldItems2"]) || array_key_exists("skipFieldItems2", $context) ? $context["skipFieldItems2"] : (function () { throw new RuntimeError('Variable "skipFieldItems2" does not exist.', 290, $this->source); })()), 290, $this->source), [$context["fkey1"], $context["fkey1"]]);
                            // line 291
                            echo "                    ";
                        }
                        // line 292
                        echo "
                    ";
                        // line 293
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable((isset($context["fieldKeys1"]) || array_key_exists("fieldKeys1", $context) ? $context["fieldKeys1"] : (function () { throw new RuntimeError('Variable "fieldKeys1" does not exist.', 293, $this->source); })()));
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
                        foreach ($context['_seq'] as $context["fkey"] => $context["fieldKey1"]) {
                            // line 294
                            echo "                        ";
                            list($context["v2"], $context["keyData2"]) =                             [null, null];
                            // line 295
                            echo "                        ";
                            $context["keyData1"] = twig_get_attribute($this->env, $this->source, $context["fieldItem1"], "get", [twig_get_attribute($this->env, $this->source, $context["fieldKey1"], "name", [], "any", false, false, true, 295)], "method", false, false, true, 295);
                            // line 296
                            echo "                        ";
                            $context["v1"] = twig_get_attribute($this->env, $this->source, $context["fieldKey1"], "getVersionPreview", [(isset($context["keyData1"]) || array_key_exists("keyData1", $context) ? $context["keyData1"] : (function () { throw new RuntimeError('Variable "keyData1" does not exist.', 296, $this->source); })())], "method", false, false, true, 296);
                            // line 297
                            echo "
                        ";
                            // line 298
                            if ((array_key_exists("ffkey2", $context) && twig_get_attribute($this->env, $this->source, ($context["fieldKeys2"] ?? null), $context["fkey"], [], "array", true, true, true, 298))) {
                                // line 299
                                echo "                            ";
                                $context["keyData2"] = twig_get_attribute($this->env, $this->source, (isset($context["ffkey2"]) || array_key_exists("ffkey2", $context) ? $context["ffkey2"] : (function () { throw new RuntimeError('Variable "ffkey2" does not exist.', 299, $this->source); })()), "get", [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fieldKeys2"]) || array_key_exists("fieldKeys2", $context) ? $context["fieldKeys2"] : (function () { throw new RuntimeError('Variable "fieldKeys2" does not exist.', 299, $this->source); })()), $context["fkey"], [], "array", false, false, true, 299), "name", [], "any", false, false, true, 299)], "method", false, false, true, 299);
                                // line 300
                                echo "                            ";
                                $context["v2"] = twig_get_attribute($this->env, $this->source, $context["fieldKey1"], "getVersionPreview", [(isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 300, $this->source); })())], "method", false, false, true, 300);
                                // line 301
                                echo "                        ";
                            }
                            // line 302
                            echo "
                        <tr ";
                            // line 303
                            if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 303) % 2 != 0)) {
                                echo "class=\"odd\"";
                            }
                            echo ">
                        <td>";
                            // line 304
                            echo twig_escape_filter($this->env, (($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldItem1"], "getType", [], "method", false, false, true, 304), 304, $this->source) . " - ") . $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldKey1"], "getTitle", [], "method", false, false, true, 304), 304, $this->source), [], "admin")), "html", null, true);
                            echo "</td>
                        <td>";
                            // line 305
                            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldKey1"], "name", [], "any", false, false, true, 305), 305, $this->source), "html", null, true);
                            echo "</td>
                            ";
                            // line 306
                            if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
                                // line 307
                                echo "                                <td>";
                                echo $this->sandbox->ensureToStringAllowed((isset($context["v1"]) || array_key_exists("v1", $context) ? $context["v1"] : (function () { throw new RuntimeError('Variable "v1" does not exist.', 307, $this->source); })()), 307, $this->source);
                                echo "</td>
                            ";
                            }
                            // line 309
                            echo "                            ";
                            $context["fieldIsEqual"] = ((($this->env->getTest('instanceof')->getCallable()($context["fieldKey1"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\EqualComparisonInterface") &&  !twig_get_attribute($this->env, $this->source, $context["fieldKey1"], "isEqual", [(isset($context["keyData1"]) || array_key_exists("keyData1", $context) ? $context["keyData1"] : (function () { throw new RuntimeError('Variable "keyData1" does not exist.', 309, $this->source); })()), (isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 309, $this->source); })())], "method", false, false, true, 309))) ? ("modified") : (""));
                            // line 310
                            echo "                            <td class=\"";
                            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["fieldIsEqual"]) || array_key_exists("fieldIsEqual", $context) ? $context["fieldIsEqual"] : (function () { throw new RuntimeError('Variable "fieldIsEqual" does not exist.', 310, $this->source); })()), 310, $this->source), "html", null, true);
                            echo "\">";
                            echo $this->sandbox->ensureToStringAllowed((isset($context["v2"]) || array_key_exists("v2", $context) ? $context["v2"] : (function () { throw new RuntimeError('Variable "v2" does not exist.', 310, $this->source); })()), 310, $this->source);
                            echo "</td>
                        </tr>
                    ";
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
                        unset($context['_seq'], $context['_iterated'], $context['fkey'], $context['fieldKey1'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 313
                        echo "                ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['fkey1'], $context['fieldItem1'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 314
                    echo "            ";
                }
                // line 315
                echo "
            ";
                // line 316
                if ((is_iterable((isset($context["fieldItems2"]) || array_key_exists("fieldItems2", $context) ? $context["fieldItems2"] : (function () { throw new RuntimeError('Variable "fieldItems2" does not exist.', 316, $this->source); })())) && (twig_length_filter($this->env, (isset($context["fieldItems2"]) || array_key_exists("fieldItems2", $context) ? $context["fieldItems2"] : (function () { throw new RuntimeError('Variable "fieldItems2" does not exist.', 316, $this->source); })())) > 0))) {
                    // line 317
                    echo "
                ";
                    // line 318
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable((isset($context["fieldItems2"]) || array_key_exists("fieldItems2", $context) ? $context["fieldItems2"] : (function () { throw new RuntimeError('Variable "fieldItems2" does not exist.', 318, $this->source); })()));
                    foreach ($context['_seq'] as $context["fkey2"] => $context["fieldItem2"]) {
                        // line 319
                        echo "                    ";
                        if (!twig_in_filter($context["fkey2"], twig_get_array_keys_filter((isset($context["skipFieldItems2"]) || array_key_exists("skipFieldItems2", $context) ? $context["skipFieldItems2"] : (function () { throw new RuntimeError('Variable "skipFieldItems2" does not exist.', 319, $this->source); })())))) {
                            // line 320
                            echo "                        ";
                            $context["fieldKeys2"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fieldDefinitions2"]) || array_key_exists("fieldDefinitions2", $context) ? $context["fieldDefinitions2"] : (function () { throw new RuntimeError('Variable "fieldDefinitions2" does not exist.', 320, $this->source); })()), twig_get_attribute($this->env, $this->source, $context["fieldItem2"], "getType", [], "method", false, false, true, 320), [], "array", false, false, true, 320), "getFieldDefinitions", [], "method", false, false, true, 320);
                            // line 321
                            echo "
                        ";
                            // line 322
                            if ((twig_get_attribute($this->env, $this->source, ($context["fieldItems1"] ?? null), $context["fkey2"], [], "array", true, true, true, 322) && (twig_get_attribute($this->env, $this->source, $context["fieldItem2"], "getType", [], "method", false, false, true, 322) == twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fieldItems1"]) || array_key_exists("fieldItems1", $context) ? $context["fieldItems1"] : (function () { throw new RuntimeError('Variable "fieldItems1" does not exist.', 322, $this->source); })()), $context["fkey2"], [], "array", false, false, true, 322), "getType", [], "method", false, false, true, 322)))) {
                                // line 323
                                echo "                            ";
                                $context["ffkey1"] = twig_get_attribute($this->env, $this->source, (isset($context["fieldItems1"]) || array_key_exists("fieldItems1", $context) ? $context["fieldItems1"] : (function () { throw new RuntimeError('Variable "fieldItems1" does not exist.', 323, $this->source); })()), $context["fkey2"], [], "array", false, false, true, 323);
                                // line 324
                                echo "                            ";
                                $context["fieldKeys1"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fieldDefinitions1"]) || array_key_exists("fieldDefinitions1", $context) ? $context["fieldDefinitions1"] : (function () { throw new RuntimeError('Variable "fieldDefinitions1" does not exist.', 324, $this->source); })()), twig_get_attribute($this->env, $this->source, (isset($context["ffkey1"]) || array_key_exists("ffkey1", $context) ? $context["ffkey1"] : (function () { throw new RuntimeError('Variable "ffkey1" does not exist.', 324, $this->source); })()), "getType", [], "method", false, false, true, 324), [], "array", false, false, true, 324), "getFieldDefinitions", [], "method", false, false, true, 324);
                                // line 325
                                echo "                        ";
                            }
                            // line 326
                            echo "
                        ";
                            // line 327
                            $context['_parent'] = $context;
                            $context['_seq'] = twig_ensure_traversable((isset($context["fieldKeys2"]) || array_key_exists("fieldKeys2", $context) ? $context["fieldKeys2"] : (function () { throw new RuntimeError('Variable "fieldKeys2" does not exist.', 327, $this->source); })()));
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
                            foreach ($context['_seq'] as $context["fkey"] => $context["fieldKey2"]) {
                                // line 328
                                echo "                            ";
                                list($context["v1"], $context["keyData1"]) =                                 [null, null];
                                // line 329
                                echo "                            ";
                                $context["keyData2"] = twig_get_attribute($this->env, $this->source, $context["fieldItem2"], "get", [twig_get_attribute($this->env, $this->source, $context["fieldKey2"], "name", [], "any", false, false, true, 329)], "method", false, false, true, 329);
                                // line 330
                                echo "                            ";
                                $context["v2"] = twig_get_attribute($this->env, $this->source, $context["fieldKey2"], "getVersionPreview", [(isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 330, $this->source); })())], "method", false, false, true, 330);
                                // line 331
                                echo "
                            ";
                                // line 332
                                if ((array_key_exists("ffkey1", $context) && twig_get_attribute($this->env, $this->source, ($context["fieldKeys1"] ?? null), $context["fkey"], [], "array", true, true, true, 332))) {
                                    // line 333
                                    echo "                                ";
                                    $context["keyData1"] = twig_get_attribute($this->env, $this->source, (isset($context["ffkey1"]) || array_key_exists("ffkey1", $context) ? $context["ffkey1"] : (function () { throw new RuntimeError('Variable "ffkey1" does not exist.', 333, $this->source); })()), "get", [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fieldKeys1"]) || array_key_exists("fieldKeys1", $context) ? $context["fieldKeys1"] : (function () { throw new RuntimeError('Variable "fieldKeys1" does not exist.', 333, $this->source); })()), $context["fkey"], [], "array", false, false, true, 333), "name", [], "any", false, false, true, 333)], "method", false, false, true, 333);
                                    // line 334
                                    echo "                                ";
                                    $context["v1"] = twig_get_attribute($this->env, $this->source, $context["fieldKey2"], "getVersionPreview", [(isset($context["keyData1"]) || array_key_exists("keyData1", $context) ? $context["keyData1"] : (function () { throw new RuntimeError('Variable "keyData1" does not exist.', 334, $this->source); })())], "method", false, false, true, 334);
                                    // line 335
                                    echo "                            ";
                                }
                                // line 336
                                echo "
                            <tr ";
                                // line 337
                                if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 337) % 2 != 0)) {
                                    echo "class=\"odd\"";
                                }
                                echo ">
                                <td>";
                                // line 338
                                echo twig_escape_filter($this->env, (($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldItem2"], "getType", [], "method", false, false, true, 338), 338, $this->source) . " - ") . $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldKey2"], "getTitle", [], "method", false, false, true, 338), 338, $this->source), [], "admin")), "html", null, true);
                                echo "</td>
                                <td>";
                                // line 339
                                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldKey2"], "name", [], "any", false, false, true, 339), 339, $this->source), "html", null, true);
                                echo "</td>
                                ";
                                // line 340
                                if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
                                    // line 341
                                    echo "                                    <td>";
                                    echo $this->sandbox->ensureToStringAllowed((isset($context["v1"]) || array_key_exists("v1", $context) ? $context["v1"] : (function () { throw new RuntimeError('Variable "v1" does not exist.', 341, $this->source); })()), 341, $this->source);
                                    echo "</td>
                                ";
                                }
                                // line 343
                                echo "                                ";
                                $context["fieldIsEqual"] = ((($this->env->getTest('instanceof')->getCallable()($context["fieldKey2"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\EqualComparisonInterface") &&  !twig_get_attribute($this->env, $this->source, $context["fieldKey2"], "isEqual", [(isset($context["v1"]) || array_key_exists("v1", $context) ? $context["v1"] : (function () { throw new RuntimeError('Variable "v1" does not exist.', 343, $this->source); })()), (isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 343, $this->source); })())], "method", false, false, true, 343))) ? ("modified") : (""));
                                // line 344
                                echo "                                <td class=\"";
                                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["fieldIsEqual"]) || array_key_exists("fieldIsEqual", $context) ? $context["fieldIsEqual"] : (function () { throw new RuntimeError('Variable "fieldIsEqual" does not exist.', 344, $this->source); })()), 344, $this->source), "html", null, true);
                                echo "\">";
                                echo $this->sandbox->ensureToStringAllowed((isset($context["v2"]) || array_key_exists("v2", $context) ? $context["v2"] : (function () { throw new RuntimeError('Variable "v2" does not exist.', 344, $this->source); })()), 344, $this->source);
                                echo "</td>
                            </tr>
                        ";
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
                            unset($context['_seq'], $context['_iterated'], $context['fkey'], $context['fieldKey2'], $context['_parent'], $context['loop']);
                            $context = array_intersect_key($context, $_parent) + $_parent;
                            // line 347
                            echo "                    ";
                        }
                        // line 348
                        echo "                ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['fkey2'], $context['fieldItem2'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 349
                    echo "            ";
                }
                // line 350
                echo "        ";
            } else {
                // line 351
                echo "            ";
                $context["keyData1"] = (( !(twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 351, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 351) === false)) ? (twig_get_attribute($this->env, $this->source, (isset($context["object1"]) || array_key_exists("object1", $context) ? $context["object1"] : (function () { throw new RuntimeError('Variable "object1" does not exist.', 351, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 351)) : (null));
                // line 352
                echo "            ";
                $context["v1"] = twig_get_attribute($this->env, $this->source, $context["definition"], "getVersionPreview", [(isset($context["keyData1"]) || array_key_exists("keyData1", $context) ? $context["keyData1"] : (function () { throw new RuntimeError('Variable "keyData1" does not exist.', 352, $this->source); })())], "method", false, false, true, 352);
                // line 353
                echo "            ";
                $context["keyData2"] = (( !(twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 353, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 353) === false)) ? (twig_get_attribute($this->env, $this->source, (isset($context["object2"]) || array_key_exists("object2", $context) ? $context["object2"] : (function () { throw new RuntimeError('Variable "object2" does not exist.', 353, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 353)) : (null));
                // line 354
                echo "            ";
                $context["v2"] = twig_get_attribute($this->env, $this->source, $context["definition"], "getVersionPreview", [(isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 354, $this->source); })())], "method", false, false, true, 354);
                // line 355
                echo "
            <tr ";
                // line 356
                if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 356) % 2 != 0)) {
                    echo "class=\"odd\"";
                }
                echo ">
                <td>";
                // line 357
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getTitle", [], "method", false, false, true, 357), 357, $this->source), [], "admin"), "html", null, true);
                echo "</td>
                <td>";
                // line 358
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getName", [], "method", false, false, true, 358), 358, $this->source), "html", null, true);
                echo "</td>
                ";
                // line 359
                if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
                    // line 360
                    echo "                    <td>";
                    echo $this->sandbox->ensureToStringAllowed((isset($context["v1"]) || array_key_exists("v1", $context) ? $context["v1"] : (function () { throw new RuntimeError('Variable "v1" does not exist.', 360, $this->source); })()), 360, $this->source);
                    echo "</td>
                ";
                }
                // line 362
                echo "                ";
                $context["fieldIsEqual"] = ((($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\EqualComparisonInterface") &&  !twig_get_attribute($this->env, $this->source, $context["definition"], "isEqual", [(isset($context["keyData1"]) || array_key_exists("keyData1", $context) ? $context["keyData1"] : (function () { throw new RuntimeError('Variable "keyData1" does not exist.', 362, $this->source); })()), (isset($context["keyData2"]) || array_key_exists("keyData2", $context) ? $context["keyData2"] : (function () { throw new RuntimeError('Variable "keyData2" does not exist.', 362, $this->source); })())], "method", false, false, true, 362))) ? ("modified") : (""));
                // line 363
                echo "                <td class=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["fieldIsEqual"]) || array_key_exists("fieldIsEqual", $context) ? $context["fieldIsEqual"] : (function () { throw new RuntimeError('Variable "fieldIsEqual" does not exist.', 363, $this->source); })()), 363, $this->source), "html", null, true);
                echo "\">";
                echo $this->sandbox->ensureToStringAllowed((isset($context["v2"]) || array_key_exists("v2", $context) ? $context["v2"] : (function () { throw new RuntimeError('Variable "v2" does not exist.', 363, $this->source); })()), 363, $this->source);
                echo "</td>
            </tr>
        ";
            }
            // line 366
            echo "    ";
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
        unset($context['_seq'], $context['_iterated'], $context['fieldName'], $context['definition'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 367
        echo "</table>

</body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreAdmin/admin/data_object/data_object/diff_versions.html.twig";
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
        return array (  1326 => 367,  1312 => 366,  1303 => 363,  1300 => 362,  1294 => 360,  1292 => 359,  1288 => 358,  1284 => 357,  1278 => 356,  1275 => 355,  1272 => 354,  1269 => 353,  1266 => 352,  1263 => 351,  1260 => 350,  1257 => 349,  1251 => 348,  1248 => 347,  1228 => 344,  1225 => 343,  1219 => 341,  1217 => 340,  1213 => 339,  1209 => 338,  1203 => 337,  1200 => 336,  1197 => 335,  1194 => 334,  1191 => 333,  1189 => 332,  1186 => 331,  1183 => 330,  1180 => 329,  1177 => 328,  1160 => 327,  1157 => 326,  1154 => 325,  1151 => 324,  1148 => 323,  1146 => 322,  1143 => 321,  1140 => 320,  1137 => 319,  1133 => 318,  1130 => 317,  1128 => 316,  1125 => 315,  1122 => 314,  1116 => 313,  1096 => 310,  1093 => 309,  1087 => 307,  1085 => 306,  1081 => 305,  1077 => 304,  1071 => 303,  1068 => 302,  1065 => 301,  1062 => 300,  1059 => 299,  1057 => 298,  1054 => 297,  1051 => 296,  1048 => 295,  1045 => 294,  1028 => 293,  1025 => 292,  1022 => 291,  1019 => 290,  1016 => 289,  1013 => 288,  1011 => 287,  1008 => 286,  1005 => 285,  1000 => 284,  998 => 283,  995 => 282,  992 => 281,  989 => 280,  986 => 279,  984 => 278,  981 => 277,  978 => 276,  975 => 275,  972 => 274,  970 => 273,  967 => 272,  964 => 271,  961 => 270,  958 => 269,  955 => 268,  952 => 267,  938 => 266,  923 => 264,  920 => 263,  911 => 260,  908 => 259,  902 => 257,  900 => 256,  896 => 255,  892 => 254,  886 => 253,  883 => 252,  880 => 251,  877 => 250,  874 => 249,  871 => 248,  868 => 247,  865 => 246,  862 => 245,  859 => 244,  857 => 243,  854 => 242,  851 => 241,  848 => 240,  846 => 239,  843 => 238,  841 => 237,  838 => 236,  835 => 235,  831 => 233,  828 => 232,  825 => 231,  822 => 230,  819 => 229,  816 => 228,  813 => 227,  811 => 226,  808 => 225,  805 => 224,  802 => 223,  800 => 222,  797 => 221,  794 => 220,  791 => 219,  777 => 218,  774 => 217,  754 => 214,  751 => 213,  745 => 211,  743 => 210,  740 => 209,  737 => 208,  734 => 207,  731 => 206,  728 => 205,  725 => 204,  722 => 203,  719 => 202,  716 => 201,  713 => 200,  711 => 199,  708 => 198,  705 => 197,  702 => 196,  700 => 195,  697 => 194,  694 => 193,  691 => 192,  688 => 191,  685 => 190,  682 => 189,  679 => 188,  676 => 187,  673 => 186,  670 => 185,  668 => 184,  665 => 183,  662 => 182,  659 => 181,  657 => 180,  654 => 179,  651 => 178,  649 => 177,  644 => 175,  638 => 174,  631 => 173,  613 => 172,  595 => 171,  592 => 170,  590 => 169,  587 => 168,  584 => 167,  582 => 166,  579 => 165,  576 => 164,  559 => 163,  556 => 162,  553 => 161,  535 => 160,  532 => 159,  529 => 158,  523 => 157,  520 => 156,  517 => 155,  511 => 154,  508 => 153,  488 => 150,  485 => 149,  479 => 147,  477 => 146,  469 => 145,  465 => 144,  459 => 143,  456 => 142,  453 => 141,  450 => 140,  447 => 139,  444 => 138,  426 => 137,  424 => 136,  421 => 135,  418 => 134,  414 => 133,  411 => 132,  408 => 131,  405 => 130,  402 => 129,  399 => 128,  395 => 127,  392 => 126,  389 => 125,  386 => 124,  383 => 123,  380 => 122,  378 => 121,  375 => 120,  369 => 119,  366 => 118,  362 => 117,  359 => 116,  357 => 115,  354 => 114,  351 => 113,  348 => 112,  345 => 111,  343 => 110,  340 => 109,  337 => 108,  334 => 107,  331 => 106,  329 => 105,  326 => 104,  324 => 103,  321 => 102,  318 => 101,  315 => 100,  312 => 99,  306 => 98,  286 => 95,  283 => 94,  277 => 92,  275 => 91,  271 => 90,  265 => 89,  259 => 88,  256 => 87,  253 => 86,  250 => 85,  248 => 84,  245 => 83,  242 => 82,  239 => 81,  236 => 80,  218 => 79,  213 => 78,  210 => 77,  193 => 76,  181 => 68,  175 => 66,  173 => 65,  168 => 62,  162 => 59,  158 => 58,  153 => 55,  151 => 54,  143 => 52,  140 => 51,  134 => 49,  132 => 48,  121 => 43,  118 => 42,  112 => 40,  110 => 39,  101 => 34,  95 => 32,  93 => 31,  87 => 27,  82 => 24,  79 => 23,  74 => 20,  70 => 18,  67 => 17,  65 => 16,  58 => 11,  56 => 10,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"/bundles/pimcoreadmin/css/object_versions.css\"/>
</head>

<body>

{% set fields = object1.getClass().getFieldDefinitions()  %}

<table class=\"preview\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
    <tr>
        <th>Name</th>
        <th>Key</th>
        {% if isImportPreview is defined %}
            {% if isNew  is defined %}
                <th>New Object or unable to resolve</th>
            {% else %}
                <th>Before</th>
                <th>After</th>
            {% endif %}
        {% else %}
            <th>Version 1</th>
            <th>Version 2</th>
        {% endif %}
    </tr>
    <tr class=\"system\">
        <td>Date</td>
        <td>modificationDate</td>
        {% if isImportPreview is not defined or isNew is not defined %}
            <td>{{ object1.getModificationDate()|date('Y-m-d H:i:s') }}</td>
        {% endif %}
        <td>{{ object2.getModificationDate()|date('Y-m-d H:i:s') }}</td>
    </tr>
    <tr class=\"system\">
        <td>Path</td>
        <td>path</td>
        {% if isImportPreview is not defined or isNew is not defined %}
            <td> {{ object1.getRealFullPath() }} </td>
        {% endif %}
        {% set modifiedPathClass = object1.getRealFullPath() is not same as(object2.getRealFullPath()) ? 'modified' : ''  %}
        <td class=\"{{ modifiedPathClass }}\">{{ object2.getRealFullPath() }}</td>
    </tr>
    <tr class=\"system\">
        <td>Published</td>
        <td>published</td>
        {% if isImportPreview is not defined or isNew is not defined %}
            <td> {{ object1.getPublished() }} </td>
        {% endif %}
        {% set modifiedPublishedClass = object1.getPublished() is not same as(object2.getPublished()) ? 'modified' : ''  %}
        <td class=\"{{ modifiedPublishedClass }}\">{{ object2.getPublished() ? 'true' : 'false' }}</td>
    </tr>
    {% if versionNote1 or versionNote2 %}
        <tr class=\"system\">
            <td>Version Note</td>
            <td>&nbsp;</td>
            <td>{{ versionNote1|nl2br }}</td>
            <td>{{ versionNote2|nl2br }}</td>
        </tr>
    {% endif %}
    <tr class=\"system\">
        <td>Id</td>
        <td>id</td>
        {% if isImportPreview is not defined or isNew is not defined %}
            <td> {{ object1.getId() }} </td>
        {% endif %}
        <td>{{ object2.getId() }}</td>
    </tr>


    <tr class=\"\">
        <td colspan=\"3\">&nbsp;</td>
    </tr>

    {% for fieldName, definition in fields %}
        {% if definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Localizedfields') %}
            {% for language in validLanguages %}
                {% for lfd in definition.getFieldDefinitions() %}
                    {% set vlContainer = object1.getValueForFieldName(fieldName) %}
                    {% set keyData1 = vlContainer is not empty ? vlContainer.getLocalizedValue(lfd.getName(), language) : null %}
                    {% set v1 = lfd.getVersionPreview(keyData1) %}

                    {% set v2Container = object2.getValueForFieldName(fieldName) %}
                    {% set keyData2 = v2Container is not empty ? v2Container.getLocalizedValue(lfd.getName(), language) : null %}
                    {% set v2 = v2Container ? lfd.getVersionPreview(keyData2) %}

                    <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                    <td>{{ lfd.getTitle()|trans([],'admin') }} ({{ language }})</td>
                    <td>{{ lfd.getName() }}</td>
                        {% if isImportPreview is not defined or isNew is not defined %}
                        <td>{{ v1 | raw }}</td>
                    {% endif %}
                        {% set fieldIsEqual = lfd is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\EqualComparisonInterface') and not lfd.isEqual(keyData1, keyData2) ? 'modified' : '' %}
                        <td class=\"{{ fieldIsEqual }}\">{{ v2 | raw }}</td>
                    </tr>
                {% endfor %}
            {% endfor %}
        {% elseif definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Classificationstore') %}
            {% set storedata1 = object1.getValueForFieldName(fieldName) %}
            {% set storedata2 = object2.getValueForFieldName(fieldName) %}

            {% set existingGroups = [] %}

            {% set activeGroups1 = [] %}
            {% if storedata1 %}
                {% set activeGroups1 = storedata1.getActiveGroups() %}
            {% endif %}

            {% set activeGroups2 = [] %}
            {% if storedata2 %}
                {% set activeGroups2 = storedata2.getActiveGroups() %}
            {% endif %}

            {% set activeGroups = activeGroups1 + activeGroups2 %}

            {% for activeGroupId, enabled in activeGroups %}
                {% set existingGroups = existingGroups + {(activeGroupId): enabled} %}
            {% endfor %}

            {% if existingGroups is not empty %}
                {% set languages = ['default'] %}
                {% if definition.isLocalized() %}
                    {% set languages = validLanguages|merge(languages) %}
                {% endif %}

                {% for activeGroupId, enabled in existingGroups %}
                    {% if activeGroups1[activeGroupId] is defined and activeGroups2[activeGroupId] is defined %}
                        {% set groupDefinition = pimcore_object_classificationstore_group(activeGroupId) %}
                        {% if groupDefinition is not empty %}
                            {% set keyGroupRelations = groupDefinition.getRelations() %}

                            {% for keyGroupRelation in keyGroupRelations %}
                                {% set keyDef = pimcore_object_classificationstore_get_field_definition_from_json(keyGroupRelation.getDefinition(), keyGroupRelation.getType())  %}

                                {% if keyDef is not empty %}
                                    {% for language in languages %}
                                        {% set keyData1 = storedata1 ? storedata1.getLocalizedKeyValue(activeGroupId, keyGroupRelation.getKeyId(), language, true, true) : null %}
                                        {% set preview1 = keyDef.getVersionPreview(keyData1) %}
                                        {% set keyData2 = storedata2 ? storedata2.getLocalizedKeyValue(activeGroupId, keyGroupRelation.getKeyId(), language, true, true) : null %}
                                        {% set preview2 = keyDef.getVersionPreview(keyData2) %}

                                        <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                                            <td>{{ definition.getTitle()|trans([],'admin') }}</td>
                                            <td>{{ groupDefinition.getName() }} - {{ keyGroupRelation.getName() }} {{ definition.isLocalized() ? \"/ \" ~ language : \"\" }}</td>
                                            {% if isImportPreview is not defined or isNew is not defined %}
                                                <td>{{ preview1 | raw }}</td>
                                            {% endif %}
                                            {% set fieldIsEqual = keyDef is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\EqualComparisonInterface') and not keyDef.isEqual(keyData1, keyData2) ? 'modified' : '' %}
                                            <td class=\"{{ fieldIsEqual }}\">{{ preview2 }}</td>
                                        </tr>
                                    {% endfor %}
                                {% endif %}
                            {% endfor %}
                        {% endif %}
                    {% endif %}
                {% endfor %}
            {% endif %}
        {% elseif definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Objectbricks') %}
            {% for asAllowedType in definition.getAllowedTypes() %}
                {% set collectionDef = pimcore_object_brick_definition_key(asAllowedType) %}

                {% for lfd in collectionDef.getFieldDefinitions() %}
                    {% set value = null %}

                    {% set bricks1 = object1.get(fieldName) %}
                    {% set bricks2 = object2.get(fieldName) %}

                    {% if bricks1 is not empty and bricks2 is not empty %}
                        {% if lfd is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Localizedfields') %}
                            {% for language in validLanguages %}
                                {% for localizedFieldDefinition in lfd.getFieldDefinitions() %}
                                    <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                                        <td>{{ localizedFieldDefinition.getTitle()|trans([],'admin') }} ({{ language }})</td>
                                        <td>{{ localizedFieldDefinition.getName() }}</td>

                                        {% set v1,localizedBrickValue1 = null,null %}
                                        {% set v2,localizedBrickValue2 = null,null %}

                                        {% if bricks1 %}
                                            {% set brickGetter = \"get\" ~ asAllowedType %}
                                            {% set brick1Value = attribute(bricks1,brickGetter) %}

                                            {% if brick1Value %}
                                                {% set localizedBrickValues = brick1Value.getLocalizedFields() %}
                                                {% set localizedBrickValue1 = localizedBrickValues.getLocalizedValue(localizedFieldDefinition.getName(), language) %}
                                                {% if localizedBrickValue1 != false %}
                                                    {% set v1 = localizedFieldDefinition.getVersionPreview(localizedBrickValue1) %}
                                                {% else %}
                                                    {% set localizedBrickValue1 = null %}
                                                {% endif %}
                                            {% endif %}
                                        {% endif %}

                                        {% if bricks2 %}
                                            {% set brickGetter = \"get\" ~ asAllowedType %}
                                            {% set brick2Value = attribute(bricks2,brickGetter) %}

                                            {% if brick2Value %}
                                                {% set localizedBrickValues = brick2Value.getLocalizedFields() %}
                                                {% set localizedBrickValue2 = localizedBrickValues.getLocalizedValue(localizedFieldDefinition.getName(), language) %}
                                                {% if localizedBrickValue2 != false %}
                                                    {% set v2 = localizedFieldDefinition.getVersionPreview(localizedBrickValue2) %}
                                                {% else %}
                                                    {% set localizedBrickValue2 = null %}
                                                {% endif %}
                                            {% endif %}
                                        {% endif %}

                                        {% if isImportPreview is not defined or isNew is not defined %}
                                            <td>{{ v1 | raw }}</td>
                                        {% endif %}
                                        {% set fieldIsEqual = localizedFieldDefinition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\EqualComparisonInterface') and not localizedFieldDefinition.isEqual(localizedBrickValue1, localizedBrickValue2) ? 'modified1' : '' %}
                                        <td class=\"{{ fieldIsEqual }}\">{{ v2 }}</td>
                                    </tr>
                                {% endfor %}
                                {% set break = true %}
                            {% endfor %}
                        {% else %}
                            {% set v1 = null %}

                            {% if bricks1 %}
                                {% set brickGetter = \"get\" ~ asAllowedType %}
                                {% set brick1Value = attribute(bricks1,brickGetter) %}

                                {% if brick1Value %}
                                    {% set brick1Value = brick1Value.getValueForFieldName(lfd.getName()) %}
                                    {% if brick1Value != false %}
                                        {% set v1 = lfd.getVersionPreview(brick1Value) %}
                                    {% else %}
                                        {% set brick1Value = null %}
                                    {% endif %}

                                {% endif %}
                            {% endif %}

                            {% set v2 = null %}

                            {% if bricks2 %}
                                {% set brickGetter = \"get\" ~ asAllowedType %}
                                {% set brick2Value = attribute(bricks2,brickGetter) %}

                                {% if brick2Value %}
                                    {% set brick2Value = brick2Value.getValueForFieldName(lfd.getName()) ?? null %}
                                    {% if brick2Value != false %}
                                        {% set v2 = lfd.getVersionPreview(brick2Value) %}
                                    {% else %}
                                        {% set brick2Value = null %}
                                    {% endif %}
                                {% endif %}
                            {% endif %}

                            <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                                <td>{{ asAllowedType|capitalize ~ \" - \" ~ lfd.getTitle() ? lfd.getTitle()|trans([],'admin') }}</td>
                                <td>{{ lfd.getName() }}</td>
                                {% if isImportPreview is not defined or isNew is not defined %}
                                    <td>{{ v1 | raw }}</td>
                                {% endif %}
                                {% set fieldIsEqual = lfd is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\EqualComparisonInterface') and not lfd.isEqual(brick1Value,brick2Value) ? 'modified' : '' %}
                                <td class=\"{{ fieldIsEqual }}\">{{ v2 | raw }}</td>
                            </tr>
                        {% endif %}
                    {% endif %}

                {% endfor %}
            {% endfor %}
        {% elseif definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Fieldcollections') %}
            {% set fields1,fields2 = object1.get(fieldName),object2.get(fieldName) %}
            {% set fieldDefinitions1,fieldItems1 = null,null %}
            {% set fieldDefinitions2,fieldItems2 = null,null %}
            {% set skipFieldItems2 = [] %}

            {% if fields1 %}
                {% set fieldDefinitions1 = fields1.getItemDefinitions() %}
                {% set fieldItems1 = fields1.getItems() %}
            {% endif %}

            {% if fields2 %}
                {% set fieldDefinitions2 = fields2.getItemDefinitions() %}
                {% set fieldItems2 = fields2.getItems() %}
            {% endif %}

            {% if fieldItems1 is iterable and fieldItems1|length > 0 %}
                {% for fkey1,fieldItem1  in fieldItems1 %}
                    {% set fieldKeys1 = fieldDefinitions1[fieldItem1.getType()].getFieldDefinitions() %}

                    {% if fieldItems2[fkey1] is defined and fieldItem1.getType() == fieldItems2[fkey1].getType() %}
                        {% set ffkey2 = fieldItems2[fkey1]  %}
                        {% set fieldKeys2 = fieldDefinitions2[ffkey2.getType()].getFieldDefinitions() %}
                        {% set skipFieldItems2 = skipFieldItems2|merge([fkey1, fkey1]) %}
                    {% endif %}

                    {% for fkey,fieldKey1 in fieldKeys1 %}
                        {% set v2,keyData2 = null,null %}
                        {% set keyData1 = fieldItem1.get(fieldKey1.name) %}
                        {% set v1 = fieldKey1.getVersionPreview(keyData1) %}

                        {% if ffkey2 is defined and fieldKeys2[fkey] is defined %}
                            {% set keyData2 = ffkey2.get(fieldKeys2[fkey].name) %}
                            {% set v2 = fieldKey1.getVersionPreview(keyData2) %}
                        {% endif %}

                        <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                        <td>{{ fieldItem1.getType() ~ \" - \" ~ fieldKey1.getTitle()|trans([],'admin') }}</td>
                        <td>{{ fieldKey1.name }}</td>
                            {% if isImportPreview is not defined or isNew is not defined %}
                                <td>{{ v1 | raw }}</td>
                            {% endif %}
                            {% set fieldIsEqual = fieldKey1 is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\EqualComparisonInterface') and not fieldKey1.isEqual(keyData1, keyData2) ? 'modified' : '' %}
                            <td class=\"{{ fieldIsEqual }}\">{{ v2 | raw }}</td>
                        </tr>
                    {% endfor %}
                {% endfor %}
            {% endif %}

            {% if fieldItems2 is iterable and fieldItems2|length > 0 %}

                {% for fkey2,fieldItem2  in fieldItems2 %}
                    {% if fkey2 not in skipFieldItems2|keys %}
                        {% set fieldKeys2 = fieldDefinitions2[fieldItem2.getType()].getFieldDefinitions() %}

                        {% if fieldItems1[fkey2] is defined and fieldItem2.getType() == fieldItems1[fkey2].getType() %}
                            {% set ffkey1 = fieldItems1[fkey2]  %}
                            {% set fieldKeys1 = fieldDefinitions1[ffkey1.getType()].getFieldDefinitions() %}
                        {% endif %}

                        {% for fkey,fieldKey2 in fieldKeys2 %}
                            {% set v1,keyData1 = null,null %}
                            {% set keyData2 = fieldItem2.get(fieldKey2.name) %}
                            {% set v2 = fieldKey2.getVersionPreview(keyData2) %}

                            {% if ffkey1 is defined and fieldKeys1[fkey] is defined %}
                                {% set keyData1 = ffkey1.get(fieldKeys1[fkey].name) %}
                                {% set v1 = fieldKey2.getVersionPreview(keyData1) %}
                            {% endif %}

                            <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                                <td>{{ fieldItem2.getType() ~ \" - \" ~ fieldKey2.getTitle()|trans([],'admin') }}</td>
                                <td>{{ fieldKey2.name }}</td>
                                {% if isImportPreview is not defined or isNew is not defined %}
                                    <td>{{ v1 | raw }}</td>
                                {% endif %}
                                {% set fieldIsEqual = fieldKey2 is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\EqualComparisonInterface') and not fieldKey2.isEqual(v1, keyData2) ? 'modified' : '' %}
                                <td class=\"{{ fieldIsEqual }}\">{{ v2 | raw }}</td>
                            </tr>
                        {% endfor %}
                    {% endif %}
                {% endfor %}
            {% endif %}
        {% else %}
            {% set keyData1 = object1.getValueForFieldName(fieldName) is not same as(false) ? object1.getValueForFieldName(fieldName) : null %}
            {% set v1 = definition.getVersionPreview(keyData1) %}
            {% set keyData2 = object2.getValueForFieldName(fieldName) is not same as(false) ? object2.getValueForFieldName(fieldName) : null %}
            {% set v2 = definition.getVersionPreview(keyData2) %}

            <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                <td>{{ definition.getTitle()|trans([],'admin') }}</td>
                <td>{{ definition.getName() }}</td>
                {% if isImportPreview is not defined or isNew is not defined %}
                    <td>{{ v1 | raw }}</td>
                {% endif %}
                {% set fieldIsEqual = definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\EqualComparisonInterface') and not definition.isEqual(keyData1, keyData2) ? 'modified' : '' %}
                <td class=\"{{ fieldIsEqual }}\">{{ v2 | raw }}</td>
            </tr>
        {% endif %}
    {% endfor %}
</table>

</body>
</html>
", "@PimcoreAdmin/admin/data_object/data_object/diff_versions.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/data_object/data_object/diff_versions.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 10, "if" => 16, "for" => 76);
        static $filters = array("escape" => 32, "date" => 32, "nl2br" => 58, "trans" => 89, "raw" => 92, "merge" => 124, "capitalize" => 254, "length" => 283, "keys" => 319);
        static $functions = array("pimcore_object_classificationstore_group" => 129, "pimcore_object_classificationstore_get_field_definition_from_json" => 134, "pimcore_object_brick_definition_key" => 161);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'for'],
                ['escape', 'date', 'nl2br', 'trans', 'raw', 'merge', 'capitalize', 'length', 'keys'],
                ['pimcore_object_classificationstore_group', 'pimcore_object_classificationstore_get_field_definition_from_json', 'pimcore_object_brick_definition_key']
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
