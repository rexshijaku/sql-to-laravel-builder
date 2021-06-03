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

            if ($this->getValue($join['type']) !== 'join') { // left,right,cross etc
                $fn = $this->fnMerger(array(strtolower($join['type']), 'join'));
            } else
                $fn = $this->fnMerger(array('join'));

            $qb .= "->" . $fn . "(" . $this->buildRawable($join['table'], $join['table_is_raw']);
            if (isset($join['on_clause']) && count($join['on_clause']) > 0) // in cross join no on_clause!
            {
                // everything except columns are raw !
                if (count($join['on_clause']) == 1
                    && $join['on_clause'][0]['type'] !== 'between'
                    && $join['on_clause'][0]['raw_field'] === false
                    && $join['on_clause'][0]['raw_value'] === false) {

                    $on_clause = $join['on_clause'][0];
                    $qb .= "," . $this->quote($on_clause['field'])
                        . "," . $this->quote(implode(' ', $on_clause['operators']))
                        . "," . $this->quote($on_clause['value']);
                } else {

                    $qb .= ',' . 'function($join) {';
                    $qb .= '$join';

                    foreach ($join['on_clause'] as $on_clause) {

                        if ($on_clause['type'] == 'between' || $on_clause['raw_field'] || $on_clause['raw_value']) {
                            if (isset($on_clause['const_value']))
                                $on_clause['raw_value'] = !$on_clause['const_value'];
                            $builder = new CriterionBuilder($this->options);
                            $q = $builder->build(array($on_clause));
                            $qb .= $q;
                        } else {
                            // no raw found and not between
                            $operators = implode(' ', $on_clause['operators']);
                            $fn_parts = $on_clause['sep'] == 'and' ? array('on') : array('or', 'on');

                            $qb .= '->';
                            $qb .= $this->fnMerger($fn_parts);
                            $qb .= '(';

                            $qb .= $this->quote($on_clause['field'], $on_clause['raw_field'])
                                . "," . $this->quote($operators)
                                . "," . $this->quote($on_clause['value'],
                                    !$on_clause['const_value'] && $on_clause['raw_value']);

                            $qb .= ')';
                        }
                    }
                    $qb .= '; }';
                }
            }
            $qb .= ")";
        }

        return $qb;
    }
}