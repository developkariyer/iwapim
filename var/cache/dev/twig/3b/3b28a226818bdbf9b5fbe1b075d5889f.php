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

/* @PimcoreAdmin/admin/index/index.html.twig */
class __TwigTemplate_b32f5921f856f14aef53d666ef1bd177 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'notification' => [$this, 'block_notification'],
            'avatar' => [$this, 'block_avatar'],
            'logout' => [$this, 'block_logout'],
            'stylesheets' => [$this, 'block_stylesheets'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/index/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/index/index.html.twig"));

        // line 1
        $context["language"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 1, $this->source); })()), "request", [], "any", false, false, true, 1), "locale", [], "any", false, false, true, 1);
        // line 3
        $context["userProxy"] = twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 3, $this->source); })()), "user", [], "any", false, false, true, 3);
        // line 4
        $context["user"] = twig_get_attribute($this->env, $this->source, (isset($context["userProxy"]) || array_key_exists("userProxy", $context) ? $context["userProxy"] : (function () { throw new RuntimeError('Variable "userProxy" does not exist.', 4, $this->source); })()), "user", [], "any", false, false, true, 4);
        // line 5
        echo "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <meta name=\"robots\" content=\"noindex, nofollow\"/>
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\"/>
    <meta name=\"apple-mobile-web-app-capable\" content=\"yes\"/>

    <link rel=\"icon\" type=\"image/png\" href=\"/bundles/pimcoreadmin/img/favicon/favicon-32x32.png\"/>
    <meta name=\"google\" value=\"notranslate\">

    <style type=\"text/css\">
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }

        #pimcore_loading {
            margin: 0 auto;
            width: 300px;
            padding: 300px 0 0 0;
            text-align: center;
        }

        .spinner {
            margin: 100px auto 0;
            width: 70px;
            text-align: center;
        }

        .spinner > div {
            width: 18px;
            height: 18px;
            background-color: #3d3d3d;

            border-radius: 100%;
            display: inline-block;
            -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
            animation: sk-bouncedelay 1.4s infinite ease-in-out both;
        }

        .spinner .bounce1 {
            -webkit-animation-delay: -0.32s;
            animation-delay: -0.32s;
        }

        .spinner .bounce2 {
            -webkit-animation-delay: -0.16s;
            animation-delay: -0.16s;
        }

        @-webkit-keyframes sk-bouncedelay {
            0%, 80%, 100% {
                -webkit-transform: scale(0)
            }
            40% {
                -webkit-transform: scale(1.0)
            }
        }

        @keyframes sk-bouncedelay {
            0%, 80%, 100% {
                -webkit-transform: scale(0);
                transform: scale(0);
            }
            40% {
                -webkit-transform: scale(1.0);
                transform: scale(1.0);
            }
        }

        #pimcore_panel_tabs-body {
            background-image: url(";
        // line 79
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_settings_display_custom_logo");
        echo ");
            ";
        // line 80
        if ( !(null === ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["adminSettings"] ?? null), "branding", [], "array", false, true, true, 80), "color_admin_interface_background", [], "array", true, true, true, 80)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["adminSettings"] ?? null), "branding", [], "array", false, true, true, 80), "color_admin_interface_background", [], "array", false, false, true, 80), null)) : (null)))) {
            // line 81
            echo "                background-color: ";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["adminSettings"]) || array_key_exists("adminSettings", $context) ? $context["adminSettings"] : (function () { throw new RuntimeError('Variable "adminSettings" does not exist.', 81, $this->source); })()), "branding", [], "array", false, false, true, 81), "color_admin_interface_background", [], "array", false, false, true, 81), 81, $this->source), "html", null, true);
            echo ";
            ";
        }
        // line 83
        echo "            background-repeat: no-repeat;
            background-position: center center;
            background-size: 500px auto;
        }
    </style>

    <title>";
        // line 89
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 89, $this->source); })()), "hostname", [], "any", false, false, true, 89), 89, $this->source), "html", null, true);
        echo " :: Pimcore</title>

    <script ";
        // line 91
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 91, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 91), 91, $this->source);
        echo ">
        var pimcore = {}; // namespace

        // hide symfony toolbar by default
        var symfonyToolbarKey = 'symfony/profiler/toolbar/displayState';
        if(!window.localStorage.getItem(symfonyToolbarKey)) {
            window.localStorage.setItem(symfonyToolbarKey, 'none');
        }
    </script>

    <script src=\"";
        // line 101
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/fosjsrouting/js/router.js"), "html", null, true);
        echo "\" ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 101, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 101), 101, $this->source);
        echo "></script>
    <script src=\"";
        // line 102
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("fos_js_routing_js", ["callback" => "fos.Router.setData"]);
        echo "\" ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 102, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 102), 102, $this->source);
        echo "></script>

    ";
        // line 104
        echo $this->extensions['Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension']->renderWebpackScriptTags("admin", null, "pimcoreAdmin");
        echo "
    ";
        // line 105
        echo $this->extensions['Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension']->renderWebpackLinkTags("admin", null, "pimcoreAdmin");
        echo "
</head>

<body class=\"pimcore_version_11\" data-app-env=\"";
        // line 108
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 108, $this->source); })()), "environment", [], "any", false, false, true, 108), 108, $this->source), "html", null, true);
        echo "\">

<div id=\"pimcore_loading\">
    <div class=\"spinner\">
        <div class=\"bounce1\"></div>
        <div class=\"bounce2\"></div>
        <div class=\"bounce3\"></div>
    </div>
</div>
";
        // line 117
        $context["runtimePerspective"] = twig_get_attribute($this->env, $this->source, (isset($context["perspectiveConfig"]) || array_key_exists("perspectiveConfig", $context) ? $context["perspectiveConfig"] : (function () { throw new RuntimeError('Variable "perspectiveConfig" does not exist.', 117, $this->source); })()), "getRuntimePerspective", [(isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 117, $this->source); })())], "method", false, false, true, 117);
        // line 118
        echo "
<div id=\"pimcore_sidebar\">
    <div id=\"pimcore_navigation\" style=\"display:none;\">
        <ul id=\"pimcore_navigation_ul\"></ul>
    </div>

    <div id=\"pimcore_status\"></div>

    ";
        // line 126
        $this->displayBlock('notification', $context, $blocks);
        // line 132
        echo "

    ";
        // line 134
        $this->displayBlock('avatar', $context, $blocks);
        // line 139
        echo "
    ";
        // line 140
        $this->displayBlock('logout', $context, $blocks);
        // line 149
        echo "
    <div id=\"pimcore_signet\" data-menu-tooltip=\"Pimcore Platform (";
        // line 150
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 150, $this->source); })()), "version", [], "any", false, false, true, 150), 150, $this->source), "html", null, true);
        echo "|";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 150, $this->source); })()), "build", [], "any", false, false, true, 150), 150, $this->source), "html", null, true);
        echo ")\" style=\"text-indent: -10000px\">
        BE RESPECTFUL AND HONOR OUR WORK FOR FREE & OPEN SOURCE SOFTWARE BY NOT REMOVING OUR LOGO.
        WE OFFER YOU THE POSSIBILITY TO ADDITIONALLY ADD YOUR OWN LOGO IN PIMCORE'S SYSTEM SETTINGS. THANK YOU!
    </div>
</div>

<div id=\"pimcore_tooltip\" style=\"display: none;\"></div>
<div id=\"pimcore_quicksearch\"></div>

";
        // line 160
        echo "
";
        // line 161
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 184
        echo "
";
        // line 186
        echo "
";
        // line 187
        $context["debugSuffix"] = "";
        // line 188
        if (twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 188, $this->source); })()), "disableMinifyJs", [], "any", false, false, true, 188)) {
            // line 189
            echo "    ";
            $context["debugSuffix"] = "-debug";
        }
        // line 191
        echo "
";
        // line 193
        echo "
";
        // line 194
        $context["scriptLibs"] = ["lib/class.js", (("../extjs/js/ext-all" . $this->sandbox->ensureToStringAllowed(        // line 196
(isset($context["debugSuffix"]) || array_key_exists("debugSuffix", $context) ? $context["debugSuffix"] : (function () { throw new RuntimeError('Variable "debugSuffix" does not exist.', 196, $this->source); })()), 196, $this->source)) . ".js"), "lib/ext-plugins/portlet/PortalDropZone.js", "lib/ext-plugins/portlet/Portlet.js", "lib/ext-plugins/portlet/PortalColumn.js", "lib/ext-plugins/portlet/PortalPanel.js", "../build/admin/ace-builds/src-min-noconflict/ace.js", "../build/admin/ace-builds/src-min-noconflict/ext-modelist.js"];
        // line 204
        if ($this->env->getFunction('pimcore_file_exists')->getCallable()((((twig_constant("PIMCORE_WEB_ROOT") . "/bundles/pimcoreadmin/js/lib/ext-locale/locale-") . (isset($context["language"]) || array_key_exists("language", $context) ? $context["language"] : (function () { throw new RuntimeError('Variable "language" does not exist.', 204, $this->source); })())) . ".js"))) {
            // line 205
            echo "    ";
            $context["scriptLibs"] = twig_array_merge($this->sandbox->ensureToStringAllowed((isset($context["scriptLibs"]) || array_key_exists("scriptLibs", $context) ? $context["scriptLibs"] : (function () { throw new RuntimeError('Variable "scriptLibs" does not exist.', 205, $this->source); })()), 205, $this->source), [(("lib/ext-locale/locale-" . $this->sandbox->ensureToStringAllowed((isset($context["language"]) || array_key_exists("language", $context) ? $context["language"] : (function () { throw new RuntimeError('Variable "language" does not exist.', 205, $this->source); })()), 205, $this->source)) . ".js")]);
        }
        // line 207
        echo "
