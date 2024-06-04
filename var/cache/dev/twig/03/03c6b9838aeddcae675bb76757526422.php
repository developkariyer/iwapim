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

/* @PimcoreAdmin/admin/asset/image_editor.html.twig */
class __TwigTemplate_86eba1832364a6003338f0ef8ad76f1e extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/asset/image_editor.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PimcoreAdmin/admin/asset/image_editor.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html dir=\"ltr\" lang=\"en-US\">
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />
    <base href=\"/bundles/pimcoreadmin/js/lib/minipaint/\" />
    ";
        // line 7
        echo $this->extensions['Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension']->renderWebpackScriptTags("imageEditor", null, "pimcoreAdminImageEditor");
        echo "
</head>
<body>
<div class=\"wrapper\">

    <div class=\"submenu\">
        <div class=\"block attributes\" id=\"action_attributes\"></div>
        <div class=\"clear\"></div>
    </div>

    <div class=\"sidebar_left\" id=\"tools_container\"></div>

    <div class=\"main_wrapper\" id=\"main_wrapper\">
        <div class=\"canvas_wrapper\" id=\"canvas_wrapper\">
            <div id=\"mouse\"></div>
            <div class=\"transparent-grid\" id=\"canvas_minipaint_background\"></div>
            <canvas id=\"canvas_minipaint\">
                <div class=\"trn error\">
                    Your browser does not support canvas or JavaScript is not enabled.
                </div>
            </canvas>
        </div>
    </div>

    <div class=\"sidebar_right\">
        <div class=\"preview block\">
            <h2 class=\"trn toggle\" data-target=\"toggle_preview\">Preview</h2>
            <div id=\"toggle_preview\"></div>
        </div>

        <div class=\"colors block\">
            <h2 class=\"trn toggle\" data-target=\"toggle_colors\">Colors</h2>
            <input
                title=\"Click to change color\"
                type=\"color\"
                class=\"color_area\"
                id=\"main_color\"
                value=\"#0000ff\"\t/>
            <div class=\"content\" id=\"toggle_colors\"></div>
        </div>

        <div class=\"block\" id=\"info_base\">
            <h2 class=\"trn toggle toggle-full\" data-target=\"toggle_info\">Information</h2>
            <div class=\"content\" id=\"toggle_info\"></div>
        </div>

        <div class=\"details block\" id=\"details_base\">
            <h2 class=\"trn toggle toggle-full\" data-target=\"toggle_details\">Layer details</h2>
            <div class=\"content\" id=\"toggle_details\"></div>
        </div>

        <div class=\"layers block\">
            <h2 class=\"trn\">Layers</h2>
            <div class=\"content\" id=\"layers_base\"></div>
        </div>
    </div>
</div>
<div class=\"mobile_menu\">
    <button class=\"right_mobile_menu\" id=\"mobile_menu_button\" type=\"button\"></button>
</div>
<div class=\"ddsmoothmenu\" id=\"main_menu\"></div>
<div class=\"hidden\" id=\"tmp\"></div>
<div id=\"popup\"></div>

