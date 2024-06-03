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

/* @PimcoreAdmin/admin/data_object/data_object/preview_version.html.twig */
class __TwigTemplate_4b0217219d455c532ba2ec206acd6137 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/data_object/data_object/preview_version.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/data_object/data_object/preview_version.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"/bundles/pimcoreadmin/css/object_versions.css\"/>
</head>

<body>


";
        // line 11
        $context["fields"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 11, $this->source); })()), "getClass", [], "method", false, false, true, 11), "getFieldDefinitions", [], "method", false, false, true, 11);
        // line 12
        echo "
<table class=\"preview\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
    <tr>
        <th>Name</th>
        <th>Key</th>
        <th>Value</th>
    </tr>
    <tr class=\"system\">
        <td>Date</td>
        <td>modificationDate</td>
        <td>";
        // line 22
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 22, $this->source); })()), "getModificationDate", [], "method", false, false, true, 22), 22, $this->source), "Y-m-d H:i:s"), "html", null, true);
        echo "</td>
    </tr>
    <tr class=\"system\">
        <td>Path</td>
        <td>path</td>
        <td>";
        // line 27
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 27, $this->source); })()), "getRealFullPath", [], "method", false, false, true, 27), 27, $this->source), "html", null, true);
        echo "</td>
    </tr>
    <tr class=\"system\">
        <td>Published</td>
        <td>published</td>
        <td>";
        // line 32
        echo ((twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 32, $this->source); })()), "getPublished", [], "method", false, false, true, 32)) ? ("true") : ("false"));
        echo "</td>
    </tr>
    ";
        // line 34
        if ((isset($context["versionNote"]) || array_key_exists("versionNote", $context) ? $context["versionNote"] : (function () { throw new RuntimeError('Variable "versionNote" does not exist.', 34, $this->source); })())) {
            // line 35
            echo "        <tr class=\"system\">
            <td>Version Note</td>
            <td>&nbsp;</td>
            <td>";
            // line 38
            echo twig_nl2br(twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["versionNote"]) || array_key_exists("versionNote", $context) ? $context["versionNote"] : (function () { throw new RuntimeError('Variable "versionNote" does not exist.', 38, $this->source); })()), 38, $this->source), "html", null, true));
            echo "</td>
        </tr>
    ";
        }
        // line 41
        echo "
    <tr class=\"\">
        <td colspan=\"3\">&nbsp;</td>
    </tr>
    ";
        // line 45
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["fields"]) || array_key_exists("fields", $context) ? $context["fields"] : (function () { throw new RuntimeError('Variable "fields" does not exist.', 45, $this->source); })()));
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
            // line 46
            echo "        ";
            if ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Localizedfields")) {
                // line 47
                echo "            ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 47, $this->source); })()));
                foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                    // line 48
                    echo "                ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["definition"], "getFieldDefinitions", [], "method", false, false, true, 48));
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
                        // line 49
                        echo "                    <tr ";
                        if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 49) % 2 != 0)) {
                            echo "class=\"odd\"";
                        }
                        echo ">
                        <td>";
                        // line 50
                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 50), 50, $this->source), [], "admin"), "html", null, true);
                        echo " (";
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 50, $this->source), "html", null, true);
                        echo ")</td>
                        <td>";
                        // line 51
                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 51), 51, $this->source), "html", null, true);
                        echo "</td>
                        <td>
                                ";
                        // line 53
                        if (twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 53, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 53)) {
                            // line 54
                            echo "                                    ";
                            echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getVersionPreview", [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 54, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 54), "getLocalizedValue", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 54), $context["language"]], "method", false, false, true, 54)], "method", false, false, true, 54), 54, $this->source);
                            echo "
                                ";
                        }
                        // line 56
                        echo "                        </td>
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
                    // line 59
                    echo "            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 60
                echo "        ";
            } elseif ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Objectbricks")) {
                // line 61
                echo "            ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["definition"], "getAllowedTypes", [], "method", false, false, true, 61));
                foreach ($context['_seq'] as $context["_key"] => $context["asAllowedType"]) {
                    // line 62
                    echo "                ";
                    $context["collectionDef"] = Pimcore\Model\DataObject\Objectbrick\Definition::getByKey($this->sandbox->ensureToStringAllowed($context["asAllowedType"], 62, $this->source));
                    // line 63
                    echo "
                ";
                    // line 64
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["collectionDef"]) || array_key_exists("collectionDef", $context) ? $context["collectionDef"] : (function () { throw new RuntimeError('Variable "collectionDef" does not exist.', 64, $this->source); })()), "getFieldDefinitions", [], "method", false, false, true, 64));
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
                        // line 65
                        echo "                    ";
                        $context["value"] = null;
                        // line 66
                        echo "
                    ";
                        // line 67
                        $context["fieldGetter"] = ("get" . twig_capitalize_string_filter($this->env, $this->sandbox->ensureToStringAllowed($context["fieldName"], 67, $this->source)));
                        // line 68
                        echo "                    ";
                        $context["brick"] = twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 68, $this->source); })()), (isset($context["fieldGetter"]) || array_key_exists("fieldGetter", $context) ? $context["fieldGetter"] : (function () { throw new RuntimeError('Variable "fieldGetter" does not exist.', 68, $this->source); })()), [], "any", false, false, true, 68);
                        // line 69
                        echo "
                    ";
                        // line 70
                        if ( !twig_test_empty((isset($context["brick"]) || array_key_exists("brick", $context) ? $context["brick"] : (function () { throw new RuntimeError('Variable "brick" does not exist.', 70, $this->source); })()))) {
                            // line 71
                            echo "                        ";
                            $context["allowedMethod"] = ("get" . $this->sandbox->ensureToStringAllowed($context["asAllowedType"], 71, $this->source));
                            // line 72
                            echo "                        ";
                            $context["brickValue"] = twig_get_attribute($this->env, $this->source, (isset($context["brick"]) || array_key_exists("brick", $context) ? $context["brick"] : (function () { throw new RuntimeError('Variable "brick" does not exist.', 72, $this->source); })()), (isset($context["allowedMethod"]) || array_key_exists("allowedMethod", $context) ? $context["allowedMethod"] : (function () { throw new RuntimeError('Variable "allowedMethod" does not exist.', 72, $this->source); })()), [], "any", false, false, true, 72);
                            // line 73
                            echo "
                        ";
                            // line 74
                            if ($this->env->getTest('instanceof')->getCallable()($context["lfd"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Localizedfields")) {
                                // line 75
                                echo "                            ";
                                $context['_parent'] = $context;
                                $context['_seq'] = twig_ensure_traversable((isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 75, $this->source); })()));
                                foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                                    // line 76
                                    echo "                                ";
                                    $context['_parent'] = $context;
                                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["lfd"], "getFieldDefinitions", [], "method", false, false, true, 76));
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
                                        // line 77
                                        echo "                                    <tr ";
                                        if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 77) % 2 != 0)) {
                                            echo "class=\"odd\"";
                                        }
                                        echo ">
                                        <td>";
                                        // line 78
                                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getTitle", [], "method", false, false, true, 78), 78, $this->source), [], "admin"), "html", null, true);
                                        echo " (";
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 78, $this->source), "html", null, true);
                                        echo ")</td>
                                        <td>";
                                        // line 79
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getName", [], "method", false, false, true, 79), 79, $this->source), "html", null, true);
                                        echo "</td>
                                        <td>
                                            ";
                                        // line 81
                                        if ((isset($context["brickValue"]) || array_key_exists("brickValue", $context) ? $context["brickValue"] : (function () { throw new RuntimeError('Variable "brickValue" does not exist.', 81, $this->source); })())) {
                                            // line 82
                                            echo "                                                ";
                                            $context["localizedBrickValues"] = twig_get_attribute($this->env, $this->source, (isset($context["brickValue"]) || array_key_exists("brickValue", $context) ? $context["brickValue"] : (function () { throw new RuntimeError('Variable "brickValue" does not exist.', 82, $this->source); })()), "getLocalizedFields", [], "method", false, false, true, 82);
                                            // line 83
                                            echo "                                                ";
                                            $context["localizedBrickValue"] = twig_get_attribute($this->env, $this->source, (isset($context["localizedBrickValues"]) || array_key_exists("localizedBrickValues", $context) ? $context["localizedBrickValues"] : (function () { throw new RuntimeError('Variable "localizedBrickValues" does not exist.', 83, $this->source); })()), "getLocalizedValue", [twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getName", [], "method", false, false, true, 83), $context["language"]], "method", false, false, true, 83);
                                            // line 84
                                            echo "                                                ";
                                            echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getVersionPreview", [(isset($context["localizedBrickValue"]) || array_key_exists("localizedBrickValue", $context) ? $context["localizedBrickValue"] : (function () { throw new RuntimeError('Variable "localizedBrickValue" does not exist.', 84, $this->source); })())], "method", false, false, true, 84), 84, $this->source);
                                            echo "
                                            ";
                                        }
                                        // line 86
                                        echo "                                        </td>
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
                                    // line 89
                                    echo "                            ";
                                }
                                $_parent = $context['_parent'];
                                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
                                $context = array_intersect_key($context, $_parent) + $_parent;
                                // line 90
                                echo "                        ";
                            } else {
                                // line 91
                                echo "                            ";
                                if ((isset($context["brickValue"]) || array_key_exists("brickValue", $context) ? $context["brickValue"] : (function () { throw new RuntimeError('Variable "brickValue" does not exist.', 91, $this->source); })())) {
                                    // line 92
                                    echo "                                ";
                                    $context["value"] = twig_get_attribute($this->env, $this->source, $context["lfd"], "getVersionPreview", [twig_get_attribute($this->env, $this->source, (isset($context["brickValue"]) || array_key_exists("brickValue", $context) ? $context["brickValue"] : (function () { throw new RuntimeError('Variable "brickValue" does not exist.', 92, $this->source); })()), "getValueForFieldName", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 92)], "method", false, false, true, 92)], "method", false, false, true, 92);
                                    // line 93
                                    echo "                            ";
                                }
                                // line 94
                                echo "                            <tr ";
                                if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 94) % 2 != 0)) {
                                    echo "class=\"odd\"";
                                }
                                echo ">
                                <td>";
                                // line 95
                                echo twig_escape_filter($this->env, ((twig_capitalize_string_filter($this->env, $this->sandbox->ensureToStringAllowed($context["asAllowedType"], 95, $this->source)) . " - ") . $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 95), 95, $this->source), [], "admin")), "html", null, true);
                                echo "</td>
                                <td>";
                                // line 96
                                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 96), 96, $this->source), "html", null, true);
                                echo "</td>
                                <td>";
                                // line 97
                                echo $this->sandbox->ensureToStringAllowed((isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 97, $this->source); })()), 97, $this->source);
                                echo "</td>
                            </tr>
                        ";
                            }
                            // line 100
                            echo "                    ";
                        }
                        // line 101
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
                    // line 103
                    echo "            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['asAllowedType'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 104
                echo "        ";
            } elseif ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Classificationstore")) {
                // line 105
                echo "            ";
                $context["storedata"] = twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 105, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 105);
                // line 106
                echo "            ";
                $context["activeGroups"] = [];
                // line 107
                echo "
            ";
                // line 108
                if ((isset($context["storedata"]) || array_key_exists("storedata", $context) ? $context["storedata"] : (function () { throw new RuntimeError('Variable "storedata" does not exist.', 108, $this->source); })())) {
                    // line 109
                    echo "                ";
                    $context["activeGroups"] = twig_get_attribute($this->env, $this->source, (isset($context["storedata"]) || array_key_exists("storedata", $context) ? $context["storedata"] : (function () { throw new RuntimeError('Variable "storedata" does not exist.', 109, $this->source); })()), "getActiveGroups", [], "method", false, false, true, 109);
                    // line 110
                    echo "            ";
                }
                // line 111
                echo "            ";
                if ( !twig_test_empty((isset($context["activeGroups"]) || array_key_exists("activeGroups", $context) ? $context["activeGroups"] : (function () { throw new RuntimeError('Variable "activeGroups" does not exist.', 111, $this->source); })()))) {
                    // line 112
                    echo "                ";
                    $context["languages"] = ["default"];
                    // line 113
                    echo "                ";
                    if (twig_get_attribute($this->env, $this->source, $context["definition"], "isLocalized", [], "method", false, false, true, 113)) {
                        // line 114
                        echo "                    ";
                        $context["languages"] = twig_array_merge($this->sandbox->ensureToStringAllowed((isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 114, $this->source); })()), 114, $this->source), $this->sandbox->ensureToStringAllowed((isset($context["languages"]) || array_key_exists("languages", $context) ? $context["languages"] : (function () { throw new RuntimeError('Variable "languages" does not exist.', 114, $this->source); })()), 114, $this->source));
                        // line 115
                        echo "                ";
                    }
                    // line 116
                    echo "
                ";
                    // line 117
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_array_filter($this->env, (isset($context["activeGroups"]) || array_key_exists("activeGroups", $context) ? $context["activeGroups"] : (function () { throw new RuntimeError('Variable "activeGroups" does not exist.', 117, $this->source); })()), function ($__enabled__, $__activeGroupId__) use ($context, $macros) { $context["enabled"] = $__enabled__; $context["activeGroupId"] = $__activeGroupId__; return  !twig_test_empty(twig_get_attribute($this->env, $this->source, (isset($context["activeGroups"]) || array_key_exists("activeGroups", $context) ? $context["activeGroups"] : (function () { throw new RuntimeError('Variable "activeGroups" does not exist.', 117, $this->source); })()), $context["activeGroupId"], [], "array", false, false, true, 117)); }));
                    foreach ($context['_seq'] as $context["activeGroupId"] => $context["enabled"]) {
                        // line 118
                        echo "                    ";
                        $context["groupDefinition"] = Pimcore\Model\DataObject\Classificationstore\GroupConfig::getById($this->sandbox->ensureToStringAllowed($context["activeGroupId"], 118, $this->source));
                        // line 119
                        echo "                    ";
                        if ( !twig_test_empty((isset($context["groupDefinition"]) || array_key_exists("groupDefinition", $context) ? $context["groupDefinition"] : (function () { throw new RuntimeError('Variable "groupDefinition" does not exist.', 119, $this->source); })()))) {
                            // line 120
                            echo "                        ";
                            $context["keyGroupRelations"] = twig_get_attribute($this->env, $this->source, (isset($context["groupDefinition"]) || array_key_exists("groupDefinition", $context) ? $context["groupDefinition"] : (function () { throw new RuntimeError('Variable "groupDefinition" does not exist.', 120, $this->source); })()), "getRelations", [], "method", false, false, true, 120);
                            // line 121
                            echo "
                        ";
                            // line 122
                            $context['_parent'] = $context;
                            $context['_seq'] = twig_ensure_traversable((isset($context["keyGroupRelations"]) || array_key_exists("keyGroupRelations", $context) ? $context["keyGroupRelations"] : (function () { throw new RuntimeError('Variable "keyGroupRelations" does not exist.', 122, $this->source); })()));
                            foreach ($context['_seq'] as $context["_key"] => $context["keyGroupRelation"]) {
                                // line 123
                                echo "                            ";
                                $context["keyDef"] = $this->extensions['Pimcore\Twig\Extension\PimcoreObjectExtension']->getFieldDefinitionFromJson($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getDefinition", [], "method", false, false, true, 123), 123, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getType", [], "method", false, false, true, 123), 123, $this->source));
                                // line 124
                                echo "
                            ";
                                // line 125
                                if ( !twig_test_empty((isset($context["keyDef"]) || array_key_exists("keyDef", $context) ? $context["keyDef"] : (function () { throw new RuntimeError('Variable "keyDef" does not exist.', 125, $this->source); })()))) {
                                    // line 126
                                    echo "                                ";
                                    $context['_parent'] = $context;
                                    $context['_seq'] = twig_ensure_traversable((isset($context["languages"]) || array_key_exists("languages", $context) ? $context["languages"] : (function () { throw new RuntimeError('Variable "languages" does not exist.', 126, $this->source); })()));
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
                                        // line 127
                                        echo "                                    ";
                                        $context["keyData"] = (((isset($context["storedata"]) || array_key_exists("storedata", $context) ? $context["storedata"] : (function () { throw new RuntimeError('Variable "storedata" does not exist.', 127, $this->source); })())) ? (twig_get_attribute($this->env, $this->source, (isset($context["storedata"]) || array_key_exists("storedata", $context) ? $context["storedata"] : (function () { throw new RuntimeError('Variable "storedata" does not exist.', 127, $this->source); })()), "getLocalizedKeyValue", [$context["activeGroupId"], twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getKeyId", [], "method", false, false, true, 127), $context["language"], true, true], "method", false, false, true, 127)) : (null));
                                        // line 128
                                        echo "
                                    ";
                                        // line 129
                                        $context["preview"] = twig_get_attribute($this->env, $this->source, (isset($context["keyDef"]) || array_key_exists("keyDef", $context) ? $context["keyDef"] : (function () { throw new RuntimeError('Variable "keyDef" does not exist.', 129, $this->source); })()), "getVersionPreview", [(isset($context["keyData"]) || array_key_exists("keyData", $context) ? $context["keyData"] : (function () { throw new RuntimeError('Variable "keyData" does not exist.', 129, $this->source); })())], "method", false, false, true, 129);
                                        // line 130
                                        echo "                                    <tr ";
                                        if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 130) % 2 != 0)) {
                                            echo "class=\"odd\"";
                                        }
                                        echo ">
                                        <td>";
                                        // line 131
                                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getTitle", [], "method", false, false, true, 131), 131, $this->source), [], "admin"), "html", null, true);
                                        echo "</td>
                                        <td>";
                                        // line 132
                                        echo twig_escape_filter($this->env, (($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["groupDefinition"]) || array_key_exists("groupDefinition", $context) ? $context["groupDefinition"] : (function () { throw new RuntimeError('Variable "groupDefinition" does not exist.', 132, $this->source); })()), "getName", [], "method", false, false, true, 132), 132, $this->source) . "-") . $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getName", [], "method", false, false, true, 132), 132, $this->source)), "html", null, true);
                                        echo " ";
                                        ((twig_get_attribute($this->env, $this->source, $context["definition"], "isLocalized", [], "method", false, false, true, 132)) ? (print (twig_escape_filter($this->env, ("/ " . $context["language"]), "html", null, true))) : (print ("")));
                                        echo "</td>
                                        ";
                                        // line 133
                                        if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
                                            // line 134
                                            echo "                                            <td>";
                                            echo $this->sandbox->ensureToStringAllowed((isset($context["preview"]) || array_key_exists("preview", $context) ? $context["preview"] : (function () { throw new RuntimeError('Variable "preview" does not exist.', 134, $this->source); })()), 134, $this->source);
                                            echo "</td>
                                        ";
                                        }
                                        // line 136
                                        echo "                                    </tr>
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
                                    // line 138
                                    echo "                            ";
                                }
                                // line 139
                                echo "                        ";
                            }
                            $_parent = $context['_parent'];
                            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['keyGroupRelation'], $context['_parent'], $context['loop']);
                            $context = array_intersect_key($context, $_parent) + $_parent;
                            // line 140
                            echo "                    ";
                        }
                        // line 141
                        echo "                ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['activeGroupId'], $context['enabled'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 142
                    echo "            ";
                }
                // line 143
                echo "        ";
            } elseif ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Fieldcollections")) {
                // line 144
                echo "            ";
                $context["fields"] = twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 144, $this->source); })()), "get", [$context["fieldName"]], "method", false, false, true, 144);
                // line 145
                echo "            ";
                $context["fieldDefinitions"] = null;
                // line 146
                echo "            ";
                $context["fieldItems"] = null;
                // line 147
                echo "
            ";
                // line 148
                if ((isset($context["fields"]) || array_key_exists("fields", $context) ? $context["fields"] : (function () { throw new RuntimeError('Variable "fields" does not exist.', 148, $this->source); })())) {
                    // line 149
                    echo "                ";
                    $context["fieldDefinitions"] = twig_get_attribute($this->env, $this->source, (isset($context["fields"]) || array_key_exists("fields", $context) ? $context["fields"] : (function () { throw new RuntimeError('Variable "fields" does not exist.', 149, $this->source); })()), "getItemDefinitions", [], "method", false, false, true, 149);
                    // line 150
                    echo "                ";
                    $context["fieldItems"] = twig_get_attribute($this->env, $this->source, (isset($context["fields"]) || array_key_exists("fields", $context) ? $context["fields"] : (function () { throw new RuntimeError('Variable "fields" does not exist.', 150, $this->source); })()), "getItems", [], "method", false, false, true, 150);
                    // line 151
                    echo "            ";
                }
                // line 152
                echo "
            ";
                // line 153
                if ((is_iterable((isset($context["fieldItems"]) || array_key_exists("fieldItems", $context) ? $context["fieldItems"] : (function () { throw new RuntimeError('Variable "fieldItems" does not exist.', 153, $this->source); })())) && (twig_length_filter($this->env, (isset($context["fieldItems"]) || array_key_exists("fieldItems", $context) ? $context["fieldItems"] : (function () { throw new RuntimeError('Variable "fieldItems" does not exist.', 153, $this->source); })())) > 0))) {
                    // line 154
                    echo "                ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable((isset($context["fieldItems"]) || array_key_exists("fieldItems", $context) ? $context["fieldItems"] : (function () { throw new RuntimeError('Variable "fieldItems" does not exist.', 154, $this->source); })()));
                    foreach ($context['_seq'] as $context["fkey"] => $context["fieldItem"]) {
                        // line 155
                        echo "                    ";
                        $context["fieldKeys"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["fieldDefinitions"]) || array_key_exists("fieldDefinitions", $context) ? $context["fieldDefinitions"] : (function () { throw new RuntimeError('Variable "fieldDefinitions" does not exist.', 155, $this->source); })()), twig_get_attribute($this->env, $this->source, $context["fieldItem"], "getType", [], "method", false, false, true, 155), [], "array", false, false, true, 155), "getFieldDefinitions", [], "method", false, false, true, 155);
                        // line 156
                        echo "                    ";
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable((isset($context["fieldKeys"]) || array_key_exists("fieldKeys", $context) ? $context["fieldKeys"] : (function () { throw new RuntimeError('Variable "fieldKeys" does not exist.', 156, $this->source); })()));
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
                        foreach ($context['_seq'] as $context["_key"] => $context["fieldKey"]) {
                            // line 157
                            echo "                        ";
                            if ($this->env->getTest('instanceof')->getCallable()($context["fieldKey"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Localizedfields")) {
                                // line 158
                                echo "                            ";
                                $context['_parent'] = $context;
                                $context['_seq'] = twig_ensure_traversable((isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 158, $this->source); })()));
                                foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                                    // line 159
                                    echo "                                ";
                                    $context['_parent'] = $context;
                                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["fieldKey"], "getFieldDefinitions", [], "method", false, false, true, 159));
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
                                        // line 160
                                        echo "                                    <tr ";
                                        if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 160) % 2 != 0)) {
                                            echo "class=\"odd\"";
                                        }
                                        echo ">
                                        <td>";
                                        // line 161
                                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 161), 161, $this->source), [], "admin"), "html", null, true);
                                        echo " (";
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 161, $this->source), "html", null, true);
                                        echo ")</td>
                                        <td>";
                                        // line 162
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldItem"], "fieldName", [], "any", false, false, true, 162), 162, $this->source), "html", null, true);
                                        echo " - ";
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 162), 162, $this->source), "html", null, true);
                                        echo "/";
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["language"], 162, $this->source), "html", null, true);
                                        echo "</td>
                                        <td>";
                                        // line 163
                                        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["fieldItem"], "Localizedfields", [], "any", false, false, true, 163), "getLocalizedValue", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 163), $context["language"]], "method", false, false, true, 163), 163, $this->source), "html", null, true);
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
                                    // line 166
                                    echo "                            ";
                                }
                                $_parent = $context['_parent'];
                                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
                                $context = array_intersect_key($context, $_parent) + $_parent;
                                // line 167
                                echo "                        ";
                            } else {
                                // line 168
                                echo "                            ";
                                $context["value"] = twig_get_attribute($this->env, $this->source, $context["fieldItem"], "get", [twig_get_attribute($this->env, $this->source, $context["fieldKey"], "getName", [], "method", false, false, true, 168)], "method", false, false, true, 168);
                                // line 169
                                echo "                            <tr ";
                                if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 169) % 2 != 0)) {
                                    echo "class=\"odd\"";
                                }
                                echo ">
                                <td>";
                                // line 170
                                echo twig_escape_filter($this->env, (($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldItem"], "getType", [], "method", false, false, true, 170), 170, $this->source) . " - ") . $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldKey"], "getTitle", [], "method", false, false, true, 170), 170, $this->source), [], "admin")), "html", null, true);
                                echo "</td>
                                <td>";
                                // line 171
                                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldKey"], "getName", [], "method", false, false, true, 171), 171, $this->source), "html", null, true);
                                echo "</td>
                                <td>";
                                // line 172
                                echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["fieldKey"], "getVersionPreview", [(isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 172, $this->source); })())], "method", false, false, true, 172), 172, $this->source);
                                echo "</td>
                            </tr>
                        ";
                            }
                            // line 175
                            echo "                    ";
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
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['fieldKey'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 176
                        echo "                ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['fkey'], $context['fieldItem'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 177
                    echo "            ";
                }
                // line 178
                echo "        ";
            } else {
                // line 179
                echo "            <tr ";
                if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 179) % 2 != 0)) {
                    echo "class=\"odd\"";
                }
                echo ">
                <td>";
                // line 180
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getTitle", [], "method", false, false, true, 180), 180, $this->source), [], "admin"), "html", null, true);
                echo "</td>
                <td>";
                // line 181
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getName", [], "method", false, false, true, 181), 181, $this->source), "html", null, true);
                echo "</td>
                <td>";
                // line 182
                echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getVersionPreview", [twig_get_attribute($this->env, $this->source, (isset($context["object"]) || array_key_exists("object", $context) ? $context["object"] : (function () { throw new RuntimeError('Variable "object" does not exist.', 182, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 182)], "method", false, false, true, 182), 182, $this->source);
                echo "</td>
            </tr>
        ";
            }
            // line 185
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
        // line 186
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
        return "@PimcoreAdmin/admin/data_object/data_object/preview_version.html.twig";
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
        return array (  762 => 186,  748 => 185,  742 => 182,  738 => 181,  734 => 180,  727 => 179,  724 => 178,  721 => 177,  715 => 176,  701 => 175,  695 => 172,  691 => 171,  687 => 170,  680 => 169,  677 => 168,  674 => 167,  668 => 166,  651 => 163,  643 => 162,  637 => 161,  630 => 160,  612 => 159,  607 => 158,  604 => 157,  586 => 156,  583 => 155,  578 => 154,  576 => 153,  573 => 152,  570 => 151,  567 => 150,  564 => 149,  562 => 148,  559 => 147,  556 => 146,  553 => 145,  550 => 144,  547 => 143,  544 => 142,  538 => 141,  535 => 140,  529 => 139,  526 => 138,  511 => 136,  505 => 134,  503 => 133,  497 => 132,  493 => 131,  486 => 130,  484 => 129,  481 => 128,  478 => 127,  460 => 126,  458 => 125,  455 => 124,  452 => 123,  448 => 122,  445 => 121,  442 => 120,  439 => 119,  436 => 118,  432 => 117,  429 => 116,  426 => 115,  423 => 114,  420 => 113,  417 => 112,  414 => 111,  411 => 110,  408 => 109,  406 => 108,  403 => 107,  400 => 106,  397 => 105,  394 => 104,  388 => 103,  373 => 101,  370 => 100,  364 => 97,  360 => 96,  356 => 95,  349 => 94,  346 => 93,  343 => 92,  340 => 91,  337 => 90,  331 => 89,  315 => 86,  309 => 84,  306 => 83,  303 => 82,  301 => 81,  296 => 79,  290 => 78,  283 => 77,  265 => 76,  260 => 75,  258 => 74,  255 => 73,  252 => 72,  249 => 71,  247 => 70,  244 => 69,  241 => 68,  239 => 67,  236 => 66,  233 => 65,  216 => 64,  213 => 63,  210 => 62,  205 => 61,  202 => 60,  196 => 59,  180 => 56,  174 => 54,  172 => 53,  167 => 51,  161 => 50,  154 => 49,  136 => 48,  131 => 47,  128 => 46,  111 => 45,  105 => 41,  99 => 38,  94 => 35,  92 => 34,  87 => 32,  79 => 27,  71 => 22,  59 => 12,  57 => 11,  45 => 1,);
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


