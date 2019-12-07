<?php

/* table/search/search_and_replace.twig */
class __TwigTemplate_39d455b4aa53cf77d19f4587c72a8c4576f27bedb9a044e56a0e2dd32e23a757 extends Twig_Template
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
        echo _gettext("Find:");
        // line 2
        echo "<input type=\"text\" value=\"\" name=\"find\" required />
";
        // line 3
        echo _gettext("Replace with:");
        // line 4
        echo "<input type=\"text\" value=\"\" name=\"replaceWith\" />

";
        // line 6
        echo _gettext("Column:");
        // line 7
        echo "<select name=\"columnIndex\">
    ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range(0, (twig_length_filter($this->env, ($context["column_names"] ?? null)) - 1)));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 9
            echo "        ";
            $context["type"] = preg_replace("@\\(.*@s", "", $this->getAttribute(($context["column_types"] ?? null), $context["i"], [], "array"));
            // line 10
            echo "        ";
            if (($this->getAttribute(($context["sql_types"] ?? null), "getTypeClass", [0 => ($context["type"] ?? null)], "method") == "CHAR")) {
                // line 11
                echo "            ";
                $context["column"] = $this->getAttribute(($context["column_names"] ?? null), $context["i"], [], "array");
                // line 12
                echo "            <option value=\"";
                echo twig_escape_filter($this->env, $context["i"], "html", null, true);
                echo "\">
                ";
                // line 13
                echo twig_escape_filter($this->env, ($context["column"] ?? null), "html", null, true);
                echo "
            </option>
        ";
            }
            // line 16
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        echo "</select>

";
        // line 19
        $this->loadTemplate("checkbox.twig", "table/search/search_and_replace.twig", 19)->display(["html_field_id" => "useRegex", "html_field_name" => "useRegex", "label" => _gettext("Use regular expression"), "checked" => false, "onclick" => false]);
    }

    public function getTemplateName()
    {
        return "table/search/search_and_replace.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 19,  65 => 17,  59 => 16,  53 => 13,  48 => 12,  45 => 11,  42 => 10,  39 => 9,  35 => 8,  32 => 7,  30 => 6,  26 => 4,  24 => 3,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "table/search/search_and_replace.twig", "D:\\phpstudy_pro\\WWW\\phpMyAdmin4.8.5\\templates\\table\\search\\search_and_replace.twig");
    }
}
