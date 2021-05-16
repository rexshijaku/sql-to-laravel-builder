<?php

namespace RexShijaku\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  groupBy
 *  groupByRaw
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class GroupByBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $qb = '';
        $parts_len = count($parts['parts']);

        if ($parts_len == 0)
            return $qb;

        $fn = !$parts['is_raw'] ? 'groupBy' : 'groupByRaw';

        $inner = '';
        if ($parts_len == 1)
            $inner .= $this->quote($parts['parts'][0]);
        else if ($parts_len > 1) {
            if ($parts['is_raw'])
                $inner = $this->quote(implode(', ', $parts['parts']));
            else
                $inner = '[' . implode(",", array_map(array($this, 'quote'), $parts['parts'])) . ']';
        }

        $qb .= "->" . $fn . '(' . $inner . ')';
        return $qb;
    }
}