";
        // line 71
        $context["imageFileExtension"] = $this->extensions['Pimcore\Twig\Extension\HelpersExtension']->getFileExtension($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 71, $this->source); })()), "getFilename", [], "method", false, false, true, 71), 71, $this->source));
        // line 72
        $context["imageUrl"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_asset_getasset", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 72, $this->source); })()), "getId", [], "method", false, false, true, 72)]);
        // line 73
        echo "
";
        // line 74
        if (!twig_in_filter((isset($context["imageFileExtension"]) || array_key_exists("imageFileExtension", $context) ? $context["imageFileExtension"] : (function () { throw new RuntimeError('Variable "imageFileExtension" does not exist.', 74, $this->source); })()), ["png", "jpg", "jpeg"])) {
            // line 75
            echo "    ";
            $context["imageUrl"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_asset_getimagethumbnail", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 75, $this->source); })()), "getId", [], "method", false, false, true, 75), "format" => "png"]);
        }
        // line 77
        echo "
<img style=\"visibility: hidden\" id='image' src=\"";
        // line 78
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed((isset($context["imageUrl"]) || array_key_exists("imageUrl", $context) ? $context["imageUrl"] : (function () { throw new RuntimeError('Variable "imageUrl" does not exist.', 78, $this->source); })()), 78, $this->source), "html", null, true);
        echo "\" />
<script ";
        // line 79
        echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["pimcore_csp"]) || array_key_exists("pimcore_csp", $context) ? $context["pimcore_csp"] : (function () { throw new RuntimeError('Variable "pimcore_csp" does not exist.', 79, $this->source); })()), "getNonceHtmlAttribute", [], "method", false, false, true, 79), 79, $this->source);
        echo ">

    /**
    * wait for image editor to be fully available 
    * before loading image to editor
    */
    async function loadEditor(e) {
        return new Promise(resolve => {
            var checkInterval = setInterval(() => {
                if (window.Layers) {
                    clearInterval(checkInterval);
                    loadImageToEditor(e);
                    resolve(true);
                }
            }, 300);
        });
    }

    function loadImageToEditor(e) {
        var image = document.getElementById('image');
        window.Layers.insert({
            name: \"";
        // line 100
        echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 100, $this->source); })()), "getFilename", [], "method", false, false, true, 100), 100, $this->source), "html", null, true);
        echo "\",
            type: 'image',
            data: image,
            width: image.naturalWidth || image.width,
            height: image.naturalHeight || image.height,
            width_original: image.naturalWidth || image.width,
            height_original: image.naturalHeight || image.height,
        });

        document.getElementById('save_button').addEventListener('click', function () {

            var tempCanvas = document.createElement(\"canvas\");
            var tempCtx = tempCanvas.getContext(\"2d\");
            var dim = window.Layers.get_dimensions();
            tempCanvas.width = dim.width;
            tempCanvas.height = dim.height;
            Layers.convert_layers_to_canvas(tempCtx);
            var dataUri = tempCanvas.toDataURL('image/";
        // line 117
        echo ((((isset($context["imageFileExtension"]) || array_key_exists("imageFileExtension", $context) ? $context["imageFileExtension"] : (function () { throw new RuntimeError('Variable "imageFileExtension" does not exist.', 117, $this->source); })()) == "png")) ? ("png") : ("jpeg"));
        echo "');

            parent.Ext.Ajax.request({
                url: \"";
        // line 120
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("pimcore_admin_asset_imageeditorsave", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["asset"]) || array_key_exists("asset", $context) ? $context["asset"] : (function () { throw new RuntimeError('Variable "asset" does not exist.', 120, $this->source); })()), "getId", [], "method", false, false, true, 120)]), "html", null, true);
        echo "\",
                method: 'PUT',
                params: {
                    dataUri: dataUri
                }
            });

            return false;
        });
    }
    
    window.addEventListener(\"load\", function(e) {
        loadEditor(e).then(function() {
            console.log(\"editor loaded\");
        });
    }, false);
    


