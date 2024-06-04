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

/* @PimcoreAdmin/searchadmin/search/quicksearch/object.html.twig */
class __TwigTemplate_ea7b8710493fc82034dcb8818b7bb2a6 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/searchadmin/search/quicksearch/object.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/searchadmin/search/quicksearch/object.html.twig"));

        // line 1
        $context["fields"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 1, $this->source); })()), "getClass", [], "method", false, false, true, 1), "getFieldDefinitions", [], "method", false, false, true, 1);
        // line 2
        $context["paddingTop"] = 20;
        // line 3
        echo "
";
        // line 4
        if ((array_key_exists("iconCls", $context) && ((isset($context["iconCls"]) || array_key_exists("iconCls", $context) ? $context["iconCls"] : (function () { throw new RuntimeError('Variable "iconCls" does not exist.', 4, $this->source); })()) != ""))) {
            // line 5
            echo "    <div class=\"small-icon ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["iconCls"]) || array_key_exists("iconCls", $context) ? $context["iconCls"] : (function () { throw new RuntimeError('Variable "iconCls" does not exist.', 5, $this->source); })()), 5, $this->source), "html", null, true);
            echo "\"></div>
    ";
            // line 6
            $context["paddingTop"] = ((isset($context["paddingTop"]) || array_key_exists("paddingTop", $context) ? $context["paddingTop"] : (function () { throw new RuntimeError('Variable "paddingTop" does not exist.', 6, $this->source); })()) + 50);
        }
        // line 8
        $this->loadTemplate("@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig", "@PimcoreAdmin/searchadmin/search/quicksearch/object.html.twig", 8)->display(twig_array_merge($context, ["element" => (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 8, $this->source); })()), "cls" => "no-opacity"]));
        // line 9
        echo "
