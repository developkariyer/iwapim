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

/* @WebProfiler/Collector/workflow.html.twig */
class __TwigTemplate_4cbe6dea56dc22df77d1b7c64e3b2a7f extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'stylesheets' => [$this, 'block_stylesheets'],
            'toolbar' => [$this, 'block_toolbar'],
            'menu' => [$this, 'block_menu'],
            'panel' => [$this, 'block_panel'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/workflow.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/workflow.html.twig"));

        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/workflow.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_stylesheets($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 4
        echo "    ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo "
    <style>
        dialog {
            border: none;
            border-radius: 6px;
            box-shadow: var(--settings-modal-shadow);
            max-width: 94%;
            width: 1200px;
        }

        dialog::backdrop {
            background: linear-gradient(
                45deg,
                rgb(18, 18, 20, 0.4),
                rgb(17, 17, 20, 0.8)
            );
        }

        dialog[open] {
            animation: scale 0.3s ease normal;
        }

        dialog[open]::backdrop {
            animation: backdrop 0.3s ease normal;
        }

        dialog.hide {
            animation-direction: reverse;
        }

        dialog h2 {
            margin-top: 0.2em
        }

        dialog i.cancel {
            cursor: pointer;
            padding: 0 5px;
            float: right;
        }

        dialog table {
            border: 1px solid #ccc;
            border-collapse: collapse;
            margin: 0 0 1em 0;
            margin-bottom: 1em;
            padding: 0;
            table-layout: fixed;
        }

        dialog table tr {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: .35em;
        }

        dialog table th,
        dialog table td {
            padding: .625em;
            text-align: center;
        }

        dialog table th {
            font-size: .85em;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        dialog menu {
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            flex-direction: row;
            vertical-align: middle;
            justify-content: center;
        }

        dialog menu small {
            margin-right: auto;
        }
        dialog menu small i {
            margin-right: 3px;
        }

        @keyframes scale {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        @keyframes backdrop {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 100
    public function block_toolbar($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "toolbar"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "toolbar"));

        // line 101
        echo "    ";
        if ((twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 101, $this->source); })()), "callsCount", [], "any", false, false, true, 101) > 0)) {
            // line 102
            echo "        ";
            ob_start();
            // line 103
            echo "            ";
            echo twig_source($this->env, "@WebProfiler/Icon/workflow.svg");
            echo "
            <span class=\"sf-toolbar-value\">";
            // line 104
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 104, $this->source); })()), "callsCount", [], "any", false, false, true, 104), 104, $this->source), "html", null, true);
            echo "</span>
        ";
            $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
            // line 106
            echo "        ";
            ob_start();
            // line 107
            echo "            <div class=\"sf-toolbar-info-piece\">
                <b>Workflow Calls</b>
                <span>";
            // line 109
            echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 109, $this->source); })()), "callsCount", [], "any", false, false, true, 109), 109, $this->source), "html", null, true);
            echo "</span>
            </div>
        ";
            $context["text"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
            // line 112
            echo "
        ";
            // line 113
            echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", ["link" => (isset($context["profiler_url"]) || array_key_exists("profiler_url", $context) ? $context["profiler_url"] : (function () { throw new RuntimeError('Variable "profiler_url" does not exist.', 113, $this->source); })())]);
            echo "
    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 117
    public function block_menu($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "menu"));

        // line 118
        echo "    <span class=\"label ";
        echo (((twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 118, $this->source); })()), "workflows", [], "any", false, false, true, 118), 118, $this->source)) == 0)) ? ("disabled") : (""));
        echo "\">
        <span class=\"icon\">
            ";
        // line 120
        echo twig_source($this->env, "@WebProfiler/Icon/workflow.svg");
        echo "
        </span>
        <strong>Workflow</strong>
    </span>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 126
    public function block_panel($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "panel"));

        // line 127
        echo "    <h2>Workflow</h2>

    ";
        // line 129
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 129, $this->source); })()), "workflows", [], "any", false, false, true, 129)) == 0)) {
            // line 130
            echo "        <div class=\"empty empty-panel\">
            <p>There are no workflows configured.</p>
        </div>
    ";
        } else {
            // line 134
            echo "        <script type=\"module\">
            import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
            mermaid.initialize({
                flowchart: { useMaxWidth: false },
                securityLevel: 'loose',
            });

            ";
            // line 141
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 141, $this->source); })()), "workflows", [], "any", false, false, true, 141));
            foreach ($context['_seq'] as $context["name"] => $context["data"]) {
                // line 142
                echo "                window.showNodeDetails";
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 142, $this->source); })()), "hash", [$context["name"]], "method", false, false, true, 142), 142, $this->source), "html", null, true);
                echo " = function (node) {
                    const map = ";
                // line 143
                echo json_encode($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["data"], "listeners", [], "any", false, false, true, 143), 143, $this->source));
                echo ";
                    showNodeDetails(node, map);
                };
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['data'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 147
            echo "
            const showNodeDetails = function (node, map) {
                const dialog = document.getElementById('detailsDialog');

                dialog.querySelector('tbody').innerHTML = '';
                for (const [eventName, listeners] of Object.entries(map[node])) {
                    listeners.forEach(listener => {
                        const row = document.createElement('tr');

                        const eventNameCode = document.createElement('code');
                        eventNameCode.textContent = eventName;

                        const eventNameCell = document.createElement('td');
                        eventNameCell.appendChild(eventNameCode);
                        row.appendChild(eventNameCell);

                        const listenerDetailsCell = document.createElement('td');
                        row.appendChild(listenerDetailsCell);

                        let listenerDetails;
                        const listenerDetailsCode = document.createElement('code');
                        listenerDetailsCode.textContent = listener.title;
                        if (listener.file) {
                            const link = document.createElement('a');
                            link.href = listener.file;
                            link.appendChild(listenerDetailsCode);
                            listenerDetails = link;
                        } else {
                            listenerDetails = listenerDetailsCode;
                        }
                        listenerDetailsCell.appendChild(listenerDetails);

                        if (typeof listener.guardExpressions === 'object') {
                            listenerDetailsCell.appendChild(document.createElement('br'));

                            const guardExpressionsWrapper = document.createElement('span');
                            guardExpressionsWrapper.appendChild(document.createTextNode('guard expressions: '));

                            listener.guardExpressions.forEach((expression, index) => {
                                if (index > 0) {
                                    guardExpressionsWrapper.appendChild(document.createTextNode(', '));
                                }

                                const expressionCode = document.createElement('code');
                                expressionCode.textContent = expression;
                                guardExpressionsWrapper.appendChild(expressionCode);
                            });

                            listenerDetailsCell.appendChild(guardExpressionsWrapper);
                        }

                        dialog.querySelector('tbody').appendChild(row);
                    });
                };

                if (dialog.dataset.processed) {
                    dialog.showModal();
                    return;
                }

                dialog.addEventListener('click', (e) => {
                    const rect = dialog.getBoundingClientRect();

                    const inDialog =
                        rect.top <= e.clientY &&
                        e.clientY <= rect.top + rect.height &&
                        rect.left <= e.clientX &&
                        e.clientX <= rect.left + rect.width;

                    !inDialog && dialog.close();
                });

                dialog.querySelectorAll('.cancel').forEach(elt => {
                    elt.addEventListener('click', () => dialog.close());
                });

                dialog.showModal();

                dialog.dataset.processed = true;
            };
            // We do not load all mermaid diagrams at once, but only when the tab is opened
            // This is because mermaid diagrams are in a tab, and cannot be renderer with a
            // \"good size\" if they are not visible
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.tab').forEach((el) => {
                    const observer = new MutationObserver(() => {
                        if (!el.classList.contains('block')) {
                            return;
                        }
                        const mEl = el.querySelector('.sf-mermaid');
                        if (mEl.dataset.processed) {
                            return;
                        }
                        mermaid.run({
                            nodes: [mEl],
                        });
                    });
                    observer.observe(el, { attributeFilter: ['class'] });
                });
            });
        </script>

        <div class=\"sf-tabs js-tabs\">
            ";
            // line 250
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 250, $this->source); })()), "workflows", [], "any", false, false, true, 250));
            foreach ($context['_seq'] as $context["name"] => $context["data"]) {
                // line 251
                echo "                <div class=\"tab\">
                    <h2 class=\"tab-title\">";
                // line 252
                echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["name"], 252, $this->source), "html", null, true);
                if (twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, $context["data"], "calls", [], "any", false, false, true, 252))) {
                    echo " (";
                    echo twig_escape_filter($this->env, twig_length_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["data"], "calls", [], "any", false, false, true, 252), 252, $this->source)), "html", null, true);
                    echo ")";
                }
                echo "</h2>

                    <div class=\"tab-content\">
                        <h3>Definition</h3>
                        <pre class=\"sf-mermaid\">
                            ";
                // line 257
                echo $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["data"], "dump", [], "any", false, false, true, 257), 257, $this->source);
                echo "
                            ";
                // line 258
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["data"], "listeners", [], "any", false, false, true, 258));
                foreach ($context['_seq'] as $context["nodeId"] => $context["events"]) {
                    // line 259
                    echo "                                click ";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed($context["nodeId"], 259, $this->source), "html", null, true);
                    echo " showNodeDetails";
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 259, $this->source); })()), "hash", [$context["name"]], "method", false, false, true, 259), 259, $this->source), "html", null, true);
                    echo "
                            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['nodeId'], $context['events'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 261
                echo "                        </pre>

                        <h3>Calls</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Call</th>
                                    <th>Args</th>
                                    <th>Return</th>
                                    <th>Exception</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                ";
                // line 276
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["data"], "calls", [], "any", false, false, true, 276));
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
                foreach ($context['_seq'] as $context["_key"] => $context["call"]) {
                    // line 277
                    echo "                                    <tr>
                                        <td class=\"font-normal text-small text-muted nowrap\">";
                    // line 278
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 278), 278, $this->source), "html", null, true);
                    echo "</td>
                                        <td>
                                            <code>";
                    // line 280
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["call"], "method", [], "any", false, false, true, 280), 280, $this->source), "html", null, true);
                    echo "()</code>
                                            ";
                    // line 281
                    if ((((twig_get_attribute($this->env, $this->source, $context["call"], "previousMarking", [], "any", true, true, true, 281) &&  !(null === twig_get_attribute($this->env, $this->source, $context["call"], "previousMarking", [], "any", false, false, true, 281)))) ? (twig_get_attribute($this->env, $this->source, $context["call"], "previousMarking", [], "any", false, false, true, 281)) : (null))) {
                        // line 282
                        echo "                                                <hr />
                                                Previous marking:
                                                ";
                        // line 284
                        echo $this->extensions['Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension']->dumpData($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["call"], "previousMarking", [], "any", false, false, true, 284), 284, $this->source));
                        echo "
                                            ";
                    }
                    // line 286
                    echo "                                        </td>
                                        <td>
                                            ";
                    // line 288
                    echo $this->extensions['Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension']->dumpData($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["call"], "args", [], "any", false, false, true, 288), 288, $this->source));
                    echo "
                                        </td>
                                        <td>
                                            ";
                    // line 291
                    if (twig_get_attribute($this->env, $this->source, $context["call"], "return", [], "any", true, true, true, 291)) {
                        // line 292
                        echo "                                                ";
                        if ((twig_get_attribute($this->env, $this->source, $context["call"], "return", [], "any", false, false, true, 292) === true)) {
                            // line 293
                            echo "                                                    <code>true</code>
                                                ";
                        } elseif ((twig_get_attribute($this->env, $this->source,                         // line 294
$context["call"], "return", [], "any", false, false, true, 294) === false)) {
                            // line 295
                            echo "                                                    <code>false</code>
                                                ";
                        } else {
                            // line 297
                            echo "                                                    ";
                            echo $this->extensions['Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension']->dumpData($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["call"], "return", [], "any", false, false, true, 297), 297, $this->source));
                            echo "
                                                ";
                        }
                        // line 299
                        echo "                                            ";
                    }
                    // line 300
                    echo "                                        </td>
                                        <td>
                                            ";
                    // line 302
                    if (twig_get_attribute($this->env, $this->source, $context["call"], "exception", [], "any", true, true, true, 302)) {
                        // line 303
                        echo "                                                ";
                        echo $this->extensions['Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension']->dumpData($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["call"], "exception", [], "any", false, false, true, 303), 303, $this->source));
                        echo "
                                            ";
                    }
                    // line 305
                    echo "                                        </td>
                                        <td>
                                            ";
                    // line 307
                    echo twig_escape_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["call"], "duration", [], "any", false, false, true, 307), 307, $this->source), "html", null, true);
                    echo "ms
                                        </td>
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['call'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 311
                echo "                            </tbody>
                        </table>
                    </div>
                </div>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['data'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 316
            echo "        </div>
    ";
        }
        // line 318
        echo "
    <dialog id=\"detailsDialog\">
        <h2>
            Event listeners
            <i class=\"cancel\">×</i>
        </h2>

        <table>
            <thead>
                <tr>
                    <th>event</th>
                    <th>listener</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <menu>
            <small><i>⌨</i> <kbd>esc</kbd></small>
            <button class=\"btn btn-sm cancel\">Close</button>
        </menu>
    </dialog>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@WebProfiler/Collector/workflow.html.twig";
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
        return array (  597 => 318,  593 => 316,  583 => 311,  565 => 307,  561 => 305,  555 => 303,  553 => 302,  549 => 300,  546 => 299,  540 => 297,  536 => 295,  534 => 294,  531 => 293,  528 => 292,  526 => 291,  520 => 288,  516 => 286,  511 => 284,  507 => 282,  505 => 281,  501 => 280,  496 => 278,  493 => 277,  476 => 276,  459 => 261,  448 => 259,  444 => 258,  440 => 257,  427 => 252,  424 => 251,  420 => 250,  315 => 147,  305 => 143,  300 => 142,  296 => 141,  287 => 134,  281 => 130,  279 => 129,  275 => 127,  265 => 126,  250 => 120,  244 => 118,  234 => 117,  221 => 113,  218 => 112,  212 => 109,  208 => 107,  205 => 106,  200 => 104,  195 => 103,  192 => 102,  189 => 101,  179 => 100,  73 => 4,  63 => 3,  40 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    <style>
        dialog {
            border: none;
            border-radius: 6px;
            box-shadow: var(--settings-modal-shadow);
            max-width: 94%;
            width: 1200px;
        }

        dialog::backdrop {
            background: linear-gradient(
                45deg,
                rgb(18, 18, 20, 0.4),
                rgb(17, 17, 20, 0.8)
            );
        }

        dialog[open] {
            animation: scale 0.3s ease normal;
        }

        dialog[open]::backdrop {
            animation: backdrop 0.3s ease normal;
        }

        dialog.hide {
            animation-direction: reverse;
        }

        dialog h2 {
            margin-top: 0.2em
        }

        dialog i.cancel {
            cursor: pointer;
            padding: 0 5px;
            float: right;
        }

        dialog table {
            border: 1px solid #ccc;
            border-collapse: collapse;
            margin: 0 0 1em 0;
            margin-bottom: 1em;
            padding: 0;
            table-layout: fixed;
        }

        dialog table tr {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: .35em;
        }

        dialog table th,
        dialog table td {
            padding: .625em;
            text-align: center;
        }

        dialog table th {
            font-size: .85em;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        dialog menu {
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            flex-direction: row;
            vertical-align: middle;
            justify-content: center;
        }

        dialog menu small {
            margin-right: auto;
        }
        dialog menu small i {
            margin-right: 3px;
        }

        @keyframes scale {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        @keyframes backdrop {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
{% endblock %}

{% block toolbar %}
    {% if collector.callsCount > 0 %}
        {% set icon %}
            {{ source('@WebProfiler/Icon/workflow.svg') }}
            <span class=\"sf-toolbar-value\">{{ collector.callsCount }}</span>
        {% endset %}
        {% set text %}
            <div class=\"sf-toolbar-info-piece\">
                <b>Workflow Calls</b>
                <span>{{ collector.callsCount }}</span>
            </div>
        {% endset %}

        {{ include('@WebProfiler/Profiler/toolbar_item.html.twig', { link: profiler_url }) }}
    {% endif %}
{% endblock %}

{% block menu %}
    <span class=\"label {{ collector.workflows|length == 0 ? 'disabled' }}\">
        <span class=\"icon\">
            {{ source('@WebProfiler/Icon/workflow.svg') }}
        </span>
        <strong>Workflow</strong>
    </span>
{% endblock %}

{% block panel %}
    <h2>Workflow</h2>

    {% if collector.workflows|length == 0 %}
        <div class=\"empty empty-panel\">
            <p>There are no workflows configured.</p>
        </div>
    {% else %}
        <script type=\"module\">
            import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
            mermaid.initialize({
                flowchart: { useMaxWidth: false },
                securityLevel: 'loose',
            });

            {% for name, data in collector.workflows %}
                window.showNodeDetails{{ collector.hash(name) }} = function (node) {
                    const map = {{ data.listeners|json_encode|raw }};
                    showNodeDetails(node, map);
                };
            {% endfor %}

            const showNodeDetails = function (node, map) {
                const dialog = document.getElementById('detailsDialog');

                dialog.querySelector('tbody').innerHTML = '';
                for (const [eventName, listeners] of Object.entries(map[node])) {
                    listeners.forEach(listener => {
                        const row = document.createElement('tr');

                        const eventNameCode = document.createElement('code');
                        eventNameCode.textContent = eventName;

                        const eventNameCell = document.createElement('td');
                        eventNameCell.appendChild(eventNameCode);
                        row.appendChild(eventNameCell);

                        const listenerDetailsCell = document.createElement('td');
                        row.appendChild(listenerDetailsCell);

                        let listenerDetails;
                        const listenerDetailsCode = document.createElement('code');
                        listenerDetailsCode.textContent = listener.title;
                        if (listener.file) {
                            const link = document.createElement('a');
                            link.href = listener.file;
                            link.appendChild(listenerDetailsCode);
                            listenerDetails = link;
                        } else {
                            listenerDetails = listenerDetailsCode;
                        }
                        listenerDetailsCell.appendChild(listenerDetails);

                        if (typeof listener.guardExpressions === 'object') {
                            listenerDetailsCell.appendChild(document.createElement('br'));

                            const guardExpressionsWrapper = document.createElement('span');
                            guardExpressionsWrapper.appendChild(document.createTextNode('guard expressions: '));

                            listener.guardExpressions.forEach((expression, index) => {
                                if (index > 0) {
                                    guardExpressionsWrapper.appendChild(document.createTextNode(', '));
                                }

                                const expressionCode = document.createElement('code');
                                expressionCode.textContent = expression;
                                guardExpressionsWrapper.appendChild(expressionCode);
                            });

                            listenerDetailsCell.appendChild(guardExpressionsWrapper);
                        }

                        dialog.querySelector('tbody').appendChild(row);
                    });
                };

                if (dialog.dataset.processed) {
                    dialog.showModal();
                    return;
                }

                dialog.addEventListener('click', (e) => {
                    const rect = dialog.getBoundingClientRect();

                    const inDialog =
                        rect.top <= e.clientY &&
                        e.clientY <= rect.top + rect.height &&
                        rect.left <= e.clientX &&
                        e.clientX <= rect.left + rect.width;

                    !inDialog && dialog.close();
                });

                dialog.querySelectorAll('.cancel').forEach(elt => {
                    elt.addEventListener('click', () => dialog.close());
                });

                dialog.showModal();

                dialog.dataset.processed = true;
            };
            // We do not load all mermaid diagrams at once, but only when the tab is opened
            // This is because mermaid diagrams are in a tab, and cannot be renderer with a
            // \"good size\" if they are not visible
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.tab').forEach((el) => {
                    const observer = new MutationObserver(() => {
                        if (!el.classList.contains('block')) {
                            return;
                        }
                        const mEl = el.querySelector('.sf-mermaid');
                        if (mEl.dataset.processed) {
                            return;
                        }
                        mermaid.run({
                            nodes: [mEl],
                        });
                    });
                    observer.observe(el, { attributeFilter: ['class'] });
                });
            });
        </script>

        <div class=\"sf-tabs js-tabs\">
            {% for name, data in collector.workflows %}
                <div class=\"tab\">
                    <h2 class=\"tab-title\">{{ name }}{% if data.calls|length %} ({{ data.calls|length }}){% endif %}</h2>

                    <div class=\"tab-content\">
                        <h3>Definition</h3>
                        <pre class=\"sf-mermaid\">
                            {{ data.dump|raw }}
                            {% for nodeId, events in data.listeners %}
                                click {{ nodeId }} showNodeDetails{{ collector.hash(name) }}
                            {% endfor %}
                        </pre>

                        <h3>Calls</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Call</th>
                                    <th>Args</th>
                                    <th>Return</th>
                                    <th>Exception</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                {% for call in data.calls %}
                                    <tr>
                                        <td class=\"font-normal text-small text-muted nowrap\">{{ loop.index }}</td>
                                        <td>
                                            <code>{{ call.method }}()</code>
                                            {% if call.previousMarking ?? null %}
                                                <hr />
                                                Previous marking:
                                                {{ profiler_dump(call.previousMarking) }}
                                            {% endif %}
                                        </td>
                                        <td>
                                            {{ profiler_dump(call.args) }}
                                        </td>
                                        <td>
                                            {% if call.return is defined %}
                                                {% if call.return is same as true %}
                                                    <code>true</code>
                                                {% elseif call.return is same as false %}
                                                    <code>false</code>
                                                {% else %}
                                                    {{ profiler_dump(call.return) }}
                                                {% endif %}
                                            {% endif %}
                                        </td>
                                        <td>
                                            {% if call.exception is defined %}
                                                {{ profiler_dump(call.exception) }}
                                            {% endif %}
                                        </td>
                                        <td>
                                            {{ call.duration }}ms
                                        </td>
                                    </tr>
                                {% endfor %}
                            </tbody>
                        </table>
                    </div>
                </div>
            {% endfor %}
        </div>
    {% endif %}

    <dialog id=\"detailsDialog\">
        <h2>
            Event listeners
            <i class=\"cancel\">×</i>
        </h2>

        <table>
            <thead>
                <tr>
                    <th>event</th>
                    <th>listener</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <menu>
            <small><i>⌨</i> <kbd>esc</kbd></small>
            <button class=\"btn btn-sm cancel\">Close</button>
        </menu>
    </dialog>
{% endblock %}
", "@WebProfiler/Collector/workflow.html.twig", "/var/www/iwapim/vendor/symfony/web-profiler-bundle/Resources/views/Collector/workflow.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 101, "set" => 102, "for" => 141);
        static $filters = array("escape" => 104, "length" => 118, "raw" => 143, "json_encode" => 143);
        static $functions = array("source" => 103, "include" => 113, "profiler_dump" => 284);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'set', 'for'],
                ['escape', 'length', 'raw', 'json_encode'],
                ['source', 'include', 'profiler_dump']
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