{% set fields = object.getClass().getFieldDefinitions()  %}

<table class=\"preview\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
    <tr>
        <th>Name</th>
        <th>Key</th>
        <th>Value</th>
    </tr>
    <tr class=\"system\">
        <td>Date</td>
        <td>modificationDate</td>
        <td>{{ object.getModificationDate()|date('Y-m-d H:i:s') }}</td>
    </tr>
    <tr class=\"system\">
        <td>Path</td>
        <td>path</td>
        <td>{{ object.getRealFullPath() }}</td>
    </tr>
    <tr class=\"system\">
        <td>Published</td>
        <td>published</td>
        <td>{{ object.getPublished() ? 'true' : 'false' }}</td>
    </tr>
    {% if versionNote %}
        <tr class=\"system\">
            <td>Version Note</td>
            <td>&nbsp;</td>
            <td>{{ versionNote|nl2br }}</td>
        </tr>
    {% endif %}

    <tr class=\"\">
        <td colspan=\"3\">&nbsp;</td>
    </tr>
    {% for fieldName, definition in fields %}
        {% if definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Localizedfields') %}
            {% for language in validLanguages %}
                {% for lfd in definition.getFieldDefinitions() %}
                    <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                        <td>{{ lfd.getTitle()|trans([],'admin') }} ({{ language }})</td>
                        <td>{{ lfd.getName() }}</td>
                        <td>
                                {% if object.getValueForFieldName(fieldName) %}
                                    {{ lfd.getVersionPreview(object.getValueForFieldName(fieldName).getLocalizedValue(lfd.getName(), language)) | raw }}
                                {% endif %}
                        </td>
                    </tr>
                {% endfor %}
            {% endfor %}
        {% elseif definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Objectbricks') %}
            {% for asAllowedType in definition.getAllowedTypes() %}
                {% set collectionDef = pimcore_object_brick_definition_key(asAllowedType) %}

                {% for lfd in collectionDef.getFieldDefinitions() %}
                    {% set value = null %}

                    {% set fieldGetter = \"get\" ~ (fieldName|capitalize) %}
                    {% set brick = attribute(object, fieldGetter) %}

                    {% if brick is not empty %}
                        {% set allowedMethod = \"get\" ~ asAllowedType %}
                        {% set brickValue = attribute(brick,allowedMethod) %}

                        {% if lfd is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Localizedfields') %}
                            {% for language in validLanguages %}
                                {% for localizedFieldDefinition in lfd.getFieldDefinitions() %}
                                    <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                                        <td>{{ localizedFieldDefinition.getTitle()|trans([],'admin') }} ({{ language }})</td>
                                        <td>{{ localizedFieldDefinition.getName() }}</td>
                                        <td>
                                            {% if brickValue %}
                                                {% set localizedBrickValues = brickValue.getLocalizedFields() %}
                                                {% set localizedBrickValue = localizedBrickValues.getLocalizedValue(localizedFieldDefinition.getName(), language) %}
                                                {{ localizedFieldDefinition.getVersionPreview(localizedBrickValue) | raw }}
                                            {% endif %}
                                        </td>
                                    </tr>
                                {% endfor %}
                            {% endfor %}
                        {% else %}
                            {% if brickValue %}
                                {% set value = lfd.getVersionPreview(brickValue.getValueForFieldName(lfd.getName())) %}
                            {% endif %}
                            <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                                <td>{{ asAllowedType|capitalize ~ \" - \" ~ lfd.getTitle()|trans([],'admin')  }}</td>
                                <td>{{ lfd.getName() }}</td>
                                <td>{{ value | raw }}</td>
                            </tr>
                        {% endif %}
                    {% endif %}

                {% endfor %}
            {% endfor %}
        {% elseif definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Classificationstore') %}
            {% set storedata = object.getValueForFieldName(fieldName) %}
            {% set activeGroups = [] %}

            {% if storedata %}
                {% set activeGroups = storedata.getActiveGroups() %}
            {% endif %}
            {% if activeGroups is not empty %}
                {% set languages = ['default'] %}
                {% if definition.isLocalized() %}
                    {% set languages = validLanguages|merge(languages) %}
                {% endif %}

                {% for activeGroupId, enabled in activeGroups|filter((enabled, activeGroupId) => activeGroups[activeGroupId] is not empty) %}
                    {% set groupDefinition = pimcore_object_classificationstore_group(activeGroupId) %}
                    {% if groupDefinition is not empty %}
                        {% set keyGroupRelations = groupDefinition.getRelations() %}

                        {% for keyGroupRelation in keyGroupRelations %}
                            {% set keyDef = pimcore_object_classificationstore_get_field_definition_from_json(keyGroupRelation.getDefinition(), keyGroupRelation.getType())  %}

                            {% if keyDef is not empty %}
                                {% for language in languages %}
                                    {% set keyData = storedata ? storedata.getLocalizedKeyValue(activeGroupId, keyGroupRelation.getKeyId(), language, true, true) : null %}

                                    {% set preview = keyDef.getVersionPreview(keyData) %}
                                    <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                                        <td>{{ definition.getTitle()|trans([],'admin') }}</td>
                                        <td>{{ groupDefinition.getName() ~ \"-\" ~ keyGroupRelation.getName() }} {{ definition.isLocalized() ? \"/ \" ~ language : \"\"  }}</td>
                                        {% if isImportPreview is not defined or isNew is not defined %}
                                            <td>{{ preview | raw }}</td>
                                        {% endif %}
                                    </tr>
                                {% endfor %}
                            {% endif %}
                        {% endfor %}
                    {% endif %}
                {% endfor %}
            {% endif %}
        {% elseif definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Fieldcollections') %}
            {% set fields = object.get(fieldName) %}
            {% set fieldDefinitions = null %}
            {% set fieldItems = null %}

            {% if fields %}
                {% set fieldDefinitions = fields.getItemDefinitions() %}
                {% set fieldItems = fields.getItems() %}
            {% endif %}

            {% if fieldItems is iterable and fieldItems|length > 0 %}
                {% for fkey,fieldItem  in fieldItems %}
                    {% set fieldKeys = fieldDefinitions[fieldItem.getType()].getFieldDefinitions() %}
                    {% for fieldKey in fieldKeys %}
                        {% if fieldKey is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Localizedfields') %}
                            {% for language in validLanguages %}
                                {% for lfd in fieldKey.getFieldDefinitions() %}
                                    <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                                        <td>{{ lfd.getTitle()|trans([],'admin') }} ({{ language }})</td>
                                        <td>{{ fieldItem.fieldName }} - {{ lfd.getName() }}/{{ language }}</td>
                                        <td>{{ fieldItem.Localizedfields.getLocalizedValue(lfd.getName(), language) }}</td>
                                    </tr>
                                {% endfor %}
                            {% endfor %}
                        {% else %}
                            {% set value = fieldItem.get(fieldKey.getName()) %}
                            <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                                <td>{{ fieldItem.getType() ~ \" - \" ~ fieldKey.getTitle()|trans([],'admin') }}</td>
                                <td>{{ fieldKey.getName() }}</td>
                                <td>{{ fieldKey.getVersionPreview(value) | raw }}</td>
                            </tr>
                        {% endif %}
                    {% endfor %}
                {% endfor %}
            {% endif %}
        {% else %}
            <tr {% if loop.index is odd %}class=\"odd\"{% endif %}>
                <td>{{ definition.getTitle()|trans([],'admin') }}</td>
                <td>{{ definition.getName() }}</td>
                <td>{{ definition.getVersionPreview(object.getValueForFieldName(fieldName)) | raw }}</td>
            </tr>
        {% endif %}
    {% endfor %}
</table>
</body>
</html>
", "@PimcoreAdmin/admin/data_object/data_object/preview_version.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/data_object/data_object/preview_version.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 11, "if" => 34, "for" => 45);
        static $filters = array("escape" => 22, "date" => 22, "nl2br" => 38, "trans" => 50, "raw" => 54, "capitalize" => 67, "merge" => 114, "filter" => 117, "length" => 153);
        static $functions = array("pimcore_object_brick_definition_key" => 62, "pimcore_object_classificationstore_group" => 118, "pimcore_object_classificationstore_get_field_definition_from_json" => 123);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'for'],
                ['escape', 'date', 'nl2br', 'trans', 'raw', 'capitalize', 'merge', 'filter', 'length'],
                ['pimcore_object_brick_definition_key', 'pimcore_object_classificationstore_group', 'pimcore_object_classificationstore_get_field_definition_from_json']
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
