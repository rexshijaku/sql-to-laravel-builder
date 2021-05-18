<?php

namespace RexShijaku\SQLToLaravelBuilder\extractors;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  insert
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class InsertExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $column_list = array();
        foreach ($value as $val) {
            if ($val['expr_type'] == 'column-list') {
                foreach ($val['sub_tree'] as $column)
                    $column_list[] = $column['base_expr'];
            }
        }

        $records = array(); // collect data so you gave only records and know about if is it batch or not
        foreach ($parsed['VALUES'] as $key => $item)
            if ($item['expr_type'] == 'record') {
                $data = array();
                foreach ($item['data'] as $datum)
                    $data[] = $datum['base_expr'];
                $records[] = $data;
            }

        return array('columns' => $column_list, 'records' => $records);
    }

}