<table class=\"data-table\" style=\"top: ";
        // line 10
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["paddingTop"]) || array_key_exists("paddingTop", $context) ? $context["paddingTop"] : (function () { throw new RuntimeError('Variable "paddingTop" does not exist.', 10, $this->source); })()), 10, $this->source), "html", null, true);
        echo "px\">
    ";
        // line 11
        $context["c"] = 0;
        // line 12
        echo "    ";
        $context["break"] = false;
        // line 13
        echo "    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_slice($this->env, (isset($context["fields"]) || array_key_exists("fields", $context) ? $context["fields"] : (function () { throw new RuntimeError('Variable "fields" does not exist.', 13, $this->source); })()), 0, 30));
        foreach ($context['_seq'] as $context["fieldName"] => $context["definition"]) {
            // line 14
            echo "        ";
            if ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Localizedfields")) {
                // line 15
                echo "            ";
                $context["language"] = twig_get_attribute($this->env, $this->source, (isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 15, $this->source); })()), 0, [], "array", false, false, true, 15);
                echo "  ";
                // line 16
                echo "            ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["definition"], "getFieldDefinitions", [], "method", false, false, true, 16));
                foreach ($context['_seq'] as $context["_key"] => $context["lfd"]) {
                    // line 17
                    echo "                ";
                    $context["trClass"] = ((((isset($context["c"]) || array_key_exists("c", $context) ? $context["c"] : (function () { throw new RuntimeError('Variable "c" does not exist.', 17, $this->source); })()) % 2)) ? ("odd") : (""));
                    // line 18
                    echo "                <tr class=\"";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["trClass"]) || array_key_exists("trClass", $context) ? $context["trClass"] : (function () { throw new RuntimeError('Variable "trClass" does not exist.', 18, $this->source); })()), 18, $this->source), "html", null, true);
                    echo "\">
                    <th>";
                    // line 19
                    echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 19)) ? ($this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 19), 19, $this->source), [], "admin")) : (twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 19))), "html", null, true);
                    echo " (";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["language"]) || array_key_exists("language", $context) ? $context["language"] : (function () { throw new RuntimeError('Variable "language" does not exist.', 19, $this->source); })()), 19, $this->source), "html", null, true);
                    echo ")</th>
                    <td>
                        <div class=\"limit-height\">
                            ";
                    // line 22
                    if (twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 22, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 22)) {
                        // line 23
                        echo "                                ";
                        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getVersionPreview", [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 23, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 23), "getLocalizedValue", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 23), (isset($context["language"]) || array_key_exists("language", $context) ? $context["language"] : (function () { throw new RuntimeError('Variable "language" does not exist.', 23, $this->source); })())], "method", false, false, true, 23)], "method", false, false, true, 23), 23, $this->source);
                        echo "
                            ";
                    }
                    // line 25
                    echo "                        </div>
                    </td>
                </tr>
                ";
                    // line 28
                    $context["c"] = ((isset($context["c"]) || array_key_exists("c", $context) ? $context["c"] : (function () { throw new RuntimeError('Variable "c" does not exist.', 28, $this->source); })()) + 1);
                    // line 29
                    echo "            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['lfd'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 30
                echo "        ";
            } elseif ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Classificationstore")) {
                // line 31
                echo "            ";
                $context["storedata"] = twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 31, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 31);
                // line 32
                echo "
            ";
                // line 33
                $context["activeGroups"] = [];
                // line 34
                echo "
            ";
                // line 35
                if ((isset($context["storedata"]) || array_key_exists("storedata", $context) ? $context["storedata"] : (function () { throw new RuntimeError('Variable "storedata" does not exist.', 35, $this->source); })())) {
                    // line 36
                    echo "                ";
                    $context["activeGroups"] = twig_get_attribute($this->env, $this->source, (isset($context["storedata"]) || array_key_exists("storedata", $context) ? $context["storedata"] : (function () { throw new RuntimeError('Variable "storedata" does not exist.', 36, $this->source); })()), "getActiveGroups", [], "method", false, false, true, 36);
                    // line 37
                    echo "            ";
                }
                // line 38
                echo "            ";
                if ( !twig_test_empty((isset($context["activeGroups"]) || array_key_exists("activeGroups", $context) ? $context["activeGroups"] : (function () { throw new RuntimeError('Variable "activeGroups" does not exist.', 38, $this->source); })()))) {
                    // line 39
                    echo "                ";
                    $context["languages"] = ["default"];
                    // line 40
                    echo "                ";
                    if (twig_get_attribute($this->env, $this->source, $context["definition"], "isLocalized", [], "method", false, false, true, 40)) {
                        // line 41
                        echo "                    ";
                        $context["languages"] = twig_array_merge($this->sandbox->ensureToStringAllowed((isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 41, $this->source); })()), 41, $this->source), $this->sandbox->ensureToStringAllowed((isset($context["languages"]) || array_key_exists("languages", $context) ? $context["languages"] : (function () { throw new RuntimeError('Variable "languages" does not exist.', 41, $this->source); })()), 41, $this->source));
                        // line 42
                        echo "                ";
                    }
                    // line 43
                    echo "                ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_array_filter($this->env, (isset($context["activeGroups"]) || array_key_exists("activeGroups", $context) ? $context["activeGroups"] : (function () { throw new RuntimeError('Variable "activeGroups" does not exist.', 43, $this->source); })()), function ($__enabled__, $__activeGroupId__) use ($context, $macros) { $context["enabled"] = $__enabled__; $context["activeGroupId"] = $__activeGroupId__; return  !twig_test_empty(twig_get_attribute($this->env, $this->source, (isset($context["activeGroups"]) || array_key_exists("activeGroups", $context) ? $context["activeGroups"] : (function () { throw new RuntimeError('Variable "activeGroups" does not exist.', 43, $this->source); })()), $context["activeGroupId"], [], "array", false, false, true, 43)); }));
                    foreach ($context['_seq'] as $context["activeGroupId"] => $context["enabled"]) {
                        // line 44
                        echo "                    ";
                        $context["groupDefinition"] = Pimcore\Model\DataObject\Classificationstore\GroupConfig::getById($this->sandbox->ensureToStringAllowed($context["activeGroupId"], 44, $this->source));
                        // line 45
                        echo "                    ";
                        if ( !twig_test_empty((isset($context["groupDefinition"]) || array_key_exists("groupDefinition", $context) ? $context["groupDefinition"] : (function () { throw new RuntimeError('Variable "groupDefinition" does not exist.', 45, $this->source); })()))) {
                            // line 46
                            echo "                        ";
                            $context["keyGroupRelations"] = twig_get_attribute($this->env, $this->source, (isset($context["groupDefinition"]) || array_key_exists("groupDefinition", $context) ? $context["groupDefinition"] : (function () { throw new RuntimeError('Variable "groupDefinition" does not exist.', 46, $this->source); })()), "getRelations", [], "method", false, false, true, 46);
                            // line 47
                            echo "
                        ";
                            // line 48
                            $context['_parent'] = $context;
                            $context['_seq'] = twig_ensure_traversable((isset($context["keyGroupRelations"]) || array_key_exists("keyGroupRelations", $context) ? $context["keyGroupRelations"] : (function () { throw new RuntimeError('Variable "keyGroupRelations" does not exist.', 48, $this->source); })()));
                            foreach ($context['_seq'] as $context["_key"] => $context["keyGroupRelation"]) {
                                // line 49
                                echo "                            ";
                                $context["keyDef"] = $this->extensions['Pimcore\Twig\Extension\PimcoreObjectExtension']->getFieldDefinitionFromJson($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getDefinition", [], "method", false, false, true, 49), 49, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getType", [], "method", false, false, true, 49), 49, $this->source));
                                // line 50
                                echo "
                            ";
                                // line 51
                                if ( !twig_test_empty((isset($context["keyDef"]) || array_key_exists("keyDef", $context) ? $context["keyDef"] : (function () { throw new RuntimeError('Variable "keyDef" does not exist.', 51, $this->source); })()))) {
                                    // line 52
                                    echo "                                ";
                                    $context['_parent'] = $context;
                                    $context['_seq'] = twig_ensure_traversable((isset($context["languages"]) || array_key_exists("languages", $context) ? $context["languages"] : (function () { throw new RuntimeError('Variable "languages" does not exist.', 52, $this->source); })()));
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
                                        // line 53
                                        echo "                                    ";
                                        $context["keyData"] = (((isset($context["storedata"]) || array_key_exists("storedata", $context) ? $context["storedata"] : (function () { throw new RuntimeError('Variable "storedata" does not exist.', 53, $this->source); })())) ? (twig_get_attribute($this->env, $this->source, (isset($context["storedata"]) || array_key_exists("storedata", $context) ? $context["storedata"] : (function () { throw new RuntimeError('Variable "storedata" does not exist.', 53, $this->source); })()), "getLocalizedKeyValue", [$context["activeGroupId"], twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getKeyId", [], "method", false, false, true, 53), $context["language"], true, true], "method", false, false, true, 53)) : (null));
                                        // line 54
                                        echo "
                                    ";
                                        // line 55
                                        $context["preview"] = twig_get_attribute($this->env, $this->source, (isset($context["keyDef"]) || array_key_exists("keyDef", $context) ? $context["keyDef"] : (function () { throw new RuntimeError('Variable "keyDef" does not exist.', 55, $this->source); })()), "getVersionPreview", [(isset($context["keyData"]) || array_key_exists("keyData", $context) ? $context["keyData"] : (function () { throw new RuntimeError('Variable "keyData" does not exist.', 55, $this->source); })())], "method", false, false, true, 55);
                                        // line 56
                                        echo "                                    <tr ";
                                        if ((twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 56) % 2 != 0)) {
                                            echo "class=\"odd\"";
                                        }
                                        echo ">
                                        <td>";
                                        // line 57
                                        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getTitle", [], "method", false, false, true, 57), 57, $this->source), [], "admin"), "html", null, true);
                                        echo "</td>
                                        <td>";
                                        // line 58
                                        echo twig_escape_filter($this->env, (($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["groupDefinition"]) || array_key_exists("groupDefinition", $context) ? $context["groupDefinition"] : (function () { throw new RuntimeError('Variable "groupDefinition" does not exist.', 58, $this->source); })()), "getName", [], "method", false, false, true, 58), 58, $this->source) . "-") . $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["keyGroupRelation"], "getName", [], "method", false, false, true, 58), 58, $this->source)), "html", null, true);
                                        echo " ";
                                        ((twig_get_attribute($this->env, $this->source, $context["definition"], "isLocalized", [], "method", false, false, true, 58)) ? (print (twig_escape_filter($this->env, ("/ " . $context["language"]), "html", null, true))) : (print ("")));
                                        echo "</td>
                                        ";
                                        // line 59
                                        if (( !array_key_exists("isImportPreview", $context) ||  !array_key_exists("isNew", $context))) {
                                            // line 60
                                            echo "                                            <td>";
                                            echo $this->sandbox->ensureToStringAllowed((isset($context["preview"]) || array_key_exists("preview", $context) ? $context["preview"] : (function () { throw new RuntimeError('Variable "preview" does not exist.', 60, $this->source); })()), 60, $this->source);
                                            echo "</td>
                                        ";
                                        }
                                        // line 62
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
                                    // line 64
                                    echo "                            ";
                                }
                                // line 65
                                echo "                        ";
                            }
                            $_parent = $context['_parent'];
                            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['keyGroupRelation'], $context['_parent'], $context['loop']);
                            $context = array_intersect_key($context, $_parent) + $_parent;
                            // line 66
                            echo "                    ";
                        }
                        // line 67
                        echo "                ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['activeGroupId'], $context['enabled'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 68
                    echo "            ";
                }
                // line 69
                echo "        ";
            } elseif ($this->env->getTest('instanceof')->getCallable()($context["definition"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Objectbricks")) {
                // line 70
                echo "            ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["definition"], "getAllowedTypes", [], "method", false, false, true, 70));
                foreach ($context['_seq'] as $context["_key"] => $context["asAllowedType"]) {
                    // line 71
                    echo "                ";
                    $context["collectionDef"] = Pimcore\Model\DataObject\Objectbrick\Definition::getByKey($this->sandbox->ensureToStringAllowed($context["asAllowedType"], 71, $this->source));
                    // line 72
                    echo "
                ";
                    // line 73
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["collectionDef"]) || array_key_exists("collectionDef", $context) ? $context["collectionDef"] : (function () { throw new RuntimeError('Variable "collectionDef" does not exist.', 73, $this->source); })()), "getFieldDefinitions", [], "method", false, false, true, 73));
                    foreach ($context['_seq'] as $context["_key"] => $context["lfd"]) {
                        // line 74
                        echo "                    ";
                        $context["value"] = null;
                        // line 75
                        echo "
                    ";
                        // line 76
                        $context["fieldGetter"] = ("get" . twig_capitalize_string_filter($this->env, $this->sandbox->ensureToStringAllowed($context["fieldName"], 76, $this->source)));
                        // line 77
                        echo "                    ";
                        $context["brick"] = twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 77, $this->source); })()), (isset($context["fieldGetter"]) || array_key_exists("fieldGetter", $context) ? $context["fieldGetter"] : (function () { throw new RuntimeError('Variable "fieldGetter" does not exist.', 77, $this->source); })()), [], "any", false, false, true, 77);
                        // line 78
                        echo "
                    ";
                        // line 79
                        if ( !twig_test_empty((isset($context["brick"]) || array_key_exists("brick", $context) ? $context["brick"] : (function () { throw new RuntimeError('Variable "brick" does not exist.', 79, $this->source); })()))) {
                            // line 80
                            echo "                        ";
                            $context["allowedMethod"] = ("get" . $this->sandbox->ensureToStringAllowed($context["asAllowedType"], 80, $this->source));
                            // line 81
                            echo "                        ";
                            $context["brickValue"] = twig_get_attribute($this->env, $this->source, (isset($context["brick"]) || array_key_exists("brick", $context) ? $context["brick"] : (function () { throw new RuntimeError('Variable "brick" does not exist.', 81, $this->source); })()), (isset($context["allowedMethod"]) || array_key_exists("allowedMethod", $context) ? $context["allowedMethod"] : (function () { throw new RuntimeError('Variable "allowedMethod" does not exist.', 81, $this->source); })()), [], "any", false, false, true, 81);
                            // line 82
                            echo "
                        ";
                            // line 83
                            if ($this->env->getTest('instanceof')->getCallable()($context["lfd"], "\\Pimcore\\Model\\DataObject\\ClassDefinition\\Data\\Localizedfields")) {
                                // line 84
                                echo "                            ";
                                $context["language"] = twig_get_attribute($this->env, $this->source, (isset($context["validLanguages"]) || array_key_exists("validLanguages", $context) ? $context["validLanguages"] : (function () { throw new RuntimeError('Variable "validLanguages" does not exist.', 84, $this->source); })()), 0, [], "array", false, false, true, 84);
                                echo " ";
                                // line 85
                                echo "                            ";
                                $context['_parent'] = $context;
                                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["lfd"], "getFieldDefinitions", [], "method", false, false, true, 85));
                                foreach ($context['_seq'] as $context["_key"] => $context["localizedFieldDefinition"]) {
                                    // line 86
                                    echo "                                ";
                                    $context["trClass"] = ((((isset($context["c"]) || array_key_exists("c", $context) ? $context["c"] : (function () { throw new RuntimeError('Variable "c" does not exist.', 86, $this->source); })()) % 2)) ? ("odd") : (""));
                                    // line 87
                                    echo "                                <tr class=\"";
                                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["trClass"]) || array_key_exists("trClass", $context) ? $context["trClass"] : (function () { throw new RuntimeError('Variable "trClass" does not exist.', 87, $this->source); })()), 87, $this->source), "html", null, true);
                                    echo "\">
                                    <th>";
                                    // line 88
                                    echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getTitle", [], "method", false, false, true, 88)) ? ($this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getTitle", [], "method", false, false, true, 88), 88, $this->source), [], "admin")) : (twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getName", [], "method", false, false, true, 88))), "html", null, true);
                                    echo "</th>
                                    <td>
                                        <div class=\"limit-height\">
                                            ";
                                    // line 91
                                    if ((isset($context["brickValue"]) || array_key_exists("brickValue", $context) ? $context["brickValue"] : (function () { throw new RuntimeError('Variable "brickValue" does not exist.', 91, $this->source); })())) {
                                        // line 92
                                        echo "                                                ";
                                        $context["localizedBrickValues"] = twig_get_attribute($this->env, $this->source, (isset($context["brickValue"]) || array_key_exists("brickValue", $context) ? $context["brickValue"] : (function () { throw new RuntimeError('Variable "brickValue" does not exist.', 92, $this->source); })()), "getLocalizedFields", [], "method", false, false, true, 92);
                                        // line 93
                                        echo "                                                ";
                                        $context["localizedBrickValue"] = twig_get_attribute($this->env, $this->source, (isset($context["localizedBrickValues"]) || array_key_exists("localizedBrickValues", $context) ? $context["localizedBrickValues"] : (function () { throw new RuntimeError('Variable "localizedBrickValues" does not exist.', 93, $this->source); })()), "getLocalizedValue", [twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getName", [], "method", false, false, true, 93), (isset($context["language"]) || array_key_exists("language", $context) ? $context["language"] : (function () { throw new RuntimeError('Variable "language" does not exist.', 93, $this->source); })())], "method", false, false, true, 93);
                                        // line 94
                                        echo "                                                ";
                                        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["localizedFieldDefinition"], "getVersionPreview", [(isset($context["localizedBrickValue"]) || array_key_exists("localizedBrickValue", $context) ? $context["localizedBrickValue"] : (function () { throw new RuntimeError('Variable "localizedBrickValue" does not exist.', 94, $this->source); })())], "method", false, false, true, 94), 94, $this->source);
                                        echo "
                                            ";
                                    }
                                    // line 96
                                    echo "                                        </div>
                                    </td>
                                </tr>
                                ";
                                    // line 99
                                    $context["c"] = ((isset($context["c"]) || array_key_exists("c", $context) ? $context["c"] : (function () { throw new RuntimeError('Variable "c" does not exist.', 99, $this->source); })()) + 1);
                                    // line 100
                                    echo "                            ";
                                }
                                $_parent = $context['_parent'];
                                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['localizedFieldDefinition'], $context['_parent'], $context['loop']);
                                $context = array_intersect_key($context, $_parent) + $_parent;
                                // line 101
                                echo "                        ";
                            } else {
                                // line 102
                                echo "                            ";
                                if ((isset($context["brickValue"]) || array_key_exists("brickValue", $context) ? $context["brickValue"] : (function () { throw new RuntimeError('Variable "brickValue" does not exist.', 102, $this->source); })())) {
                                    // line 103
                                    echo "                                ";
                                    $context["value"] = twig_get_attribute($this->env, $this->source, $context["lfd"], "getVersionPreview", [twig_get_attribute($this->env, $this->source, (isset($context["brickValue"]) || array_key_exists("brickValue", $context) ? $context["brickValue"] : (function () { throw new RuntimeError('Variable "brickValue" does not exist.', 103, $this->source); })()), "getValueForFieldName", [twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 103)], "method", false, false, true, 103)], "method", false, false, true, 103);
                                    // line 104
                                    echo "                            ";
                                }
                                // line 105
                                echo "                            ";
                                $context["trClass"] = ((((isset($context["c"]) || array_key_exists("c", $context) ? $context["c"] : (function () { throw new RuntimeError('Variable "c" does not exist.', 105, $this->source); })()) % 2)) ? ("odd") : (""));
                                // line 106
                                echo "                            <tr class=\"";
                                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["trClass"]) || array_key_exists("trClass", $context) ? $context["trClass"] : (function () { throw new RuntimeError('Variable "trClass" does not exist.', 106, $this->source); })()), 106, $this->source), "html", null, true);
                                echo "\">
                                <th>";
                                // line 107
                                echo twig_escape_filter($this->env, ((twig_capitalize_string_filter($this->env, $this->sandbox->ensureToStringAllowed($context["asAllowedType"], 107, $this->source)) . " - ") . ((twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 107)) ? ($this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["lfd"], "getTitle", [], "method", false, false, true, 107), 107, $this->source), [], "admin")) : (twig_get_attribute($this->env, $this->source, $context["lfd"], "getName", [], "method", false, false, true, 107)))), "html", null, true);
                                echo "</th>
                                <td>
                                    <div class=\"limit-height\">
                                        ";
                                // line 110
                                echo $this->sandbox->ensureToStringAllowed((isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 110, $this->source); })()), 110, $this->source);
                                echo "
                                    </div>
                                </td>
                            </tr>
                        ";
                            }
                            // line 115
                            echo "                    ";
                        }
                        // line 116
                        echo "
                ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['lfd'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 118
                    echo "            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['asAllowedType'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 119
                echo "        ";
            } else {
                // line 120
                echo "            ";
                $context["trClass"] = ((((isset($context["c"]) || array_key_exists("c", $context) ? $context["c"] : (function () { throw new RuntimeError('Variable "c" does not exist.', 120, $this->source); })()) % 2)) ? ("odd") : (""));
                // line 121
                echo "            <tr class=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["trClass"]) || array_key_exists("trClass", $context) ? $context["trClass"] : (function () { throw new RuntimeError('Variable "trClass" does not exist.', 121, $this->source); })()), 121, $this->source), "html", null, true);
                echo "\">
            <th>";
                // line 122
                echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, $context["definition"], "getTitle", [], "method", false, false, true, 122)) ? ($this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getTitle", [], "method", false, false, true, 122), 122, $this->source), [], "admin")) : (twig_get_attribute($this->env, $this->source, $context["definition"], "getName", [], "method", false, false, true, 122))), "html", null, true);
                echo "</th>
            <td>
                <div class=\"limit-height\">
                    ";
                // line 125
                echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["definition"], "getVersionPreview", [twig_get_attribute($this->env, $this->source, (isset($context["element"]) || array_key_exists("element", $context) ? $context["element"] : (function () { throw new RuntimeError('Variable "element" does not exist.', 125, $this->source); })()), "getValueForFieldName", [$context["fieldName"]], "method", false, false, true, 125)], "method", false, false, true, 125), 125, $this->source);
                echo "
                </div>
            </td>
            </tr>
        ";
            }
            // line 130
            echo "        ";
            $context["c"] = ((isset($context["c"]) || array_key_exists("c", $context) ? $context["c"] : (function () { throw new RuntimeError('Variable "c" does not exist.', 130, $this->source); })()) + 1);
            // line 131
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['fieldName'], $context['definition'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 132
        echo "</table>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreAdmin/searchadmin/search/quicksearch/object.html.twig";
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
        return array (  462 => 132,  456 => 131,  453 => 130,  445 => 125,  439 => 122,  434 => 121,  431 => 120,  428 => 119,  422 => 118,  415 => 116,  412 => 115,  404 => 110,  398 => 107,  393 => 106,  390 => 105,  387 => 104,  384 => 103,  381 => 102,  378 => 101,  372 => 100,  370 => 99,  365 => 96,  359 => 94,  356 => 93,  353 => 92,  351 => 91,  345 => 88,  340 => 87,  337 => 86,  332 => 85,  328 => 84,  326 => 83,  323 => 82,  320 => 81,  317 => 80,  315 => 79,  312 => 78,  309 => 77,  307 => 76,  304 => 75,  301 => 74,  297 => 73,  294 => 72,  291 => 71,  286 => 70,  283 => 69,  280 => 68,  274 => 67,  271 => 66,  265 => 65,  262 => 64,  247 => 62,  241 => 60,  239 => 59,  233 => 58,  229 => 57,  222 => 56,  220 => 55,  217 => 54,  214 => 53,  196 => 52,  194 => 51,  191 => 50,  188 => 49,  184 => 48,  181 => 47,  178 => 46,  175 => 45,  172 => 44,  167 => 43,  164 => 42,  161 => 41,  158 => 40,  155 => 39,  152 => 38,  149 => 37,  146 => 36,  144 => 35,  141 => 34,  139 => 33,  136 => 32,  133 => 31,  130 => 30,  124 => 29,  122 => 28,  117 => 25,  111 => 23,  109 => 22,  101 => 19,  96 => 18,  93 => 17,  88 => 16,  84 => 15,  81 => 14,  76 => 13,  73 => 12,  71 => 11,  67 => 10,  64 => 9,  62 => 8,  59 => 6,  54 => 5,  52 => 4,  49 => 3,  47 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% set fields = element.getClass().getFieldDefinitions() %}
{% set paddingTop = 20 %}

{% if iconCls is defined and iconCls != '' %}
    <div class=\"small-icon {{ iconCls }}\"></div>
    {% set paddingTop = paddingTop + 50 %}
{% endif %}
{% include '@PimcoreAdmin/searchadmin/search/quicksearch/info_table.html.twig' with {'element': element, 'cls': 'no-opacity'} %}

<table class=\"data-table\" style=\"top: {{ paddingTop }}px\">
    {% set c = 0 %}
    {% set break = false %}
    {% for fieldName, definition in fields|slice(0,30) %}
        {% if definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Localizedfields') %}
            {% set language = validLanguages[0] %}  {# show only one language data #}
            {% for lfd in definition.getFieldDefinitions() %}
                {% set trClass = (c % 2) ? 'odd' : '' %}
                <tr class=\"{{ trClass }}\">
                    <th>{{ lfd.getTitle() ? lfd.getTitle()|trans([],'admin') : lfd.getName() }} ({{ language }})</th>
                    <td>
                        <div class=\"limit-height\">
                            {% if element.getValueForFieldName(fieldName) %}
                                {{ lfd.getVersionPreview(element.getValueForFieldName(fieldName).getLocalizedValue(lfd.getName(), language)) | raw }}
                            {% endif %}
                        </div>
                    </td>
                </tr>
                {% set c = c + 1 %}
            {% endfor %}
        {% elseif definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Classificationstore') %}
            {% set storedata = element.getValueForFieldName(fieldName) %}

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
        {% elseif definition is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Objectbricks') %}
            {% for asAllowedType in definition.getAllowedTypes() %}
                {% set collectionDef = pimcore_object_brick_definition_key(asAllowedType) %}

                {% for lfd in collectionDef.getFieldDefinitions() %}
                    {% set value = null %}

                    {% set fieldGetter = \"get\" ~ (fieldName|capitalize) %}
                    {% set brick = attribute(element, fieldGetter) %}

                    {% if brick is not empty %}
                        {% set allowedMethod = \"get\" ~ asAllowedType %}
                        {% set brickValue = attribute(brick,allowedMethod) %}

                        {% if lfd is instanceof('\\\\Pimcore\\\\Model\\\\DataObject\\\\ClassDefinition\\\\Data\\\\Localizedfields') %}
                            {% set language = validLanguages[0] %} {# show only one language data #}
                            {% for localizedFieldDefinition in lfd.getFieldDefinitions() %}
                                {% set trClass = (c % 2) ? 'odd' : '' %}
                                <tr class=\"{{ trClass }}\">
                                    <th>{{ localizedFieldDefinition.getTitle() ? localizedFieldDefinition.getTitle()|trans([],'admin') : localizedFieldDefinition.getName() }}</th>
                                    <td>
                                        <div class=\"limit-height\">
                                            {% if brickValue %}
                                                {% set localizedBrickValues = brickValue.getLocalizedFields() %}
                                                {% set localizedBrickValue = localizedBrickValues.getLocalizedValue(localizedFieldDefinition.getName(), language) %}
                                                {{ localizedFieldDefinition.getVersionPreview(localizedBrickValue) | raw }}
                                            {% endif %}
                                        </div>
                                    </td>
                                </tr>
                                {% set c = c + 1 %}
                            {% endfor %}
                        {% else %}
                            {% if brickValue %}
                                {% set value = lfd.getVersionPreview(brickValue.getValueForFieldName(lfd.getName())) %}
                            {% endif %}
                            {% set trClass = (c % 2) ? 'odd' : '' %}
                            <tr class=\"{{ trClass }}\">
                                <th>{{ asAllowedType|capitalize ~ \" - \" ~ (lfd.getTitle() ? lfd.getTitle()|trans([],'admin') : lfd.getName() ) }}</th>
                                <td>
                                    <div class=\"limit-height\">
                                        {{ value | raw }}
                                    </div>
                                </td>
                            </tr>
                        {% endif %}
                    {% endif %}

                {% endfor %}
            {% endfor %}
        {% else %}
            {% set trClass = (c % 2) ? 'odd' : '' %}
            <tr class=\"{{ trClass }}\">
            <th>{{ definition.getTitle() ? definition.getTitle()|trans([],'admin') : definition.getName() }}</th>
            <td>
                <div class=\"limit-height\">
                    {{ definition.getVersionPreview(element.getValueForFieldName(fieldName)) | raw }}
                </div>
            </td>
            </tr>
        {% endif %}
        {% set c = c + 1 %}
    {% endfor %}
</table>
", "@PimcoreAdmin/searchadmin/search/quicksearch/object.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/searchadmin/search/quicksearch/object.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 1, "if" => 4, "include" => 8, "for" => 13);
        static $filters = array("escape" => 5, "slice" => 13, "trans" => 19, "raw" => 23, "merge" => 41, "filter" => 43, "capitalize" => 76);
        static $functions = array("pimcore_object_classificationstore_group" => 44, "pimcore_object_classificationstore_get_field_definition_from_json" => 49, "pimcore_object_brick_definition_key" => 71);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'include', 'for'],
                ['escape', 'slice', 'trans', 'raw', 'merge', 'filter', 'capitalize'],
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