</script>

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
        return "@PimcoreAdmin/admin/asset/image_editor.html.twig";
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
        return array (  190 => 120,  184 => 117,  164 => 100,  140 => 79,  136 => 78,  133 => 77,  129 => 75,  127 => 74,  124 => 73,  122 => 72,  120 => 71,  53 => 7,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html dir=\"ltr\" lang=\"en-US\">
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />
    <base href=\"/bundles/pimcoreadmin/js/lib/minipaint/\" />
    {{ encore_entry_script_tags('imageEditor', null, 'pimcoreAdminImageEditor') }}
</head>
<body>
<div class=\"wrapper\">

    <div class=\"submenu\">
        <div class=\"block attributes\" id=\"action_attributes\"></div>
        <div class=\"clear\"></div>
    </div>

    <div class=\"sidebar_left\" id=\"tools_container\"></div>

    <div class=\"main_wrapper\" id=\"main_wrapper\">
        <div class=\"canvas_wrapper\" id=\"canvas_wrapper\">
            <div id=\"mouse\"></div>
            <div class=\"transparent-grid\" id=\"canvas_minipaint_background\"></div>
            <canvas id=\"canvas_minipaint\">
                <div class=\"trn error\">
                    Your browser does not support canvas or JavaScript is not enabled.
                </div>
            </canvas>
        </div>
    </div>

    <div class=\"sidebar_right\">
        <div class=\"preview block\">
            <h2 class=\"trn toggle\" data-target=\"toggle_preview\">Preview</h2>
            <div id=\"toggle_preview\"></div>
        </div>

        <div class=\"colors block\">
            <h2 class=\"trn toggle\" data-target=\"toggle_colors\">Colors</h2>
            <input
                title=\"Click to change color\"
                type=\"color\"
                class=\"color_area\"
                id=\"main_color\"
                value=\"#0000ff\"\t/>
            <div class=\"content\" id=\"toggle_colors\"></div>
        </div>

        <div class=\"block\" id=\"info_base\">
            <h2 class=\"trn toggle toggle-full\" data-target=\"toggle_info\">Information</h2>
            <div class=\"content\" id=\"toggle_info\"></div>
        </div>

        <div class=\"details block\" id=\"details_base\">
            <h2 class=\"trn toggle toggle-full\" data-target=\"toggle_details\">Layer details</h2>
            <div class=\"content\" id=\"toggle_details\"></div>
        </div>

        <div class=\"layers block\">
            <h2 class=\"trn\">Layers</h2>
            <div class=\"content\" id=\"layers_base\"></div>
        </div>
    </div>
</div>
<div class=\"mobile_menu\">
    <button class=\"right_mobile_menu\" id=\"mobile_menu_button\" type=\"button\"></button>
</div>
<div class=\"ddsmoothmenu\" id=\"main_menu\"></div>
<div class=\"hidden\" id=\"tmp\"></div>
<div id=\"popup\"></div>

{% set imageFileExtension = pimcore_file_extension(asset.getFilename()) %}
{% set imageUrl = path('pimcore_admin_asset_getasset', {id: asset.getId()}) %}

{% if imageFileExtension not in ['png', 'jpg', 'jpeg'] %}
    {% set imageUrl = path('pimcore_admin_asset_getimagethumbnail', {id: asset.getId(), format: 'png' }) %}
{% endif %}

<img style=\"visibility: hidden\" id='image' src=\"{{ imageUrl }}\" />
<script {{ pimcore_csp.getNonceHtmlAttribute()|raw }}>

    /**
    * wait for image editor to be fully available 
    * before loading image to editor
    */
    async function loadEditor(e) {
        return new Promise(resolve => {
            var checkInterval = setInterval(() => {
                if (window.Layers) {
                    clearInterval(checkInterval);
                    loadImageToEditor(e);
                    resolve(true);
                }
            }, 300);
        });
    }

    function loadImageToEditor(e) {
        var image = document.getElementById('image');
        window.Layers.insert({
            name: \"{{ asset.getFilename() }}\",
            type: 'image',
            data: image,
            width: image.naturalWidth || image.width,
            height: image.naturalHeight || image.height,
            width_original: image.naturalWidth || image.width,
            height_original: image.naturalHeight || image.height,
        });

        document.getElementById('save_button').addEventListener('click', function () {

            var tempCanvas = document.createElement(\"canvas\");
            var tempCtx = tempCanvas.getContext(\"2d\");
            var dim = window.Layers.get_dimensions();
            tempCanvas.width = dim.width;
            tempCanvas.height = dim.height;
            Layers.convert_layers_to_canvas(tempCtx);
            var dataUri = tempCanvas.toDataURL('image/{{ imageFileExtension == \"png\" ? \"png\" : \"jpeg\" }}');

            parent.Ext.Ajax.request({
                url: \"{{ path('pimcore_admin_asset_imageeditorsave', {id: asset.getId()}) }}\",
                method: 'PUT',
                params: {
                    dataUri: dataUri
                }
            });

            return false;
        });
    }
    
    window.addEventListener(\"load\", function(e) {
        loadEditor(e).then(function() {
            console.log(\"editor loaded\");
        });
    }, false);
    


</script>

</body>
</html>
", "@PimcoreAdmin/admin/asset/image_editor.html.twig", "/var/www/iwapim/vendor/pimcore/admin-ui-classic-bundle/templates/admin/asset/image_editor.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 71, "if" => 74);
        static $filters = array("escape" => 78, "raw" => 79);
        static $functions = array("encore_entry_script_tags" => 7, "pimcore_file_extension" => 71, "path" => 72);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['escape', 'raw'],
                ['encore_entry_script_tags', 'pimcore_file_extension', 'path']
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