";
        // line 209
        echo "
";
        // line 210
        $context["scripts"] = ["pimcore/functions.js", "pimcore/common.js", "pimcore/elementservice.js", "pimcore/helpers.js", "pimcore/error.js", "pimcore/events.js", "pimcore/treenodelocator.js", "pimcore/helpers/generic-grid.js", "pimcore/helpers/quantityValue.js", "pimcore/overrides.js", "pimcore/perspective.js", "pimcore/user.js", "pimcore/tool/paralleljobs.js", "pimcore/tool/genericiframewindow.js", "pimcore/settings/user/panels/abstract.js", "pimcore/settings/user/panel.js", "pimcore/settings/user/usertab.js", "pimcore/settings/user/editorSettings.js", "pimcore/settings/user/websiteTranslationSettings.js", "pimcore/settings/user/role/panel.js", "pimcore/settings/user/role/tab.js", "pimcore/settings/user/user/objectrelations.js", "pimcore/settings/user/user/settings.js", "pimcore/settings/user/user/keyBindings.js", "pimcore/settings/user/workspaces.js", "pimcore/settings/user/workspace/asset.js", "pimcore/settings/user/workspace/document.js", "pimcore/settings/user/workspace/object.js", "pimcore/settings/user/workspace/customlayouts.js", "pimcore/settings/user/workspace/language.js", "pimcore/settings/user/workspace/special.js", "pimcore/settings/user/role/settings.js", "pimcore/settings/profile/panel.js", "pimcore/settings/profile/twoFactorSettings.js", "pimcore/settings/thumbnail/item.js", "pimcore/settings/thumbnail/panel.js", "pimcore/settings/videothumbnail/item.js", "pimcore/settings/videothumbnail/panel.js", "pimcore/settings/translation.js", "pimcore/settings/translationEditor.js", "pimcore/settings/translation/translationmerger.js", "pimcore/settings/translation/translationSettingsTab.js", "pimcore/settings/metadata/predefined.js", "pimcore/settings/properties/predefined.js", "pimcore/settings/docTypes.js", "pimcore/settings/system.js", "pimcore/settings/systemAppearance.js", "pimcore/settings/website.js", "pimcore/settings/recyclebin.js", "pimcore/settings/maintenance.js", "pimcore/settings/email/log.js", "pimcore/settings/email/blocklist.js", "pimcore/settings/gdpr/gdprPanel.js", "pimcore/settings/gdpr/dataproviders/assets.js", "pimcore/settings/gdpr/dataproviders/dataObjects.js", "pimcore/settings/gdpr/dataproviders/sentMail.js", "pimcore/settings/gdpr/dataproviders/pimcoreUsers.js", "pimcore/element/applicationLoggerPanelFacade.js", "pimcore/element/customReportsPanelFacade.js", "pimcore/element/selector/searchFacade.js", "pimcore/element/abstract.js", "pimcore/element/abstractPreview.js", "pimcore/element/properties.js", "pimcore/element/scheduler.js", "pimcore/element/dependencies.js", "pimcore/element/metainfo.js", "pimcore/element/history.js", "pimcore/element/notes.js", "pimcore/element/note_details.js", "pimcore/element/workflows.js", "pimcore/element/tag/imagecropper.js", "pimcore/element/tag/imagehotspotmarkereditor.js", "pimcore/element/replace_assignments.js", "pimcore/element/permissionchecker.js", "pimcore/element/gridexport/abstract.js", "pimcore/element/helpers/gridColumnConfig.js", "pimcore/element/helpers/gridConfigDialog.js", "pimcore/element/helpers/gridCellEditor.js", "pimcore/element/helpers/gridTabAbstract.js", "pimcore/object/helpers/grid.js", "pimcore/object/helpers/gridConfigDialog.js", "pimcore/object/helpers/classTree.js", "pimcore/object/helpers/gridTabAbstract.js", "pimcore/object/helpers/metadataMultiselectEditor.js", "pimcore/object/helpers/customLayoutEditor.js", "pimcore/object/helpers/optionEditor.js", "pimcore/object/helpers/imageGalleryDropZone.js", "pimcore/object/helpers/imageGalleryPanel.js", "pimcore/object/helpers/selectField.js", "pimcore/object/helpers/reservedWords.js", "pimcore/element/tag/configuration.js", "pimcore/element/tag/assignment.js", "pimcore/element/tag/tree.js", "pimcore/asset/helpers/metadataTree.js", "pimcore/asset/helpers/gridConfigDialog.js", "pimcore/asset/helpers/gridTabAbstract.js", "pimcore/asset/helpers/grid.js", "pimcore/document/properties.js", "pimcore/document/document.js", "pimcore/document/page_snippet.js", "pimcore/document/edit.js", "pimcore/document/versions.js", "pimcore/document/settings_abstract.js", "pimcore/document/pages/settings.js", "pimcore/document/pages/preview.js", "pimcore/document/snippets/settings.js", "pimcore/document/emails/settings.js", "pimcore/document/link.js", "pimcore/document/hardlink.js", "pimcore/document/folder.js", "pimcore/document/tree.js", "pimcore/document/snippet.js", "pimcore/document/email.js", "pimcore/document/page.js", "pimcore/document/document_language_overview.js", "pimcore/document/customviews/tree.js", "pimcore/asset/metadata/data/data.js", "pimcore/asset/metadata/data/input.js", "pimcore/asset/metadata/data/textarea.js", "pimcore/asset/metadata/data/asset.js", "pimcore/asset/metadata/data/document.js", "pimcore/asset/metadata/data/object.js", "pimcore/asset/metadata/data/date.js", "pimcore/asset/metadata/data/checkbox.js", "pimcore/asset/metadata/data/select.js", "pimcore/asset/metadata/tags/abstract.js", "pimcore/asset/metadata/tags/checkbox.js", "pimcore/asset/metadata/tags/date.js", "pimcore/asset/metadata/tags/input.js", "pimcore/asset/metadata/tags/manyToOneRelation.js", "pimcore/asset/metadata/tags/asset.js", "pimcore/asset/metadata/tags/document.js", "pimcore/asset/metadata/tags/object.js", "pimcore/asset/metadata/tags/select.js", "pimcore/asset/metadata/tags/textarea.js", "pimcore/asset/asset.js", "pimcore/asset/unknown.js", "pimcore/asset/embedded_meta_data.js", "pimcore/asset/image.js", "pimcore/asset/document.js", "pimcore/asset/archive.js", "pimcore/asset/video.js", "pimcore/asset/audio.js", "pimcore/asset/text.js", "pimcore/asset/folder.js", "pimcore/asset/listfolder.js", "pimcore/asset/versions.js", "pimcore/asset/metadata/dataProvider.js", "pimcore/asset/metadata/grid.js", "pimcore/asset/metadata/editor.js", "pimcore/asset/tree.js", "pimcore/asset/customviews/tree.js", "pimcore/asset/gridexport/csv.js", "pimcore/asset/gridexport/xlsx.js", "pimcore/object/helpers/edit.js", "pimcore/object/helpers/layout.js", "pimcore/object/classes/class.js", "pimcore/object/class.js", "pimcore/object/bulk-base.js", "pimcore/object/bulk-export.js", "pimcore/object/bulk-import.js", "pimcore/object/classes/data/data.js", "pimcore/object/classes/data/block.js", "pimcore/object/classes/data/classificationstore.js", "pimcore/object/classes/data/rgbaColor.js", "pimcore/object/classes/data/date.js", "pimcore/object/classes/data/datetime.js", "pimcore/object/classes/data/dateRange.js", "pimcore/object/classes/data/encryptedField.js", "pimcore/object/classes/data/time.js", "pimcore/object/classes/data/manyToOneRelation.js", "pimcore/object/classes/data/image.js", "pimcore/object/classes/data/externalImage.js", "pimcore/object/classes/data/hotspotimage.js", "pimcore/object/classes/data/imagegallery.js", "pimcore/object/classes/data/video.js", "pimcore/object/classes/data/input.js", "pimcore/object/classes/data/numeric.js", "pimcore/object/classes/data/numericRange.js", "pimcore/object/classes/data/manyToManyObjectRelation.js", "pimcore/object/classes/data/advancedManyToManyRelation.js", "pimcore/object/classes/data/advancedManyToManyObjectRelation.js", "pimcore/object/classes/data/reverseObjectRelation.js", "pimcore/object/classes/data/booleanSelect.js", "pimcore/object/classes/data/select.js", "pimcore/object/classes/data/urlSlug.js", "pimcore/object/classes/data/user.js", "pimcore/object/classes/data/textarea.js", "pimcore/object/classes/data/wysiwyg.js", "pimcore/object/classes/data/checkbox.js", "pimcore/object/classes/data/consent.js", "pimcore/object/classes/data/slider.js", "pimcore/object/classes/data/manyToManyRelation.js", "pimcore/object/classes/data/table.js", "pimcore/object/classes/data/structuredTable.js", "pimcore/object/classes/data/country.js", "pimcore/object/classes/data/geo/abstract.js", "pimcore/object/classes/data/geopoint.js", "pimcore/object/classes/data/geobounds.js", "pimcore/object/classes/data/geopolygon.js", "pimcore/object/classes/data/geopolyline.js", "pimcore/object/classes/data/language.js", "pimcore/object/classes/data/password.js", "pimcore/object/classes/data/multiselect.js", "pimcore/object/classes/data/link.js", "pimcore/object/classes/data/fieldcollections.js", "pimcore/object/classes/data/objectbricks.js", "pimcore/object/classes/data/localizedfields.js", "pimcore/object/classes/data/countrymultiselect.js", "pimcore/object/classes/data/languagemultiselect.js", "pimcore/object/classes/data/firstname.js", "pimcore/object/classes/data/lastname.js", "pimcore/object/classes/data/email.js", "pimcore/object/classes/data/gender.js", "pimcore/object/classes/data/quantityValue.js", "pimcore/object/classes/data/inputQuantityValue.js", "pimcore/object/classes/data/quantityValueRange.js", "pimcore/object/classes/data/calculatedValue.js", "pimcore/object/classes/layout/layout.js", "pimcore/object/classes/layout/accordion.js", "pimcore/object/classes/layout/fieldset.js", "pimcore/object/classes/layout/fieldcontainer.js", "pimcore/object/classes/layout/panel.js", "pimcore/object/classes/layout/region.js", "pimcore/object/classes/layout/tabpanel.js", "pimcore/object/classes/layout/iframe.js", "pimcore/object/fieldlookup/filterdialog.js", "pimcore/object/fieldlookup/helper.js", "pimcore/object/classes/layout/text.js", "pimcore/object/fieldcollection.js", "pimcore/object/fieldcollections/field.js", "pimcore/object/gridcolumn/Abstract.js", "pimcore/object/gridcolumn/operator/IsEqual.js", "pimcore/object/gridcolumn/operator/Text.js", "pimcore/object/gridcolumn/operator/Anonymizer.js", "pimcore/object/gridcolumn/operator/AnyGetter.js", "pimcore/object/gridcolumn/operator/AssetMetadataGetter.js", "pimcore/object/gridcolumn/operator/Arithmetic.js", "pimcore/object/gridcolumn/operator/Boolean.js", "pimcore/object/gridcolumn/operator/BooleanFormatter.js", "pimcore/object/gridcolumn/operator/CaseConverter.js", "pimcore/object/gridcolumn/operator/CharCounter.js", "pimcore/object/gridcolumn/operator/Concatenator.js", "pimcore/object/gridcolumn/operator/DateFormatter.js", "pimcore/object/gridcolumn/operator/ElementCounter.js", "pimcore/object/gridcolumn/operator/Iterator.js", "pimcore/object/gridcolumn/operator/JSON.js", "pimcore/object/gridcolumn/operator/LocaleSwitcher.js", "pimcore/object/gridcolumn/operator/Merge.js", "pimcore/object/gridcolumn/operator/ObjectFieldGetter.js", "pimcore/object/gridcolumn/operator/PHP.js", "pimcore/object/gridcolumn/operator/PHPCode.js", "pimcore/object/gridcolumn/operator/Base64.js", "pimcore/object/gridcolumn/operator/TranslateValue.js", "pimcore/object/gridcolumn/operator/PropertyGetter.js", "pimcore/object/gridcolumn/operator/RequiredBy.js", "pimcore/object/gridcolumn/operator/StringContains.js", "pimcore/object/gridcolumn/operator/StringReplace.js", "pimcore/object/gridcolumn/operator/Substring.js", "pimcore/object/gridcolumn/operator/LFExpander.js", "pimcore/object/gridcolumn/operator/Trimmer.js", "pimcore/object/gridcolumn/operator/Alias.js", "pimcore/object/gridcolumn/operator/WorkflowState.js", "pimcore/object/gridcolumn/value/DefaultValue.js", "pimcore/object/gridcolumn/operator/GeopointRenderer.js", "pimcore/object/gridcolumn/operator/ImageRenderer.js", "pimcore/object/gridcolumn/operator/HotspotimageRenderer.js", "pimcore/object/importcolumn/Abstract.js", "pimcore/object/importcolumn/operator/Base64.js", "pimcore/object/importcolumn/operator/Ignore.js", "pimcore/object/importcolumn/operator/Iterator.js", "pimcore/object/importcolumn/operator/LocaleSwitcher.js", "pimcore/object/importcolumn/operator/ObjectBrickSetter.js", "pimcore/object/importcolumn/operator/PHPCode.js", "pimcore/object/importcolumn/operator/Published.js", "pimcore/object/importcolumn/operator/Splitter.js", "pimcore/object/importcolumn/operator/Unserialize.js", "pimcore/object/importcolumn/value/DefaultValue.js", "pimcore/object/objectbrick.js", "pimcore/object/objectbricks/field.js", "pimcore/object/selectoptions.js", "pimcore/object/selectoptionsitems/definition.js", "pimcore/object/tags/abstract.js", "pimcore/object/tags/abstractRelations.js", "pimcore/object/tags/block.js", "pimcore/object/tags/rgbaColor.js", "pimcore/object/tags/date.js", "pimcore/object/tags/datetime.js", "pimcore/object/tags/dateRange.js", "pimcore/object/tags/time.js", "pimcore/object/tags/manyToOneRelation.js", "pimcore/object/tags/image.js", "pimcore/object/tags/encryptedField.js", "pimcore/object/tags/externalImage.js", "pimcore/object/tags/hotspotimage.js", "pimcore/object/tags/imagegallery.js", "pimcore/object/tags/video.js", "pimcore/object/tags/input.js", "pimcore/object/tags/classificationstore.js", "pimcore/object/tags/numeric.js", "pimcore/object/tags/numericRange.js", "pimcore/object/tags/manyToManyObjectRelation.js", "pimcore/object/tags/advancedManyToManyRelation.js", "pimcore/object/gridcolumn/operator/FieldCollectionGetter.js", "pimcore/object/tags/advancedManyToManyObjectRelation.js", "pimcore/object/tags/reverseObjectRelation.js", "pimcore/object/tags/urlSlug.js", "pimcore/object/tags/booleanSelect.js", "pimcore/object/tags/select.js", "pimcore/object/tags/user.js", "pimcore/object/tags/checkbox.js", "pimcore/object/tags/consent.js", "pimcore/object/tags/textarea.js", "pimcore/object/tags/wysiwyg.js", "pimcore/object/tags/slider.js", "pimcore/object/tags/manyToManyRelation.js", "pimcore/object/tags/table.js", "pimcore/object/tags/structuredTable.js", "pimcore/object/tags/country.js", "pimcore/object/tags/geo/abstract.js", "pimcore/object/tags/geobounds.js", "pimcore/object/tags/geopoint.js", "pimcore/object/tags/geopolygon.js", "pimcore/object/tags/geopolyline.js", "pimcore/object/tags/language.js", "pimcore/object/tags/password.js", "pimcore/object/tags/multiselect.js", "pimcore/object/tags/link.js", "pimcore/object/tags/fieldcollections.js", "pimcore/object/tags/localizedfields.js", "pimcore/object/tags/countrymultiselect.js", "pimcore/object/tags/languagemultiselect.js", "pimcore/object/tags/objectbricks.js", "pimcore/object/tags/firstname.js", "pimcore/object/tags/lastname.js", "pimcore/object/tags/email.js", "pimcore/object/tags/gender.js", "pimcore/object/tags/quantityValue.js", "pimcore/object/tags/quantityValueRange.js", "pimcore/object/tags/inputQuantityValue.js", "pimcore/object/tags/calculatedValue.js", "pimcore/object/preview.js", "pimcore/object/versions.js", "pimcore/object/variantsTab.js", "pimcore/object/folder/search.js", "pimcore/object/edit.js", "pimcore/object/abstract.js", "pimcore/object/object.js", "pimcore/object/folder.js", "pimcore/object/variant.js", "pimcore/object/tree.js", "pimcore/object/layout/iframe.js", "pimcore/object/customviews/tree.js", "pimcore/object/quantityvalue/unitsettings.js", "pimcore/object/gridexport/csv.js", "pimcore/object/gridexport/xlsx.js", "pimcore/layout/portal.js", "pimcore/layout/portlets/abstract.js", "pimcore/layout/portlets/modifiedDocuments.js", "pimcore/layout/portlets/modifiedObjects.js", "pimcore/layout/portlets/modifiedAssets.js", "pimcore/layout/portlets/modificationStatistic.js", "pimcore/layout/menu.js", "pimcore/layout/toolbar.js", "pimcore/layout/treepanelmanager.js", "pimcore/document/seemode.js", "pimcore/object/classificationstore/groupsPanel.js", "pimcore/object/classificationstore/propertiesPanel.js", "pimcore/object/classificationstore/collectionsPanel.js", "pimcore/object/classificationstore/keyDefinitionWindow.js", "pimcore/object/classificationstore/keySelectionWindow.js", "pimcore/object/classificationstore/relationSelectionWindow.js", "pimcore/object/classificationstore/storeConfiguration.js", "pimcore/object/classificationstore/storeTree.js", "pimcore/object/classificationstore/columnConfigDialog.js", "pimcore/workflow/transitionPanel.js", "pimcore/workflow/transitions.js", "pimcore/workflow/transitions.js", "pimcore/colorpicker-overrides.js", "pimcore/notification/helper.js", "pimcore/notification/panel.js", "pimcore/notification/modal.js"];
        // line 621
        echo "
