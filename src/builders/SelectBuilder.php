<?php

namespace RexShijaku\builders;

use RexShijaku\utils\SelectQueryTypes;

/**
 * This class constructs and produces following Query Builder methods :
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
class SelectBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $type = $parts['s_type'];
        $parts = $parts['parts'];

        $qb = '';
        if ($parts['distinct'])
            $qb = $this->distinctQ();

        switch ($type) {
            case SelectQueryTypes::Aggregate:
                $qb .= $this->aggregateQ($parts['suffix'], $parts['column'], $parts['alias'], $close_qb);
                return array('query_part' => $qb, 'type' => $close_qb ? 'lastly' : 'eq', 'close_qb' => $close_qb);
            case SelectQueryTypes::CountATable:
                $skip_bag[] = 'FROM';
                $qb .= $this->countAllQ($parts['table']);
                return array('query_part' => $qb, 'type' => 'eq', 'close_qb' => true);
            case SelectQueryTypes::Other:
                $qb .= $this->selectOnlyQ($parts['selected'], $parts['raws']);
                return array('query_part' => $qb, 'type' => 'eq', 'close_qb' => false);
            default:
                break;
        }
    }

    private function aggregateQ($suffix, $column, $alias, &$close_qb)
    {
        $close_qb = false;
        if ($alias !== false) {
            $fn = strtoupper($suffix) . '(' . $column . ')';
            $qb = "->selectRaw(" . $this->buildRawable($fn . " AS " . $alias) . ")";
        } else {
            $close_qb = true; // max(something) is the end of query / or count
            if ($column != '*')
                $qb = '->' . $this->getValue($suffix) . '(' . $this->quote($column) . ')';
            else
                $qb = '->' . $this->getValue($suffix) . '()';
        }
        return $qb;
    }

    private function selectOnlyQ($parts, $raws)
    {
        $column_len = count($parts);

        if ($column_len == 1 && $parts[0] == '*')
            return '';

        $query = '->';
        $ci_part = 'select'; // to be done selectRaw
        $query .= $ci_part . "(";
        foreach ($parts as $k => $column) {
            $query .= $this->buildRawable($column, $raws[$k]);
            if ($k + 1 != $column_len)
                $query .= ',';
        }
        $query .= ")";
        return $query;
    }

    private function countAllQ($table)
    {
        return 'table(' . $this->quote($table) . ')->count()';
    }

    private function distinctQ()
    {
        return '->distinct()';
    }
}