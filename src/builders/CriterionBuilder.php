<?php

namespace RexShijaku\SQLToLaravelBuilder\builders;

use RexShijaku\SQLToLaravelBuilder\utils\CriterionTypes;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  where
 *  orWhere
 *  whereRaw
 *  orWhereRaw
 *
 *  whereBetween
 *  orWhereNotBetween
 *  whereIn
 *  orWhereIn
 *  whereNotIn
 *  orWhereNotIn
 *
 *  whereNull
 *  whereNotNull
 *
 *  Logical Grouping methods
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class CriterionBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        $query_val = '';

        foreach ($parts as $part) {

            switch ($part['type']) {

                case CriterionTypes::Comparison:
                case CriterionTypes::Is:
                case CriterionTypes::Like:
                    $op = implode(' ', $part['operators']);
                    $part['value'] = $this->getValue($part['value']) == 'null' ? 'null' : $part['value'];

                    if ($part['raw_field'] || $part['raw_value']) {

                        $fn = $this->getValue($part['sep']) == 'or' ? 'orWhereRaw' : 'whereRaw';

                        if ($part['raw_field'] && !$part['raw_value'] && $this->getValue($part['value']) !== 'null')
                            $inner = $this->quote($part['field'] . ' ' . strtoupper($op) . ' ? ') . ', ' . '[' . $this->wrapValue($part['value']) . ']';
                        else
                            $inner = $this->quote($part['field'] . ' ' . strtoupper($op) . ' ' . $part['value']) . '';
                    } else {

                        $fn_parts = $this->getValue($part['sep']) == 'or' ? array('or', 'where') : array('where');

                        if ($part['value'] == 'null') {
                            if ($op == 'is not') {
                                $fn_parts[] = 'not';
                                $fn_parts[] = 'null';
                            } else if ($op == 'is')
                                $fn_parts[] = 'null';
                            $inner = $this->quote($part['field']);
                        } else
                            $inner = $this->quote($part['field']) . ',' . $this->quote(strtoupper($op)) . ',' . $this->wrapValue($part['value']);
                        $fn = $this->fnMerger($fn_parts);
                    }
                    $query_val .= '->' . $fn . '(' . $inner . ')';
                    break;

                case CriterionTypes::InFieldValue:
                case CriterionTypes::InField: // for sub queries

                    $value_php_arr = (isset($part['as_php_arr']) && $part['as_php_arr'] == true);

                    if ($part['raw_field'] || ($part['raw_value'] && !$value_php_arr)) {
                        // Raw Methods
                        $fn = $this->getValue($part['sep']) == 'or' ? 'orWhereRaw' : 'whereRaw';
                        $query_val .= '->' . $fn . '(' . $this->quote($part['field'] . ' ' . strtoupper(implode(' ', $part['operators'])) . ' ' . $part['value']) . ')';

                    } else {
                        // Additional Where Clauses
                        $operator_tokens = $this->getValue($part['sep']) == 'or' ? array('or', 'where') : array('where');
                        $operator_tokens = array_merge($operator_tokens, $part['operators']); // not + in part (depending on what was passed)
                        $fn = $this->fnMerger($operator_tokens);

                        $query_val .= '->' . $fn . '(' . $this->quote($part['field']) . ',';
                        if ($value_php_arr)
                            $query_val .= '[' . $this->unBracket($part['value']) . ']';
                        else
                            $query_val .= '[' . $this->wrapValue($part['value']) . ']';
                        $query_val .= ')';
                    }
                    break;
                case CriterionTypes::Between:
                    $query_val .= $this->buildBetween($part);
                    break;
                case CriterionTypes::Group:
                    $this->buildGroup($part, $query_val);
                    break;
                case CriterionTypes::Against:
                    $fn = $this->getValue($part['sep']) == 'or' ? 'orWhereRaw' : 'whereRaw';
                    $query_val .= '->' . $fn . '(' . $this->quote($part['field'] . ' AGAINST ' . $part['value']) . ')';
                    break;
                case CriterionTypes::Function:
                    $fn = $this->getValue($part['sep']) == 'or' ? 'orWhere' : 'where';
                    $fn = $this->fnMerger(array($fn, $part['fn']));
                    $op = $part['operator'];
                    $inner = $this->quote($part['field']) . ',' . $this->quote(strtoupper($op)) . ',' . $this->wrapValue($part['value']['value']);
                    $query_val .= '->' . $fn . '(' . $inner . ')';
                    break;
                default:
                    break;
            }

        }
        return $query_val;
    }

    public function buildAsArray(array $parts)
    {
        $query_val = $this->arrayify($parts);
        if ($query_val !== false)
            return '->where(' . $query_val . ')';
        return false;
    }

    private function buildGroup($part, &$query_val)
    {
        if (in_array($part['se'], array('start', 'end'))) {

            $fn = '';
            if ($part['se'] == 'start') {
                $query_val .= "->";

                if ($part['has_negation']) {  // when not is before group, only where can be used!
                    $fn = 'where(function ($query) { $query';
                } else {
                    if ($part['log'] == 'or')
                        $fn = 'orWhere(function ($query) { $query';
                    else
                        $fn = 'where(function ($query) { $query';
                }


            } else if ($part['se'] == 'end') {
                if ($part['has_negation'])
                    $fn = ';},null,null,\'' . $part['log'] . ' ' . 'not' . '\')'; // see https://github.com/laravel/ideas/issues/708
                else
                    $fn = ';})';
            }

            $query_val .= $fn;
        }
    }

    private function buildBetween($part)
    {
        $query = '->';
        $prefix = $part['sep'] == 'and' ? null : 'or';
        if (in_array('not', $part['operators'])) { // is not between?
            $fn_parts = ['where', 'not', 'between'];
            if ($prefix == 'or')
                array_unshift($fn_parts, $prefix);
            $fn = $this->fnMerger($fn_parts);
            $query .= $fn . '(' . $this->buildRawable($part['field'], $part['raw_field']) . ',';
        } else { // is simply between ?
            $fn_parts = ['where', 'between'];
            if ($prefix == 'or')
                array_unshift($fn_parts, $prefix);
            $fn = $this->fnMerger($fn_parts);
            $query .= $fn . '(' . $this->buildRawable($part['field'], $part['raw_field']) . ',';
        }

        $query .= '[' . $this->buildRawable($part['value1'], $part['raw_values'][0]) . ',' .
            $this->buildRawable($part['value2'], $part['raw_values'][1]) . ']' . ')';

        return $query;
    }
}