<!-- some javascript -->
";
        // line 624
        echo "<script ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 624, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 624), 624, $this->source);
        echo ">
    pimcore.settings = ";
        // line 625
        echo json_encode($this->sandbox->ensureToStringAllowed((isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 625, $this->source); })()), 625, $this->source), twig_constant("JSON_PRETTY_PRINT"));
        echo ";
</script>

<script src=\"";
        // line 628
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_misc_jsontranslationssystem", ["language" => (isset($context["language"]) || array_key_exists("language", $context) ? $context["language"] : (function () { throw new RuntimeError('Variable "language" does not exist.', 628, $this->source); })()), "_dc" => twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 628, $this->source); })()), "build", [], "any", false, false, true, 628)]), "html", null, true);
        echo "\" ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 628, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 628), 628, $this->source);
        echo "></script>
<script src=\"";
        // line 629
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_user_getcurrentuser", ["_dc" => twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 629, $this->source); })()), "build", [], "any", false, false, true, 629)]), "html", null, true);
        echo "\" ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 629, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 629), 629, $this->source);
        echo "></script>
<script src=\"";
        // line 630
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_misc_availablelanguages", ["_dc" => twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 630, $this->source); })()), "build", [], "any", false, false, true, 630)]), "html", null, true);
        echo "\" ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 630, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 630), 630, $this->source);
        echo "></script>

