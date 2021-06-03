<?php

namespace RexShijaku\SQLToLaravelBuilder\extractors;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 * table
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class FromExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $parts = array();

        foreach ($value as $val) {
            if (!isset($parts['table'])) {
                $parts = $this->extractSingle($value);
            } else {
                if (!$this->validJoin($val['join_type'])) { // such as natural join
                    $join = array(
                        'type' => $val['join_type'],
                        'table_expr' => $val['base_expr']
                    );
                    $parts['joins'][] = $join;
                }
            }
        }
        return $parts;
    }

    function extractSingle($value)
    {
        $is_raw = $value[0]['expr_type'] != 'table';
        $table = $this->getWithAlias($value[0], $is_raw);
        return array('table' => $table, 'is_raw' => $is_raw);
    }

}