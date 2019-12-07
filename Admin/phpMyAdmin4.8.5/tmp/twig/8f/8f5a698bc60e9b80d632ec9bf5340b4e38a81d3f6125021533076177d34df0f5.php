<?php

/* table/search/options_zoom.twig */
class __TwigTemplate_757d35f630684ad163900ba0f0f4d81f2dc93d03476c68c38c40b6b7e3b2dbe5 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<table class=\"data\">
    ";
        // line 3
        echo "    <tr>
        <td>
            <label for=\"dataLabel\">
                ";
        // line 6
        echo _gettext("Use this column to label each point");
        // line 7
        echo "            </label>
        </td>
        <td>
            <select name=\"dataLabel\" id=\"dataLabel\" >
                <option value = \"\">
                    ";
        // line 12
        echo _gettext("None");
        // line 13
        echo "                </option>
                ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range(0, (twig_length_filter($this->env, ($context["column_names"] ?? null)) - 1)));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 15
            echo "                    ";
            if (((isset($context["data_label"]) || array_key_exists("data_label", $context)) && (($context["data_label"] ?? null) == twig_escape_filter($this->env, $this->getAttribute(($context["column_names"] ?? null), $context["i"], [], "array"))))) {
                // line 16
                echo "                        <option value=\"";
                echo twig_escape_filter($this->env, $this->getAttribute(($context["column_names"] ?? null), $context["i"], [], "array"), "html", null, true);
                echo "\" selected=\"selected\">
                            ";
                // line 17
                echo twig_escape_filter($this->env, $this->getAttribute(($context["column_names"] ?? null), $context["i"], [], "array"), "html", null, true);
                echo "
                        </option>
                    ";
            } else {
                // line 20
                echo "                        <option value=\"";
                echo twig_escape_filter($this->env, $this->getAttribute(($context["column_names"] ?? null), $context["i"], [], "array"), "html", null, true);
                echo "\" >
                            ";
                // line 21
                echo twig_escape_filter($this->env, $this->getAttribute(($context["column_names"] ?? null), $context["i"], [], "array"), "html", null, true);
                echo "
                        </option>
                    ";
            }
            // line 24
            echo "                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 25
        echo "            </select>
        </td>
    </tr>
    ";
        // line 29
        echo "    <tr>
        <td>
            <label for=\"maxRowPlotLimit\">
                ";
        // line 32
        echo _gettext("Maximum rows to plot");
        // line 33
        echo "            </label>
        </td>
        <td>
            <input type=\"number\"
                name=\"maxPlotLimit\"
                id=\"maxRowPlotLimit\"
                required=\"required\"
                value=\"";
        // line 40
        echo twig_escape_filter($this->env, ($context["max_plot_limit"] ?? null), "html", null, true);
        echo "\" />
        </td>
    </tr>
</table>
";
    }

    public function getTemplateName()
    {
        return "table/search/options_zoom.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  97 => 40,  88 => 33,  86 => 32,  81 => 29,  76 => 25,  70 => 24,  64 => 21,  59 => 20,  53 => 17,  48 => 16,  45 => 15,  41 => 14,  38 => 13,  36 => 12,  29 => 7,  27 => 6,  22 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "table/search/options_zoom.twig", "D:\\phpstudy_pro\\WWW\\phpMyAdmin4.8.5\\templates\\table\\search\\options_zoom.twig");
    }
}
