<?php

/* table/search/rows_zoom.twig */
class __TwigTemplate_033a0a4f4a3ea596f36f78c24ddd6fd6ace0bb9fee5c0fe27103acaa542e5c6a extends Twig_Template
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
        // line 2
        $context["type"] = [];
        // line 3
        $context["collation"] = [];
        // line 4
        $context["func"] = [];
        // line 5
        $context["value"] = [];
        // line 6
        echo "
";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range(0, 3));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 8
            echo "    ";
            // line 9
            echo "    ";
            if (($context["i"] == 2)) {
                // line 10
                echo "        <tr>
            <td>
                ";
                // line 12
                echo _gettext("Additional search criteria");
                // line 13
                echo "            </td>
        </tr>
    ";
            }
            // line 16
            echo "    <tr class=\"noclick\">
        <th>
            <select name=\"criteriaColumnNames[]\" id=\"tableid_";
            // line 18
            echo twig_escape_filter($this->env, $context["i"], "html", null, true);
            echo "\" >
                <option value=\"pma_null\">
                    ";
            // line 20
            echo _gettext("None");
            // line 21
            echo "                </option>
                ";
            // line 22
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(range(0, (twig_length_filter($this->env, ($context["column_names"] ?? null)) - 1)));
            foreach ($context['_seq'] as $context["_key"] => $context["j"]) {
                // line 23
                echo "                    ";
                if (($this->getAttribute(($context["criteria_column_names"] ?? null), $context["i"], [], "array", true, true) && ($this->getAttribute(                // line 24
($context["criteria_column_names"] ?? null), $context["i"], [], "array") == $this->getAttribute(($context["column_names"] ?? null), $context["j"], [], "array")))) {
                    // line 25
                    echo "                        <option value=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute(($context["column_names"] ?? null), $context["j"], [], "array"), "html", null, true);
                    echo "\" selected=\"selected\">
                            ";
                    // line 26
                    echo twig_escape_filter($this->env, $this->getAttribute(($context["column_names"] ?? null), $context["j"], [], "array"), "html", null, true);
                    echo "
                        </option>
                    ";
                } else {
                    // line 29
                    echo "                        <option value=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute(($context["column_names"] ?? null), $context["j"], [], "array"), "html", null, true);
                    echo "\">
                            ";
                    // line 30
                    echo twig_escape_filter($this->env, $this->getAttribute(($context["column_names"] ?? null), $context["j"], [], "array"), "html", null, true);
                    echo "
                        </option>
                    ";
                }
                // line 33
                echo "                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['j'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 34
            echo "            </select>
        </th>
        ";
            // line 36
            if (((isset($context["criteria_column_names"]) || array_key_exists("criteria_column_names", $context)) && ($this->getAttribute(            // line 37
($context["criteria_column_names"] ?? null), $context["i"], [], "array") != "pma_null"))) {
                // line 38
                echo "            ";
                $context["key"] = array_search($this->getAttribute(($context["criteria_column_names"] ?? null), $context["i"], [], "array"), ($context["column_names"] ?? null));
                // line 39
                echo "            ";
                $context["properties"] = $this->getAttribute(($context["self"] ?? null), "getColumnProperties", [0 => $context["i"], 1 => ($context["key"] ?? null)], "method");
                // line 40
                echo "            ";
                $context["type"] = twig_array_merge(($context["type"] ?? null), ["i" => $this->getAttribute(($context["properties"] ?? null), "type", [], "array")]);
                // line 41
                echo "            ";
                $context["collation"] = twig_array_merge(($context["collation"] ?? null), ["i" => $this->getAttribute(($context["properties"] ?? null), "collation", [], "array")]);
                // line 42
                echo "            ";
                $context["func"] = twig_array_merge(($context["func"] ?? null), ["i" => $this->getAttribute(($context["properties"] ?? null), "func", [], "array")]);
                // line 43
                echo "            ";
                $context["value"] = twig_array_merge(($context["value"] ?? null), ["i" => $this->getAttribute(($context["properties"] ?? null), "value", [], "array")]);
                // line 44
                echo "        ";
            }
            // line 45
            echo "        ";
            // line 46
            echo "        <td dir=\"ltr\">
            ";
            // line 47
            echo twig_escape_filter($this->env, (($this->getAttribute(($context["type"] ?? null), $context["i"], [], "array", true, true)) ? ($this->getAttribute(($context["type"] ?? null), $context["i"], [], "array")) : ("")), "html", null, true);
            echo "
        </td>
        ";
            // line 50
            echo "        <td>
            ";
            // line 51
            echo twig_escape_filter($this->env, (($this->getAttribute(($context["collation"] ?? null), $context["i"], [], "array", true, true)) ? ($this->getAttribute(($context["collation"] ?? null), $context["i"], [], "array")) : ("")), "html", null, true);
            echo "
        </td>
        ";
            // line 54
            echo "        <td>
            ";
            // line 55
            echo (($this->getAttribute(($context["func"] ?? null), $context["i"], [], "array", true, true)) ? ($this->getAttribute(($context["func"] ?? null), $context["i"], [], "array")) : (""));
            echo "
        </td>
        ";
            // line 58
            echo "        <td>
        </td>
        <td>
            ";
            // line 61
            echo (($this->getAttribute(($context["value"] ?? null), $context["i"], [], "array", true, true)) ? ($this->getAttribute(($context["value"] ?? null), $context["i"], [], "array")) : (""));
            echo "
            ";
            // line 63
            echo "            <input type=\"hidden\"
                name=\"criteriaColumnTypes[";
            // line 64
            echo twig_escape_filter($this->env, $context["i"], "html", null, true);
            echo "]\"
                id=\"types_";
            // line 65
            echo twig_escape_filter($this->env, $context["i"], "html", null, true);
            echo "\"";
            // line 66
            if ($this->getAttribute(($context["criteria_column_types"] ?? null), $context["i"], [], "array", true, true)) {
                // line 67
                echo "                    value=\"";
                echo twig_escape_filter($this->env, $this->getAttribute(($context["criteria_column_types"] ?? null), $context["i"], [], "array"), "html", null, true);
                echo "\"";
            }
            // line 68
            echo " />
            <input type=\"hidden\"
                name=\"criteriaColumnCollations[";
            // line 70
            echo twig_escape_filter($this->env, $context["i"], "html", null, true);
            echo "]\"
                id=\"collations_";
            // line 71
            echo twig_escape_filter($this->env, $context["i"], "html", null, true);
            echo "\" />
        </td>
    </tr>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['i'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "table/search/rows_zoom.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  188 => 71,  184 => 70,  180 => 68,  175 => 67,  173 => 66,  170 => 65,  166 => 64,  163 => 63,  159 => 61,  154 => 58,  149 => 55,  146 => 54,  141 => 51,  138 => 50,  133 => 47,  130 => 46,  128 => 45,  125 => 44,  122 => 43,  119 => 42,  116 => 41,  113 => 40,  110 => 39,  107 => 38,  105 => 37,  104 => 36,  100 => 34,  94 => 33,  88 => 30,  83 => 29,  77 => 26,  72 => 25,  70 => 24,  68 => 23,  64 => 22,  61 => 21,  59 => 20,  54 => 18,  50 => 16,  45 => 13,  43 => 12,  39 => 10,  36 => 9,  34 => 8,  30 => 7,  27 => 6,  25 => 5,  23 => 4,  21 => 3,  19 => 2,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "table/search/rows_zoom.twig", "D:\\phpstudy_pro\\WWW\\phpMyAdmin4.8.5\\templates\\table\\search\\rows_zoom.twig");
    }
}
