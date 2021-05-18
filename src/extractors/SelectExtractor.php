<?php

namespace RexShijaku\SQLToLaravelBuilder\extractors;

use RexShijaku\SQLToLaravelBuilder\utils\SelectQueryTypes;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  table
 *  distinct
 *  select
 *  sum
 *  avg
 *  min
 *  max
 *  count
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class SelectExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $distinct = $this->isDistinct($value);
        if ($distinct)
            array_shift($value);

        if ($this->isSingleTable($parsed) &&
            $this->isCountTable($value)) {

            return array('s_type' => SelectQueryTypes::CountATable, 'parts' => array('table' => $parsed['FROM'][0]['base_expr'], 'distinct' => $distinct, 'selected' => 'COUNT(*)'));

        } else if ($this->isAggregate($value))
            return array('s_type' => SelectQueryTypes::Aggregate,
                'parts' => $this->extractAggregateParts($value, $distinct));

        $this->getExpressionParts($value, $parts, $raws);
        return array('s_type' => SelectQueryTypes::Other, 'parts' => array('selected' => $parts, 'distinct' => $distinct, 'raws' => $raws));
    }

    private function isAggregate($value)
    {
        return count($value) == 1 && $value[0]['expr_type'] == 'aggregate_function'
            && in_array($this->getValue($value[0]['base_expr']), $this->options['settings']['agg']);
    }

    private function isDistinct($value)
    {
        return count($value) > 0 && $value[0]['expr_type'] == 'reserved' && $this->getValue($value[0]['base_expr']) == 'distinct';
    }

    private function isCountTable($value)
    {
        return count($value) == 1 && $value[0]['expr_type'] == 'aggregate_function' && $this->getValue($value[0]['base_expr']) == 'count' && $this->getFnParams($value[0], $d) === "*";
    }

    private function extractAggregateParts($value, $distinct)
    {
        $fn_suffix = $this->getValue($value[0]['base_expr']);
        $this->getExpressionParts($value[0]['sub_tree'], $parts);
        $column = implode('', $parts);

        $alias = $this->hasAlias($value[0]);
        if ($alias)
            $alias = $value[0]['alias']['name'];
        return array('suffix' => $fn_suffix, 'column' => $column, 'alias' => $alias, 'distinct' => $distinct);
    }

}