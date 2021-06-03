<?php

namespace RexShijaku\SQLToLaravelBuilder\extractors;

use RexShijaku\SQLToLaravelBuilder\utils\CriterionContext;
/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  join
 *  leftJoin
 *  rightJoin
 *  crossJoin
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class JoinExtractor extends AbstractExtractor implements Extractor
{

    public function extract(array $value, array $parsed = array())
    {
        $joins = array();
        foreach ($value as $k => $val) {
            if ($k == 0) // skip from table
                continue;

            if (!$this->validJoin($val['join_type'])) // skip joins such as natural
                continue;

            $is_raw_table = false;
            $join_table = $this->getWithAlias($val, $is_raw_table);

            $join = array(
                'table' => $join_table,
                'table_is_raw' => $is_raw_table,
                'type' => $val['join_type']
            );

            if ($val['ref_clause'] !== false)
                $join['on_clause'] = $this->getOnCriterion($val['ref_clause']);

            $joins[] = $join;
        }
        return $joins;
    }

    private function getOnCriterion($val)
    {
        $parts = array();
        $criterion = new CriterionExtractor($this->options);
        $criterion->getCriteriaParts($val, $parts, CriterionContext::Join);
        return $parts;
    }
}