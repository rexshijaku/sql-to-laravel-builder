<?php

namespace RexShijaku\SQLToLaravelBuilder\extractors;

/**
 * This class provides common functionality for all Extractor classes.
 * Extractor classes are classes which help to pull out SQL query parts in a way which are more understandable and processable by Builder.
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
abstract class AbstractExtractor
{

    protected $options;

    function __construct($options)
    {
        $this->options = $options;
    }

    function getValue($val)
    {
        return strtolower(trim($val));
    }

    function isLogicalOperator($operator)
    {
        return in_array($this->getValue($operator), array('and', 'or'));
    }

    function hasAlias($val)
    {
        return isset($val['alias']) && $val['alias'] !== false;
    }

    function getFnParams($val, &$params)
    {
        if ($val['sub_tree'] !== false) {
            foreach ($val['sub_tree'] as $k => $item) {
                $params .= $item['base_expr'];
                if ($k < count($val['sub_tree']) - 1)
                    $params .= ",";
                if ($item['expr_type'] !== 'bracket_expression')
                    $this->getFnParams($item, $params);
            }
        }
        return $params;
    }

    function isSingleTable($parsed) // duplicate?
    {
        return (isset($parsed['FROM']) && count($parsed['FROM']) == 1 && $parsed['FROM'][0]['expr_type'] == 'table');
    }

    function validJoin($join_type)
    {
        return $this->getValue($join_type) != 'natural';
    }

    function isArithmeticOperator($op)
    {
        return in_array($this->getValue($op), array('+', '-', '*', '/', '%'));
    }

    function isComparisonOperator($operator, $append = array())
    {
        $simple_operators = array('>', '<', '=', '!=', '>=', '<=', '!<', '!>', '<>');
        if (!empty($append))
            $simple_operators = array_merge($simple_operators, $append);
        return in_array($this->getValue($operator), $simple_operators);
    }

    function getExpressionParts($value, &$parts, &$raws = array(), $recursive = false)
    {

        $val_len = count($value);
        foreach ($value as $k => $val) {

            if (in_array($val['expr_type'], array('function', 'aggregate_function'))) { // base expressions are not enough in such cases
                $local_parts = array($val['base_expr']);
                $local_parts[] = '('; // e.g function wrappers
                if ($val['sub_tree'] !== false) { // functions + agg fn and others
                    $this->getExpressionParts($val['sub_tree'], $local_parts, $raws, true);
                }
                $local_parts[] = ')';
                if ($this->hasAlias($val))
                    $local_parts[] = ' ' . $val['alias']['base_expr'];
                $parts[] = implode('', $local_parts);
                $raws[] = true;

                continue;
            }

            $sub_local = array($val['base_expr']);

            if (!in_array($val['expr_type'], array('expression', 'subquery'))) // these already have aliases appended
            {
                if ($this->hasAlias($val))
                    $sub_local[] = ' ' . $val['alias']['base_expr'];
            }

            if ($recursive) {
                if (isset($val['delim']) && $val['delim'] !== false)
                    $sub_local[] = $val['delim'];
                else if ($k != $val_len - 1)
                    $sub_local[] = ",";
                $parts = array_merge($parts, $sub_local);
            } else {
                $parts[] = implode('', $sub_local);
                $raws[] = $val['expr_type'] != 'colref';
            }

        }
    }

    function mergeExpressionParts($parts)
    {
        return (implode('', $parts));
    }

    protected function getWithAlias($val, &$is_raw)
    {
        if ($val['expr_type'] === 'table')
            $return = $val['table']; // no alias here, if any, it will be added at the end
        else {
            if ($val['expr_type'] == 'subquery') {
                $return = '(' . $val['base_expr'] . ')';
            } else {
                $return = $val['base_expr'];
            }
        }
        if ($this->hasAlias($val)) {
            $return .= ' ';
            if ($val['alias']['as'] === false) // because Laravel escapes 'table t' expressions entirely!
                $is_raw = true;
            $return .= $val['alias']['base_expr'];
        }
        return $return;
    }

    public function isRaw($val)
    {
        return !($val['expr_type'] == 'colref' || $val['expr_type'] == 'const');
    }
}
