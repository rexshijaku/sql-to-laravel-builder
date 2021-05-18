<?php

namespace RexShijaku\SQLToLaravelBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  join
 *  leftJoin
 *  rightJoin
 *  crossJoin
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class JoinBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        $qb = '';

        foreach ($parts as $join) {

            $condition = implode('', $join['condition_separators']);
            if ($this->getValue($join['type']) !== 'join') { // left,right,cross etc
                $fn = $this->fnMerger(array(strtolower($join['type']), 'join'));
            } else
                $fn = $this->fnMerger(array('join'));

            $qb .= "->" . $fn . "(" . $this->quote($join['table']);
            if (!empty($join['condition_fields'])) { // in cross e.g are empty
                $qb .= "," . $this->quote($join['condition_fields'][0])
                    . "," . $this->quote($condition)
                    . "," . $this->quote($join['condition_fields'][1]);
            }
            $qb .= ")";
        }

        return $qb;
    }
}