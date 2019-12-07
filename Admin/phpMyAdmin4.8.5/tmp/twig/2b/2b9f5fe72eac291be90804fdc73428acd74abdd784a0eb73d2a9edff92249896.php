<?php

/* display/results/comment_for_row.twig */
class __TwigTemplate_f0e6f2a433f1345d79e4e6bf7d696800c77f56982242c9859ed77bf97ab4168a extends Twig_Template
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
        if (($this->getAttribute((isset($context["comments_map"]) ? $context["comments_map"] : null), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "table", []), [], "array", true, true) && $this->getAttribute($this->getAttribute(        // line 2
(isset($context["comments_map"]) ? $context["comments_map"] : null), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "table", []), [], "array", false, true), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "name", []), [], "array", true, true))) {
            // line 3
            echo "    <span class=\"tblcomment\" title=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["comments_map"]) ? $context["comments_map"] : null), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "table", []), [], "array"), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "name", []), [], "array"), "html", null, true);
            echo "\">
        ";
            // line 4
            if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["comments_map"]) ? $context["comments_map"] : null), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "table", []), [], "array"), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "name", []), [], "array")) > (isset($context["limit_chars"]) ? $context["limit_chars"] : null))) {
                // line 5
                echo "            ";
                echo twig_escape_filter($this->env, twig_slice($this->env, $this->getAttribute($this->getAttribute((isset($context["comments_map"]) ? $context["comments_map"] : null), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "table", []), [], "array"), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "name", []), [], "array"), 0, (isset($context["limit_chars"]) ? $context["limit_chars"] : null)), "html", null, true);
                echo "…
        ";
            } else {
                // line 7
                echo "            ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["comments_map"]) ? $context["comments_map"] : null), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "table", []), [], "array"), $this->getAttribute((isset($context["fields_meta"]) ? $context["fields_meta"] : null), "name", []), [], "array"), "html", null, true);
                echo "
        ";
            }
            // line 9
            echo "    </span>
";
        }
    }

    public function getTemplateName()
    {
        return "display/results/comment_for_row.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 9,  35 => 7,  29 => 5,  27 => 4,  22 => 3,  20 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "display/results/comment_for_row.twig", "D:\\phpstudy_pro\\WWW\\phpMyAdmin4.8.5\\templates\\display\\results\\comment_for_row.twig");
    }
}
