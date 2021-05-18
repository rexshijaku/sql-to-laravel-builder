<?php

namespace RexShijaku\SQLToLaravelBuilder\extractors;
/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  groupBy
 *  groupByRaw
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class GroupByExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $parts = array(); // columns
        $is_raw = false;
        foreach ($value as $k => $val) {
            $parts_tmp = array();
            $this->getExpressionParts(array($val), $parts_tmp); // expression parts since it can be anything! such as fn, subquery etc.
            $parts[] = $this->mergeExpressionParts($parts_tmp);
            if ($this->isRaw($val))
                $is_raw = true;
        }
        return array('parts' => $parts, 'is_raw' => $is_raw);
    }
}