<!-- library scripts -->
";
        // line 633
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["scriptLibs"]) || array_key_exists("scriptLibs", $context) ? $context["scriptLibs"] : (function () { throw new RuntimeError('Variable "scriptLibs" does not exist.', 633, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["scriptUrl"]) {
            // line 634
            echo "    <script src=\"/bundles/pimcoreadmin/js/";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["scriptUrl"], 634, $this->source), "html", null, true);
            echo "?_dc=";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 634, $this->source); })()), "build", [], "any", false, false, true, 634), 634, $this->source), "html", null, true);
            echo "\" ";
            echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 634, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 634), 634, $this->source);
            echo "></script>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['scriptUrl'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 636
        echo "
<!-- internal scripts -->
";
        // line 638
        if (twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 638, $this->source); })()), "disableMinifyJs", [], "any", false, false, true, 638)) {
            // line 639
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["scripts"]) || array_key_exists("scripts", $context) ? $context["scripts"] : (function () { throw new RuntimeError('Variable "scripts" does not exist.', 639, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["scriptUrl"]) {
                // line 640
                echo "        <script src=\"/bundles/pimcoreadmin/js/";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["scriptUrl"], 640, $this->source), "html", null, true);
                echo "?_dc=";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 640, $this->source); })()), "build", [], "any", false, false, true, 640), 640, $this->source), "html", null, true);
                echo "\"></script>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['scriptUrl'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        } else {
            // line 643
            echo "    ";
            echo $this->extensions['Pimcore\Bundle\AdminBundle\Twig\Extension\AdminExtension']->minimize($this->sandbox->ensureToStringAllowed((isset($context["scripts"]) || array_key_exists("scripts", $context) ? $context["scripts"] : (function () { throw new RuntimeError('Variable "scripts" does not exist.', 643, $this->source); })()), 643, $this->source));
            echo "
";
        }
        // line 645
        echo "
";
        // line 647
        echo "
";
        // line 651
        echo "
";
        // line 652
        $context["pluginDcValue"] = twig_date_format_filter($this->env, "now", "U");
        // line 653
        if (twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 653, $this->source); })()), "disableMinifyJs", [], "any", false, false, true, 653)) {
            // line 654
            echo "    ";
            $context["pluginDcValue"] = 1;
        }
        // line 656
        echo "
<!-- bundle scripts -->
";
        // line 658
        if (twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 658, $this->source); })()), "disableMinifyJs", [], "any", false, false, true, 658)) {
            // line 659
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["pluginJsPaths"]) || array_key_exists("pluginJsPaths", $context) ? $context["pluginJsPaths"] : (function () { throw new RuntimeError('Variable "pluginJsPaths" does not exist.', 659, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["pluginJsPath"]) {
                // line 660
                echo "        <script src=\"";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["pluginJsPath"], 660, $this->source), "html", null, true);
                echo "?_dc=";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["pluginDcValue"]) || array_key_exists("pluginDcValue", $context) ? $context["pluginDcValue"] : (function () { throw new RuntimeError('Variable "pluginDcValue" does not exist.', 660, $this->source); })()), 660, $this->source), "html", null, true);
                echo "\" ";
                echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 660, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 660), 660, $this->source);
                echo "></script>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['pluginJsPath'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        } else {
            // line 663
            echo "    ";
            echo $this->extensions['Pimcore\Bundle\AdminBundle\Twig\Extension\AdminExtension']->minimize($this->sandbox->ensureToStringAllowed((isset($context["pluginJsPaths"]) || array_key_exists("pluginJsPaths", $context) ? $context["pluginJsPaths"] : (function () { throw new RuntimeError('Variable "pluginJsPaths" does not exist.', 663, $this->source); })()), 663, $this->source));
            echo "
";
        }
        // line 665
        echo "
";
        // line 666
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["pluginCssPaths"]) || array_key_exists("pluginCssPaths", $context) ? $context["pluginCssPaths"] : (function () { throw new RuntimeError('Variable "pluginCssPaths" does not exist.', 666, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["pluginCssPath"]) {
            // line 667
            echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["pluginCssPath"], 667, $this->source), "html", null, true);
            echo "?_dc=";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["pluginDcValue"]) || array_key_exists("pluginDcValue", $context) ? $context["pluginDcValue"] : (function () { throw new RuntimeError('Variable "pluginDcValue" does not exist.', 667, $this->source); })()), 667, $this->source), "html", null, true);
            echo "\"/>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['pluginCssPath'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 669
        echo "
";
        // line 671
        echo "<script src=\"/bundles/pimcoreadmin/js/pimcore/startup.js?_dc=";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 671, $this->source); })()), "build", [], "any", false, false, true, 671), 671, $this->source), "html", null, true);
        echo "\" ";
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 671, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 671), 671, $this->source);
        echo "></script>
