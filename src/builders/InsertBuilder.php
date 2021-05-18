<?php

namespace RexShijaku\SQLToLaravelBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  insert
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class InsertBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        $qb = '';
        $record_len = count($parts['records']);
        $column_len = count($parts['columns']);

        if ($record_len == 0)
            return false;

        $is_batch = $record_len > 1;

        $inner_arrays = '';
        foreach ($parts['records'] as $record_key => $record) {

            if (count($record) != $column_len)
                return '';

            if ($is_batch && $record_key > 0)
                $inner_arrays .= ",";

            $single_array = $is_batch ? '[' : '';
            foreach ($record as $k => $col_val) {
                if ($k > 0)
                    $single_array .= ',';
                $single_array .= $this->quote($parts['columns'][$k]) . '=>' . ($this->wrapValue($col_val));

            }
            $inner_arrays .= $single_array . ($is_batch ? ']' : '');
        }

        if (!empty($inner_arrays)) {
            $outer_array = '[' . $inner_arrays . ']';
            $qb = '->insert(' . $outer_array . ')';
        }
        return $qb;
    }

}