</body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 126
    public function block_notification($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "notification"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "notification"));

        // line 127
        echo "        <div id=\"pimcore_notification\" data-menu-tooltip=\"";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("notifications", [], "admin"), "html", null, true);
        echo "\" class=\"pimcore_icon_comments\">
            <img src=\"/bundles/pimcoreadmin/img/material-icons/outline-sms-24px.svg\">
            <span id=\"notification_value\" style=\"display:none;\"></span>
        </div>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 134
    public function block_avatar($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "avatar"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "avatar"));

        // line 135
        echo "        <div id=\"pimcore_avatar\" style=\"display:none;\">
            <img src=\"";
        // line 136
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_user_getimage");
        echo "\" data-menu-tooltip=\"";
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new RuntimeError('Variable "user" does not exist.', 136, $this->source); })()), "name", [], "any", false, false, true, 136), 136, $this->source), "html", null, true);
        echo " | ";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("my_profile", [], "admin"), "html", null, true);
        echo "\"/>
        </div>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 140
    public function block_logout($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "logout"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "logout"));

        // line 141
        echo "        <form id=\"pimcore_logout_form\" method=\"post\" action=\"";
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_logout");
        echo "\">
            <input type=\"hidden\" name=\"csrfToken\" value=\"";
        // line 142
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csrf"]) || array_key_exists("pimcore_csrf", $context) ? $context["pimcore_csrf"] : (function () { throw new RuntimeError('Variable "pimcore_csrf" does not exist.', 142, $this->source); })()), "getCsrfToken", [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 142, $this->source); })()), "request", [], "any", false, false, true, 142), "session", [], "any", false, false, true, 142)], "method", false, false, true, 142), 142, $this->source), "html", null, true);
        echo "\">

            <a id=\"pimcore_logout\" data-menu-tooltip=\"";
        // line 144
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("logout", [], "admin"), "html", null, true);
        echo "\" href=\"#\" style=\"display: none\">
                <img src=\"/bundles/pimcoreadmin/img/material-icons/outline-logout-24px.svg\">
            </a>
        </form>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 161
    public function block_stylesheets($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 162
        echo "
    ";
        // line 163
        $context["styles"] = [$this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_misc_admincss"), "/bundles/pimcoreadmin/css/icons.css", "/bundles/pimcoreadmin/extjs/css/PimcoreApp-all_1.css", "/bundles/pimcoreadmin/extjs/css/PimcoreApp-all_2.css", "/bundles/pimcoreadmin/css/admin.css"];
        // line 170
        echo "
    <!-- stylesheets -->
    <style type=\"text/css\">
        ";
        // line 178
        echo "        ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["styles"]) || array_key_exists("styles", $context) ? $context["styles"] : (function () { throw new RuntimeError('Variable "styles" does not exist.', 178, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["style"]) {
            // line 179
            echo "            @import url(\"";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["style"], 179, $this->source), "html", null, true);
            echo "?_dc=";
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["settings"]) || array_key_exists("settings", $context) ? $context["settings"] : (function () { throw new RuntimeError('Variable "settings" does not exist.', 179, $this->source); })()), "build", [], "any", false, false, true, 179), 179, $this->source), "html", null, true);
            echo "\");
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['style'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 181
        echo "    </style>

";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@PimcoreAdmin/admin/index/index.html.twig";
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
        return array (  582 => 181,  571 => 179,  566 => 178,  561 => 170,  559 => 163,  556 => 162,  546 => 161,  531 => 144,  526 => 142,  521 => 141,  511 => 140,  494 => 136,  491 => 135,  481 => 134,  465 => 127,  455 => 126,  438 => 671,  435 => 669,  424 => 667,  420 => 666,  417 => 665,  411 => 663,  397 => 660,  392 => 659,  390 => 658,  386 => 656,  382 => 654,  380 => 653,  378 => 652,  375 => 651,  372 => 647,  369 => 645,  363 => 643,  351 => 640,  346 => 639,  344 => 638,  340 => 636,  327 => 634,  323 => 633,  315 => 630,  309 => 629,  303 => 628,  297 => 625,  292 => 624,  288 => 621,  286 => 210,  283 => 209,  280 => 207,  276 => 205,  274 => 204,  272 => 196,  271 => 194,  268 => 193,  265 => 191,  261 => 189,  259 => 188,  257 => 187,  254 => 186,  251 => 184,  249 => 161,  246 => 160,  232 => 150,  229 => 149,  227 => 140,  224 => 139,  222 => 134,  218 => 132,  216 => 126,  206 => 118,  204 => 117,  192 => 108,  186 => 105,  182 => 104,  175 => 102,  169 => 101,  156 => 91,  151 => 89,  143 => 83,  137 => 81,  135 => 80,  131 => 79,  55 => 5,  53 => 4,  51 => 3,  49 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% set language = app.request.locale %}
{# @var \\Pimcore\\Security\\User\\User #}
{% set userProxy = app.user %}
{% set user = userProxy.user %}

<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <meta name=\"robots\" content=\"noindex, nofollow\"/>
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\"/>
    <meta name=\"apple-mobile-web-app-capable\" content=\"yes\"/>

    <link rel=\"icon\" type=\"image/png\" href=\"/bundles/pimcoreadmin/img/favicon/favicon-32x32.png\"/>
    <meta name=\"google\" value=\"notranslate\">

    <style type=\"text/css\">
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }

        #pimcore_loading {
            margin: 0 auto;
            width: 300px;
            padding: 300px 0 0 0;
            text-align: center;
        }

        .spinner {
            margin: 100px auto 0;
            width: 70px;
            text-align: center;
        }

        .spinner > div {
            width: 18px;
            height: 18px;
            background-color: #3d3d3d;

            border-radius: 100%;
            display: inline-block;
            -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
            animation: sk-bouncedelay 1.4s infinite ease-in-out both;
        }

        .spinner .bounce1 {
            -webkit-animation-delay: -0.32s;
            animation-delay: -0.32s;
        }

        .spinner .bounce2 {
            -webkit-animation-delay: -0.16s;
            animation-delay: -0.16s;
        }

        @-webkit-keyframes sk-bouncedelay {
            0%, 80%, 100% {
                -webkit-transform: scale(0)
            }
            40% {
                -webkit-transform: scale(1.0)
            }
        }

        @keyframes sk-bouncedelay {
            0%, 80%, 100% {
                -webkit-transform: scale(0);
                transform: scale(0);
            }
            40% {
                -webkit-transform: scale(1.0);
                transform: scale(1.0);
            }
        }

        #pimcore_panel_tabs-body {
            background-image: url({{ path('pimcore_settings_display_custom_logo') }});
            {% if adminSettings['branding']['color_admin_interface_background']|default(null) is not null %}
                background-color: {{ adminSettings['branding']['color_admin_interface_background'] }};
            {% endif %}
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 500px auto;
        }
    </style>

    <title>{{ settings.hostname }} :: Pimcore</title>

    <script {{ pimcore_csp.getNonceHtmlAttribute()|raw }}>
        var pimcore = {}; // namespace

        // hide symfony toolbar by default
        var symfonyToolbarKey = 'symfony/profiler/toolbar/displayState';
        if(!window.localStorage.getItem(symfonyToolbarKey)) {
            window.localStorage.setItem(symfonyToolbarKey, 'none');
        }
    </script>

    <script src=\"{{ asset('bundles/fosjsrouting/js/router.js') }}\" {{ pimcore_csp.getNonceHtmlAttribute()|raw }}></script>
    <script src=\"{{ path('fos_js_routing_js', {'callback' : 'fos.Router.setData'}) }}\" {{ pimcore_csp.getNonceHtmlAttribute()|raw }}></script>

    {{ encore_entry_script_tags('admin', null, 'pimcoreAdmin') }}
    {{ encore_entry_link_tags('admin', null, 'pimcoreAdmin') }}
</head>

<body class=\"pimcore_version_11\" data-app-env=\"{{ app.environment }}\">

<div id=\"pimcore_loading\">
    <div class=\"spinner\">
        <div class=\"bounce1\"></div>
        <div class=\"bounce2\"></div>
        <div class=\"bounce3\"></div>
    </div>
</div>
{% set runtimePerspective = perspectiveConfig.getRuntimePerspective(user) %}

<div id=\"pimcore_sidebar\">
    <div id=\"pimcore_navigation\" style=\"display:none;\">
        <ul id=\"pimcore_navigation_ul\"></ul>
    </div>

    <div id=\"pimcore_status\"></div>

    {% block notification %}
        <div id=\"pimcore_notification\" data-menu-tooltip=\"{{ \"notifications\"|trans([],'admin') }}\" class=\"pimcore_icon_comments\">
            <img src=\"/bundles/pimcoreadmin/img/material-icons/outline-sms-24px.svg\">
            <span id=\"notification_value\" style=\"display:none;\"></span>
        </div>
    {% endblock %}


    {% block avatar %}
        <div id=\"pimcore_avatar\" style=\"display:none;\">
            <img src=\"{{ path('pimcore_admin_user_getimage') }}\" data-menu-tooltip=\"{{ user.name }} | {{ 'my_profile'|trans([],'admin') }}\"/>
        </div>
    {% endblock %}

    {% block logout %}
        <form id=\"pimcore_logout_form\" method=\"post\" action=\"{{ path('pimcore_admin_logout') }}\">
            <input type=\"hidden\" name=\"csrfToken\" value=\"{{ pimcore_csrf.getCsrfToken(app.request.session) }}\">

            <a id=\"pimcore_logout\" data-menu-tooltip=\"{{ \"logout\"|trans([],'admin') }}\" href=\"#\" style=\"display: none\">
                <img src=\"/bundles/pimcoreadmin/img/material-icons/outline-logout-24px.svg\">
            </a>
        </form>
    {% endblock %}

    <div id=\"pimcore_signet\" data-menu-tooltip=\"Pimcore Platform ({{ settings.version }}|{{ settings.build }})\" style=\"text-indent: -10000px\">
        BE RESPECTFUL AND HONOR OUR WORK FOR FREE & OPEN SOURCE SOFTWARE BY NOT REMOVING OUR LOGO.
        WE OFFER YOU THE POSSIBILITY TO ADDITIONALLY ADD YOUR OWN LOGO IN PIMCORE'S SYSTEM SETTINGS. THANK YOU!
    </div>
</div>

<div id=\"pimcore_tooltip\" style=\"display: none;\"></div>
<div id=\"pimcore_quicksearch\"></div>

{# define stylesheets #}

{% block stylesheets %}

    {% set styles = [
        path('pimcore_admin_misc_admincss'),
        \"/bundles/pimcoreadmin/css/icons.css\",
        \"/bundles/pimcoreadmin/extjs/css/PimcoreApp-all_1.css\",
        \"/bundles/pimcoreadmin/extjs/css/PimcoreApp-all_2.css\",
        \"/bundles/pimcoreadmin/css/admin.css\"
    ] %}

    <!-- stylesheets -->
    <style type=\"text/css\">
        {#
         # use @import here, because if IE9 CSS file limitations (31 files)
         # see also: http://blogs.telerik.com/blogs/posts/10-05-03/internet-explorer-css-limits.aspx
         # @import bypasses this problem in an elegant way
        #}
        {% for style in styles %}
            @import url(\"{{ style }}?_dc={{ settings.build }}\");
        {% endfor %}
    </style>

{% endblock %}

{# define scripts #}

{% set debugSuffix = '' %}
{% if settings.disableMinifyJs %}
    {% set debugSuffix = \"-debug\" %}
{% endif %}

{# SCRIPT LIBRARIES #}

{% set scriptLibs = [
    \"lib/class.js\",
    \"../extjs/js/ext-all\" ~ debugSuffix ~ \".js\",
    \"lib/ext-plugins/portlet/PortalDropZone.js\",
    \"lib/ext-plugins/portlet/Portlet.js\",
    \"lib/ext-plugins/portlet/PortalColumn.js\",
    \"lib/ext-plugins/portlet/PortalPanel.js\",
    \"../build/admin/ace-builds/src-min-noconflict/ace.js\",
    \"../build/admin/ace-builds/src-min-noconflict/ext-modelist.js\"
] %}
{% if pimcore_file_exists(constant('PIMCORE_WEB_ROOT') ~ '/bundles/pimcoreadmin/js/lib/ext-locale/locale-' ~ language ~ '.js') %}
    {% set scriptLibs = scriptLibs|merge(['lib/ext-locale/locale-' ~ language ~ '.js']) %}
{% endif %}

{# PIMCORE SCRIPTS #}

{% set scripts = [


    \"pimcore/functions.js\",
    \"pimcore/common.js\",
    \"pimcore/elementservice.js\",
    \"pimcore/helpers.js\",
    \"pimcore/error.js\",
    \"pimcore/events.js\",

    \"pimcore/treenodelocator.js\",
    \"pimcore/helpers/generic-grid.js\",
    \"pimcore/helpers/quantityValue.js\",
    \"pimcore/overrides.js\",

    \"pimcore/perspective.js\",
    \"pimcore/user.js\",

    \"pimcore/tool/paralleljobs.js\",
    \"pimcore/tool/genericiframewindow.js\",


    \"pimcore/settings/user/panels/abstract.js\",
    \"pimcore/settings/user/panel.js\",

    \"pimcore/settings/user/usertab.js\",
    \"pimcore/settings/user/editorSettings.js\",
    \"pimcore/settings/user/websiteTranslationSettings.js\",
    \"pimcore/settings/user/role/panel.js\",
    \"pimcore/settings/user/role/tab.js\",
    \"pimcore/settings/user/user/objectrelations.js\",
    \"pimcore/settings/user/user/settings.js\",
    \"pimcore/settings/user/user/keyBindings.js\",
    \"pimcore/settings/user/workspaces.js\",
    \"pimcore/settings/user/workspace/asset.js\",
    \"pimcore/settings/user/workspace/document.js\",
    \"pimcore/settings/user/workspace/object.js\",
    \"pimcore/settings/user/workspace/customlayouts.js\",
    \"pimcore/settings/user/workspace/language.js\",
    \"pimcore/settings/user/workspace/special.js\",
    \"pimcore/settings/user/role/settings.js\",
    \"pimcore/settings/profile/panel.js\",
    \"pimcore/settings/profile/twoFactorSettings.js\",
    \"pimcore/settings/thumbnail/item.js\",
    \"pimcore/settings/thumbnail/panel.js\",
    \"pimcore/settings/videothumbnail/item.js\",
    \"pimcore/settings/videothumbnail/panel.js\",
    \"pimcore/settings/translation.js\",
    \"pimcore/settings/translationEditor.js\",
    \"pimcore/settings/translation/translationmerger.js\",
    \"pimcore/settings/translation/translationSettingsTab.js\",
    \"pimcore/settings/metadata/predefined.js\",
    \"pimcore/settings/properties/predefined.js\",
    \"pimcore/settings/docTypes.js\",
    \"pimcore/settings/system.js\",
    \"pimcore/settings/systemAppearance.js\",
    \"pimcore/settings/website.js\",

    \"pimcore/settings/recyclebin.js\",
    \"pimcore/settings/maintenance.js\",
    \"pimcore/settings/email/log.js\",
    \"pimcore/settings/email/blocklist.js\",

    \"pimcore/settings/gdpr/gdprPanel.js\",
    \"pimcore/settings/gdpr/dataproviders/assets.js\",
    \"pimcore/settings/gdpr/dataproviders/dataObjects.js\",
    \"pimcore/settings/gdpr/dataproviders/sentMail.js\",
    \"pimcore/settings/gdpr/dataproviders/pimcoreUsers.js\",

    \"pimcore/element/applicationLoggerPanelFacade.js\",
    \"pimcore/element/customReportsPanelFacade.js\",
    \"pimcore/element/selector/searchFacade.js\",

    \"pimcore/element/abstract.js\",
    \"pimcore/element/abstractPreview.js\",
    \"pimcore/element/properties.js\",
    \"pimcore/element/scheduler.js\",
    \"pimcore/element/dependencies.js\",
    \"pimcore/element/metainfo.js\",
    \"pimcore/element/history.js\",
    \"pimcore/element/notes.js\",
    \"pimcore/element/note_details.js\",
    \"pimcore/element/workflows.js\",
    \"pimcore/element/tag/imagecropper.js\",
    \"pimcore/element/tag/imagehotspotmarkereditor.js\",
    \"pimcore/element/replace_assignments.js\",
    \"pimcore/element/permissionchecker.js\",
    \"pimcore/element/gridexport/abstract.js\",
    \"pimcore/element/helpers/gridColumnConfig.js\",
    \"pimcore/element/helpers/gridConfigDialog.js\",
    \"pimcore/element/helpers/gridCellEditor.js\",
    \"pimcore/element/helpers/gridTabAbstract.js\",
    \"pimcore/object/helpers/grid.js\",
    \"pimcore/object/helpers/gridConfigDialog.js\",
    \"pimcore/object/helpers/classTree.js\",
    \"pimcore/object/helpers/gridTabAbstract.js\",
    \"pimcore/object/helpers/metadataMultiselectEditor.js\",
    \"pimcore/object/helpers/customLayoutEditor.js\",
    \"pimcore/object/helpers/optionEditor.js\",
    \"pimcore/object/helpers/imageGalleryDropZone.js\",
    \"pimcore/object/helpers/imageGalleryPanel.js\",
    \"pimcore/object/helpers/selectField.js\",
    \"pimcore/object/helpers/reservedWords.js\",
    \"pimcore/element/tag/configuration.js\",
    \"pimcore/element/tag/assignment.js\",
    \"pimcore/element/tag/tree.js\",
    \"pimcore/asset/helpers/metadataTree.js\",
    \"pimcore/asset/helpers/gridConfigDialog.js\",
    \"pimcore/asset/helpers/gridTabAbstract.js\",
    \"pimcore/asset/helpers/grid.js\",


    \"pimcore/document/properties.js\",
    \"pimcore/document/document.js\",
    \"pimcore/document/page_snippet.js\",
    \"pimcore/document/edit.js\",
    \"pimcore/document/versions.js\",
    \"pimcore/document/settings_abstract.js\",
    \"pimcore/document/pages/settings.js\",
    \"pimcore/document/pages/preview.js\",
    \"pimcore/document/snippets/settings.js\",
    \"pimcore/document/emails/settings.js\",
    \"pimcore/document/link.js\",
    \"pimcore/document/hardlink.js\",
    \"pimcore/document/folder.js\",
    \"pimcore/document/tree.js\",
    \"pimcore/document/snippet.js\",
    \"pimcore/document/email.js\",
    \"pimcore/document/page.js\",
    \"pimcore/document/document_language_overview.js\",
    \"pimcore/document/customviews/tree.js\",

    \"pimcore/asset/metadata/data/data.js\",
    \"pimcore/asset/metadata/data/input.js\",
    \"pimcore/asset/metadata/data/textarea.js\",
    \"pimcore/asset/metadata/data/asset.js\",
    \"pimcore/asset/metadata/data/document.js\",
    \"pimcore/asset/metadata/data/object.js\",
    \"pimcore/asset/metadata/data/date.js\",
    \"pimcore/asset/metadata/data/checkbox.js\",
    \"pimcore/asset/metadata/data/select.js\",

    \"pimcore/asset/metadata/tags/abstract.js\",
    \"pimcore/asset/metadata/tags/checkbox.js\",
    \"pimcore/asset/metadata/tags/date.js\",
    \"pimcore/asset/metadata/tags/input.js\",
    \"pimcore/asset/metadata/tags/manyToOneRelation.js\",
    \"pimcore/asset/metadata/tags/asset.js\",
    \"pimcore/asset/metadata/tags/document.js\",
    \"pimcore/asset/metadata/tags/object.js\",
    \"pimcore/asset/metadata/tags/select.js\",
    \"pimcore/asset/metadata/tags/textarea.js\",
    \"pimcore/asset/asset.js\",
    \"pimcore/asset/unknown.js\",
    \"pimcore/asset/embedded_meta_data.js\",
    \"pimcore/asset/image.js\",
    \"pimcore/asset/document.js\",
    \"pimcore/asset/archive.js\",
    \"pimcore/asset/video.js\",
    \"pimcore/asset/audio.js\",
    \"pimcore/asset/text.js\",
    \"pimcore/asset/folder.js\",
    \"pimcore/asset/listfolder.js\",
    \"pimcore/asset/versions.js\",
    \"pimcore/asset/metadata/dataProvider.js\",
    \"pimcore/asset/metadata/grid.js\",
    \"pimcore/asset/metadata/editor.js\",
    \"pimcore/asset/tree.js\",
    \"pimcore/asset/customviews/tree.js\",
    \"pimcore/asset/gridexport/csv.js\",
    \"pimcore/asset/gridexport/xlsx.js\",

    \"pimcore/object/helpers/edit.js\",
    \"pimcore/object/helpers/layout.js\",
    \"pimcore/object/classes/class.js\",
    \"pimcore/object/class.js\",
    \"pimcore/object/bulk-base.js\",
    \"pimcore/object/bulk-export.js\",
    \"pimcore/object/bulk-import.js\",
    \"pimcore/object/classes/data/data.js\",
    \"pimcore/object/classes/data/block.js\",
    \"pimcore/object/classes/data/classificationstore.js\",
    \"pimcore/object/classes/data/rgbaColor.js\",
    \"pimcore/object/classes/data/date.js\",
    \"pimcore/object/classes/data/datetime.js\",
    \"pimcore/object/classes/data/dateRange.js\",
    \"pimcore/object/classes/data/encryptedField.js\",
    \"pimcore/object/classes/data/time.js\",
    \"pimcore/object/classes/data/manyToOneRelation.js\",
    \"pimcore/object/classes/data/image.js\",
    \"pimcore/object/classes/data/externalImage.js\",
    \"pimcore/object/classes/data/hotspotimage.js\",
    \"pimcore/object/classes/data/imagegallery.js\",
    \"pimcore/object/classes/data/video.js\",
    \"pimcore/object/classes/data/input.js\",
    \"pimcore/object/classes/data/numeric.js\",
    \"pimcore/object/classes/data/numericRange.js\",
    \"pimcore/object/classes/data/manyToManyObjectRelation.js\",
    \"pimcore/object/classes/data/advancedManyToManyRelation.js\",
    \"pimcore/object/classes/data/advancedManyToManyObjectRelation.js\",
    \"pimcore/object/classes/data/reverseObjectRelation.js\",
    \"pimcore/object/classes/data/booleanSelect.js\",
    \"pimcore/object/classes/data/select.js\",
    \"pimcore/object/classes/data/urlSlug.js\",
    \"pimcore/object/classes/data/user.js\",
    \"pimcore/object/classes/data/textarea.js\",
    \"pimcore/object/classes/data/wysiwyg.js\",
    \"pimcore/object/classes/data/checkbox.js\",
    \"pimcore/object/classes/data/consent.js\",
    \"pimcore/object/classes/data/slider.js\",
    \"pimcore/object/classes/data/manyToManyRelation.js\",
    \"pimcore/object/classes/data/table.js\",
    \"pimcore/object/classes/data/structuredTable.js\",
    \"pimcore/object/classes/data/country.js\",
    \"pimcore/object/classes/data/geo/abstract.js\",
    \"pimcore/object/classes/data/geopoint.js\",
    \"pimcore/object/classes/data/geobounds.js\",
    \"pimcore/object/classes/data/geopolygon.js\",
    \"pimcore/object/classes/data/geopolyline.js\",
    \"pimcore/object/classes/data/language.js\",
    \"pimcore/object/classes/data/password.js\",
    \"pimcore/object/classes/data/multiselect.js\",
    \"pimcore/object/classes/data/link.js\",
    \"pimcore/object/classes/data/fieldcollections.js\",
    \"pimcore/object/classes/data/objectbricks.js\",
    \"pimcore/object/classes/data/localizedfields.js\",
    \"pimcore/object/classes/data/countrymultiselect.js\",
    \"pimcore/object/classes/data/languagemultiselect.js\",
    \"pimcore/object/classes/data/firstname.js\",
    \"pimcore/object/classes/data/lastname.js\",
    \"pimcore/object/classes/data/email.js\",
    \"pimcore/object/classes/data/gender.js\",
    \"pimcore/object/classes/data/quantityValue.js\",
    \"pimcore/object/classes/data/inputQuantityValue.js\",
    \"pimcore/object/classes/data/quantityValueRange.js\",
    \"pimcore/object/classes/data/calculatedValue.js\",
    \"pimcore/object/classes/layout/layout.js\",
    \"pimcore/object/classes/layout/accordion.js\",
    \"pimcore/object/classes/layout/fieldset.js\",
    \"pimcore/object/classes/layout/fieldcontainer.js\",
    \"pimcore/object/classes/layout/panel.js\",
    \"pimcore/object/classes/layout/region.js\",
    \"pimcore/object/classes/layout/tabpanel.js\",
    \"pimcore/object/classes/layout/iframe.js\",
    \"pimcore/object/fieldlookup/filterdialog.js\",
    \"pimcore/object/fieldlookup/helper.js\",
    \"pimcore/object/classes/layout/text.js\",
    \"pimcore/object/fieldcollection.js\",
    \"pimcore/object/fieldcollections/field.js\",
    \"pimcore/object/gridcolumn/Abstract.js\",
    \"pimcore/object/gridcolumn/operator/IsEqual.js\",
    \"pimcore/object/gridcolumn/operator/Text.js\",
    \"pimcore/object/gridcolumn/operator/Anonymizer.js\",
    \"pimcore/object/gridcolumn/operator/AnyGetter.js\",
    \"pimcore/object/gridcolumn/operator/AssetMetadataGetter.js\",
    \"pimcore/object/gridcolumn/operator/Arithmetic.js\",
    \"pimcore/object/gridcolumn/operator/Boolean.js\",
    \"pimcore/object/gridcolumn/operator/BooleanFormatter.js\",
    \"pimcore/object/gridcolumn/operator/CaseConverter.js\",
    \"pimcore/object/gridcolumn/operator/CharCounter.js\",
    \"pimcore/object/gridcolumn/operator/Concatenator.js\",
    \"pimcore/object/gridcolumn/operator/DateFormatter.js\",
    \"pimcore/object/gridcolumn/operator/ElementCounter.js\",
    \"pimcore/object/gridcolumn/operator/Iterator.js\",
    \"pimcore/object/gridcolumn/operator/JSON.js\",
    \"pimcore/object/gridcolumn/operator/LocaleSwitcher.js\",
    \"pimcore/object/gridcolumn/operator/Merge.js\",
    \"pimcore/object/gridcolumn/operator/ObjectFieldGetter.js\",
    \"pimcore/object/gridcolumn/operator/PHP.js\",
    \"pimcore/object/gridcolumn/operator/PHPCode.js\",
    \"pimcore/object/gridcolumn/operator/Base64.js\",
    \"pimcore/object/gridcolumn/operator/TranslateValue.js\",
    \"pimcore/object/gridcolumn/operator/PropertyGetter.js\",
    \"pimcore/object/gridcolumn/operator/RequiredBy.js\",
    \"pimcore/object/gridcolumn/operator/StringContains.js\",
    \"pimcore/object/gridcolumn/operator/StringReplace.js\",
    \"pimcore/object/gridcolumn/operator/Substring.js\",
    \"pimcore/object/gridcolumn/operator/LFExpander.js\",
    \"pimcore/object/gridcolumn/operator/Trimmer.js\",
    \"pimcore/object/gridcolumn/operator/Alias.js\",
    \"pimcore/object/gridcolumn/operator/WorkflowState.js\",
    \"pimcore/object/gridcolumn/value/DefaultValue.js\",
    \"pimcore/object/gridcolumn/operator/GeopointRenderer.js\",
    \"pimcore/object/gridcolumn/operator/ImageRenderer.js\",
    \"pimcore/object/gridcolumn/operator/HotspotimageRenderer.js\",
    \"pimcore/object/importcolumn/Abstract.js\",
    \"pimcore/object/importcolumn/operator/Base64.js\",
    \"pimcore/object/importcolumn/operator/Ignore.js\",
    \"pimcore/object/importcolumn/operator/Iterator.js\",
    \"pimcore/object/importcolumn/operator/LocaleSwitcher.js\",
    \"pimcore/object/importcolumn/operator/ObjectBrickSetter.js\",
    \"pimcore/object/importcolumn/operator/PHPCode.js\",
    \"pimcore/object/importcolumn/operator/Published.js\",
    \"pimcore/object/importcolumn/operator/Splitter.js\",
    \"pimcore/object/importcolumn/operator/Unserialize.js\",
    \"pimcore/object/importcolumn/value/DefaultValue.js\",
    \"pimcore/object/objectbrick.js\",
    \"pimcore/object/objectbricks/field.js\",
    \"pimcore/object/selectoptions.js\",
    \"pimcore/object/selectoptionsitems/definition.js\",
    \"pimcore/object/tags/abstract.js\",
    \"pimcore/object/tags/abstractRelations.js\",
    \"pimcore/object/tags/block.js\",
    \"pimcore/object/tags/rgbaColor.js\",
    \"pimcore/object/tags/date.js\",
    \"pimcore/object/tags/datetime.js\",
    \"pimcore/object/tags/dateRange.js\",
    \"pimcore/object/tags/time.js\",
    \"pimcore/object/tags/manyToOneRelation.js\",
    \"pimcore/object/tags/image.js\",
    \"pimcore/object/tags/encryptedField.js\",
    \"pimcore/object/tags/externalImage.js\",
    \"pimcore/object/tags/hotspotimage.js\",
    \"pimcore/object/tags/imagegallery.js\",
    \"pimcore/object/tags/video.js\",
    \"pimcore/object/tags/input.js\",
    \"pimcore/object/tags/classificationstore.js\",
    \"pimcore/object/tags/numeric.js\",
    \"pimcore/object/tags/numericRange.js\",
    \"pimcore/object/tags/manyToManyObjectRelation.js\",
    \"pimcore/object/tags/advancedManyToManyRelation.js\",
    \"pimcore/object/gridcolumn/operator/FieldCollectionGetter.js\",
    \"pimcore/object/tags/advancedManyToManyObjectRelation.js\",
    \"pimcore/object/tags/reverseObjectRelation.js\",
    \"pimcore/object/tags/urlSlug.js\",
    \"pimcore/object/tags/booleanSelect.js\",
    \"pimcore/object/tags/select.js\",
    \"pimcore/object/tags/user.js\",
    \"pimcore/object/tags/checkbox.js\",
    \"pimcore/object/tags/consent.js\",
    \"pimcore/object/tags/textarea.js\",
    \"pimcore/object/tags/wysiwyg.js\",
    \"pimcore/object/tags/slider.js\",
    \"pimcore/object/tags/manyToManyRelation.js\",
    \"pimcore/object/tags/table.js\",
    \"pimcore/object/tags/structuredTable.js\",
    \"pimcore/object/tags/country.js\",
    \"pimcore/object/tags/geo/abstract.js\",
    \"pimcore/object/tags/geobounds.js\",
    \"pimcore/object/tags/geopoint.js\",
    \"pimcore/object/tags/geopolygon.js\",
    \"pimcore/object/tags/geopolyline.js\",
    \"pimcore/object/tags/language.js\",
    \"pimcore/object/tags/password.js\",
    \"pimcore/object/tags/multiselect.js\",
    \"pimcore/object/tags/link.js\",
    \"pimcore/object/tags/fieldcollections.js\",
    \"pimcore/object/tags/localizedfields.js\",
    \"pimcore/object/tags/countrymultiselect.js\",
    \"pimcore/object/tags/languagemultiselect.js\",
    \"pimcore/object/tags/objectbricks.js\",
    \"pimcore/object/tags/firstname.js\",
    \"pimcore/object/tags/lastname.js\",
    \"pimcore/object/tags/email.js\",
    \"pimcore/object/tags/gender.js\",
    \"pimcore/object/tags/quantityValue.js\",
    \"pimcore/object/tags/quantityValueRange.js\",
    \"pimcore/object/tags/inputQuantityValue.js\",
    \"pimcore/object/tags/calculatedValue.js\",
    \"pimcore/object/preview.js\",
    \"pimcore/object/versions.js\",
    \"pimcore/object/variantsTab.js\",
    \"pimcore/object/folder/search.js\",
    \"pimcore/object/edit.js\",
    \"pimcore/object/abstract.js\",
    \"pimcore/object/object.js\",
    \"pimcore/object/folder.js\",
    \"pimcore/object/variant.js\",
    \"pimcore/object/tree.js\",
    \"pimcore/object/layout/iframe.js\",
    \"pimcore/object/customviews/tree.js\",
    \"pimcore/object/quantityvalue/unitsettings.js\",
    \"pimcore/object/gridexport/csv.js\",
    \"pimcore/object/gridexport/xlsx.js\",

    \"pimcore/layout/portal.js\",
    \"pimcore/layout/portlets/abstract.js\",
    \"pimcore/layout/portlets/modifiedDocuments.js\",
    \"pimcore/layout/portlets/modifiedObjects.js\",
    \"pimcore/layout/portlets/modifiedAssets.js\",
    \"pimcore/layout/portlets/modificationStatistic.js\",

    \"pimcore/layout/menu.js\",
    \"pimcore/layout/toolbar.js\",
    \"pimcore/layout/treepanelmanager.js\",
    \"pimcore/document/seemode.js\",

    \"pimcore/object/classificationstore/groupsPanel.js\",
    \"pimcore/object/classificationstore/propertiesPanel.js\",
    \"pimcore/object/classificationstore/collectionsPanel.js\",
    \"pimcore/object/classificationstore/keyDefinitionWindow.js\",
    \"pimcore/object/classificationstore/keySelectionWindow.js\",
    \"pimcore/object/classificationstore/relationSelectionWindow.js\",
    \"pimcore/object/classificationstore/storeConfiguration.js\",
    \"pimcore/object/classificationstore/storeTree.js\",
    \"pimcore/object/classificationstore/columnConfigDialog.js\",


    \"pimcore/workflow/transitionPanel.js\",
    \"pimcore/workflow/transitions.js\",
    \"pimcore/workflow/transitions.js\",


    \"pimcore/colorpicker-overrides.js\",


    \"pimcore/notification/helper.js\",
    \"pimcore/notification/panel.js\",
    \"pimcore/notification/modal.js\",
]
%}

<!-- some javascript -->
{# pimcore constants #}
<script {{ pimcore_csp.getNonceHtmlAttribute()|raw }}>
    pimcore.settings = {{(settings|json_encode(constant('JSON_PRETTY_PRINT'))|raw)}};
</script>

<script src=\"{{ path('pimcore_admin_misc_jsontranslationssystem', {'language': language, '_dc': settings.build }) }}\" {{ pimcore_csp.getNonceHtmlAttribute()|raw }}></script>
<script src=\"{{ path('pimcore_admin_user_getcurrentuser', {'_dc': settings.build }) }}\" {{ pimcore_csp.getNonceHtmlAttribute()|raw }}></script>
<script src=\"{{ path('pimcore_admin_misc_availablelanguages', {'_dc': settings.build }) }}\" {{ pimcore_csp.getNonceHtmlAttribute()|raw }}></script>

<!-- library scripts -->
{% for scriptUrl in scriptLibs %}
    <script src=\"/bundles/pimcoreadmin/js/{{ scriptUrl }}?_dc={{ settings.build }}\" {{ pimcore_csp.getNonceHtmlAttribute()|raw }}></script>
{% endfor %}

<!-- internal scripts -->
{% if settings.disableMinifyJs %}
    {% for scriptUrl in scripts %}
        <script src=\"/bundles/pimcoreadmin/js/{{ scriptUrl }}?_dc={{ settings.build }}\"></script>
    {% endfor %}
{% else %}
    {{ pimcore_minimize_scripts(scripts)|raw }}
{% endif %}

{# load plugin scripts #}

{# // only add the timestamp if the devmode is not activated, otherwise it is very hard to develop and debug plugins,
 # // because the filename changes on every reload and therefore breakpoints, ... are resetted on every reload
#}

{% set pluginDcValue = \"now\"|date('U') %}
{% if settings.disableMinifyJs %}
    {% set pluginDcValue = 1 %}
{% endif %}

<!-- bundle scripts -->
{% if settings.disableMinifyJs %}
    {% for pluginJsPath in pluginJsPaths %}
        <script src=\"{{ pluginJsPath }}?_dc={{ pluginDcValue }}\" {{ pimcore_csp.getNonceHtmlAttribute()|raw }}></script>
    {% endfor %}
{% else %}
    {{ pimcore_minimize_scripts(pluginJsPaths)|raw }}
{% endif %}

{% for pluginCssPath in pluginCssPaths %}
    <link rel=\"stylesheet\" type=\"text/css\" href=\"{{ pluginCssPath }}?_dc={{ pluginDcValue }}\"/>
{% endfor %}

{#  MUST BE THE LAST LINE  #}
<script src=\"/bundles/pimcoreadmin/js/pimcore/startup.js?_dc={{ settings.build }}\" {{ pimcore_csp.getNonceHtmlAttribute()|raw }}></script>
</body>
</html>
", "@PimcoreAdmin/admin/index/index.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/index/index.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 1, "if" => 80, "block" => 126, "for" => 633);
        static $filters = array("default" => 80, "escape" => 81, "raw" => 91, "merge" => 205, "json_encode" => 625, "date" => 652, "trans" => 127);
        static $functions = array("path" => 79, "asset" => 101, "encore_entry_script_tags" => 104, "encore_entry_link_tags" => 105, "pimcore_file_exists" => 204, "constant" => 204, "pimcore_minimize_scripts" => 643);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'block', 'for'],
                ['default', 'escape', 'raw', 'merge', 'json_encode', 'date', 'trans'],
                ['path', 'asset', 'encore_entry_script_tags', 'encore_entry_link_tags', 'pimcore_file_exists', 'constant', 'pimcore_minimize_scripts']
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
