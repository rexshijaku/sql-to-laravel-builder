<?php

namespace RexShijaku\SQLToLaravelBuilder\extractors;

use RexShijaku\SQLToLaravelBuilder\utils\CriterionContext;
use RexShijaku\SQLToLaravelBuilder\utils\CriterionTypes;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  where
 *  orWhere
 *  whereRaw
 *  orWhereRaw
 *
 *  whereBetween
 *  orWhereNotBetween
 *  whereIn
 *  whereNotIn
 *
 *  whereNull
 *  whereNotNull
 *
 *  Logical Grouping methods
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class CriterionExtractor extends AbstractExtractor implements Extractor
{
    private $negation_on = false;
    private $handle_outer_negation = false;

    public function extract(array $value, array $parsed = array())
    {
        $this->getCriteriaParts($value, $parts);
        return $parts;
    }

    function extractAsArray($value, &$part)
    {
        if (!$this->options['group']) {
            $part = false;
            return false;
        }

        $part = $this->getArrayParts($value); // passed by reference value changed
        if ($part !== false)
            return true;
        return false;
    }

    function getCriteriaParts($value, &$parts = array(), $context = CriterionContext::Where, $handle_outer_negation = true)
    {
        $sep = '';
        $curr_index = 0;
        $logical_operator = 'and';

        foreach ($value as $index => $val) {

            if ($index < $curr_index)
                continue; // skip those collected in inner loop

            if (in_array($val['expr_type'], array('operator', 'reserved'))) { // reserved since k+10 in (in is considered reserved)
                $sep = $this->getValue($val['base_expr']);
                if ($this->isLogicalOperator($sep)) {
                    $logical_operator = $this->getValue($val['base_expr']);
                    continue;
                }

                switch ($this->getValue($sep)) {
                    case $this->isComparisonOperator($sep):
                        $this->handle_outer_negation = $handle_outer_negation;
                        $res_field = $this->getLeft($index, $value, $context);
                        $res_value = $this->getRight($index, $value, $curr_index, $context);

                        $parts[] = array(
                            'type' => CriterionTypes::Comparison,
                            'operators' => array(strtolower($sep)),
                            'field' => $res_field['value'],
                            'value' => $res_value['value'],
                            'raw_field' => $res_field['is_raw'],
                            'raw_value' => $res_value['is_raw'],
                            'sep' => $logical_operator,
                            'const_value' => $res_value['is_const']
                        );
                        break;
                    case 'is':

                        $this->handle_outer_negation = $handle_outer_negation;

                        $res_field = $this->getLeft($index, $value);
                        $res_value = $this->getRight($index, $value, $curr_index, $context);

                        $operators_ = array('is');
                        if ($res_value['has_negation'])
                            $operators_[] = 'not';

                        $parts[] = array(
                            'type' => CriterionTypes::Is,
                            'operators' => $operators_,
                            'field' => $res_field['value'],
                            'value' => $res_value['value'],
                            'raw_field' => $res_field['is_raw'],
                            'raw_value' => $res_value['is_raw'],
                            'sep' => $logical_operator,
                            'const_value' => $res_value['is_const']
                            ); // now combine fields + operators
                        break;
                    case "between":
                        $btw_operators = array();
                        if ($this->negation_on)
                            $btw_operators[] = 'not';
                        $btw_operators[] = 'between';

                        $res_field = $this->getLeft($index, $value);
                        $res_val = $this->getBetweenValue($index, $value, $curr_index);


                        $parts[] = array(
                            'type' => CriterionTypes::Between,
                            'operators' => $btw_operators,
                            'field' => $res_field['value'],
                            'value1' => implode('', $res_val['value'][0]),
                            'value2' => implode('', $res_val['value'][1]),
                            'raw_field' => $res_field['is_raw'],
                            'raw_values' => $res_val['is_raw'],
                            'sep' => $logical_operator); // now combine fields + operators
                        break;
                    case "like":
                        $like_operators = array();
                        if ($this->negation_on)
                            $like_operators[] = 'not';
                        $like_operators[] = 'like';

                        $res_field = $this->getLeft($index, $value);
                        $res_val = $this->getRight($index, $value, $curr_index, $context);


                        $parts[] = array(
                            'type' => CriterionTypes::Like,
                            'operators' => $like_operators,
                            'field' => $res_field['value'],
                            'value' => $res_val['value'],
                            'raw_field' => $res_field['is_raw'],
                            'raw_value' => $res_val['is_raw'],
                            'sep' => $logical_operator,
                            'const_value' => $res_val['is_const']);
                        break;
                    case "in":

                        $in_operators = array();
                        if ($this->negation_on) // is not in ?
                            $in_operators[] = 'not';
                        $in_operators[] = 'in';

                        $res_field = $this->getLeft($index, $value);
                        $res_val = $this->getRight($index, $value, $curr_index, $context);

                        $parts[] = array(
                            'type' => $res_val['value_type'] == 'field_only' ? CriterionTypes::InField : CriterionTypes::InFieldValue,
                            'operators' => $in_operators,
                            'field' => $res_field['value'],
                            'value' => $res_val['value'],
                            'raw_field' => $res_field['is_raw'],
                            'raw_value' => $res_val['is_raw'],
                            'sep' => $logical_operator,
                            'as_php_arr' => $res_val['value_type'] == 'in-list',
                            'const_value' => $res_val['is_const']);

                        break;
                    case "not":
                        $this->negation_on = !$this->negation_on;
                        break;
                    default:

                        break;
                }
            } else if ($val['expr_type'] == 'bracket_expression') {
                $local = array();

                if ($val['sub_tree'] !== false) { // skip cases such ()
                    $negation_on = $this->negation_on;
                    $this->getCriteriaParts($val['sub_tree'], $local, $context,false); // recursion
                    if (!empty($local)) {
                        $parts[] = array('type' => CriterionTypes::Group, 'se' => 'start', 'has_negation' => $negation_on, 'log' => $logical_operator);
                        $parts = array_merge($parts, $local);
                        $parts[] = array('type' => CriterionTypes::Group, 'se' => 'end', 'has_negation' => $negation_on, 'log' => $logical_operator);
                    }
                }

            } else if ($val['expr_type'] == 'function') {
                if ($this->getValue($val['base_expr']) == 'against') {
                    $res_field = $this->getLeft($index, $value);
                    $res_val = $this->getRight($index, $value, $curr_index, $context);

                    $parts[] = array(
                        'type' => CriterionTypes::Against,
                        'field' => $res_field['value'],
                        'value' => $res_val['value'],
                        'sep' => $logical_operator
                    );

                } else if (CriterionContext::Where == $context) {
                    $fn = $this->getValue($val['base_expr']);

                    if (in_array($fn, $this->options['settings']['fns'])) {

                        if($val['sub_tree'] !== false && $this->isRaw($val['sub_tree'][0]))
                            continue;

                        $params = ''; // params is field in this context
                        $this->getFnParams($val, $params);

                        $temp_index = $curr_index;
                        $curr_index = $index = ($index + 1); // move to operator
                        $sep = $this->getValue($value[$curr_index]['base_expr']);
                        $res_val = $this->getRight($index, $value, $curr_index, $context);

                        if ($res_val['is_raw']) {
                            $curr_index = $index = $temp_index;
                            continue;
                        }


                        $parts[] = array(
                            'type' => CriterionTypes::Function,
                            'fn' => $fn,
                            'field' => $params,
                            'value' => $res_val,
                            'operator' => $sep,
                            'sep' => $logical_operator
                        );
                    }
                }

            }
        }
    }

    private function getArrayParts($val)
    {
        $fields = array();
        $values = array();
        $operators = array();

        $next = 'field';
        $local_operators = array();

        foreach ($val as $v) {
            if ($v['expr_type'] == 'operator') {

                if ($this->getValue($v['base_expr']) == 'not' && (count($fields) - count($values)) == 0) // not x > 1
                    return false;

                if ($this->isComparisonOperator($v['base_expr'], array('like', 'not'))) { // in this case [like, not like] are are valid comparison operators
                    $local_operators[] = $v['base_expr'];
                    continue;
                }

                if ($this->getValue($v['base_expr']) != 'and') // prevent group on [or] operator, also in any operation too such as field1+field2 > number (this needs not to be escaped, therefore it will be as separate row)
                    return false;
            } else {
                if ($next == 'field') {
                    $field = $this->getAllValue($v);
                    if (in_array($field, $fields)) // dont allow grouping in duplicate keys (because of php arrays)
                        return false;

                    if ($this->isRaw($v))
                        return false;

                    $fields[] = $field;
                    $next = 'value';
                } else if ($next == 'value') {
                    $value = $this->getAllValue($v);
                    if ($this->isRaw($v))
                        return false;
                    $values[] = $value;
                    $operators[] = implode(' ', $local_operators);
                    $next = 'field';
                    $local_operators = array();
                }
            }
        }

        if (count($fields) != count($values) || count($fields) <= 1)
            return false;

        return array('fields' => $fields, 'operators' => $operators, 'values' => $values);
    }

    function getLeft($index, $value, $context = CriterionContext::Where)
    {
        $field_value = '';
        $left_ind = $index;
        $left_operator = '';
        $is_raw = false;
        while (!$this->isLogicalOperator($left_operator)) {
            if ($left_ind > 0) {
                $left_ind--;
                $op_type = $this->getValue($value[$left_ind]['expr_type']);
                if ($op_type == 'operator') {
                    if (!$this->isArithmeticOperator($this->getValue($value[$left_ind]['base_expr']))) {
                        $left_operator = $this->getValue($value[$left_ind]['base_expr']);
                    } else {
                        $field_value = $value[$left_ind]['base_expr'] . $field_value;
                        $is_raw = true; // if some operation is happening then the expression should not be escaped
                    }
                } else if ($context == CriterionContext::Having && $op_type == 'colref' && $value[$left_ind]['base_expr'] == ',') {
                    break;
                } else {
                    if ($op_type == 'reserved') // where x like '%abc'; stop at where, todo needs a better solution
                        break;

                    if ($op_type != 'const' && $op_type != 'colref')
                        $is_raw = true;

                    $field_value = $this->getAllValue($value[$left_ind]) . $field_value;
                }
            } else
                break;
        }

        if ($this->handle_outer_negation && $this->negation_on) {
            $field_value = ' NOT ' . $field_value;
            $is_raw = true;

        }

        $this->negation_on = false;
        $this->handle_outer_negation = false;

        return array('value' => $field_value, 'is_raw' => $is_raw);
    }

    function getRight($index, $value, &$curr_index, $context = CriterionContext::Where)
    {
        $has_negation = false;
        $value_ = '';
        $value_type = '';

        $right_ind = $index;
        $right_operator = '';
        $is_raw = false;

        $is_const = null;

        while (!$this->isLogicalOperator($right_operator)) { // x > 2 and (until you find first logical operator keep looping)
            $right_ind++;
            if ($right_ind < count($value)) {

                if ($this->getValue($value[$right_ind]['expr_type']) == 'operator') {

                    if (!$this->isArithmeticOperator($value[$right_ind]['base_expr']))
                        $right_operator = $this->getValue($value[$right_ind]['base_expr']);
                    else {
                        $value_ .= $value[$right_ind]['base_expr'];
                        if ($context === CriterionContext::Join) // because on x=x+5, x+5 is escaped entirely !
                            $is_const = false;
                        else
                            $is_raw = true; // if some operation is happening then the expression should not be escaped
                    }

                    if ($right_operator == 'not')
                        $has_negation = true;
                } else if ($context == CriterionContext::Having
                    && $value[$right_ind]['expr_type'] == 'colref'
                    && $value[$right_ind]['base_expr'] == ','
                ) {
                    break;
                } else {
                    $value_type = $value[$right_ind]['expr_type'];
                    if ($context === CriterionContext::Join) { // on x = y (both x,y must be column)
                        if ($value[$right_ind]['expr_type'] != 'colref')
                            $is_raw = true;

                        if (!isset($is_const)) {
                            if ($value[$right_ind]['expr_type'] == 'const')
                                $is_const = true;
                            else
                                $is_const = false;
                        }

                    } else {
                        if ($value[$right_ind]['expr_type'] != 'const')
                            $is_raw = true;
                    }

                    if ($value_type == 'subquery')
                        $value_type = 'field_only';

                    $val = $this->getAllValue($value[$right_ind]);
                    $value_ .= $val;
                }
            } else
                break;
        }
        $curr_index = $right_ind;
        return array('value' => $value_, 'has_negation' => $has_negation,
            'is_raw' => $is_raw, 'value_type' => $value_type, 'is_const' => $is_const);
    }

    private function getBetweenValue($index, $value, &$curr_index)
    {
        $has_negation = false;
        $final = $raws = $values_ = array();
        $right_ind = $index;
        $right_operator = '';

        $log_operator_count = 0;
        $is_raw = false;
        while ($log_operator_count != 2) { // between x and y and (until you find second logical operator keep looping)
            $right_ind++;
            if ($right_ind < count($value)) {
                if ($this->getValue($value[$right_ind]['expr_type']) == 'operator') {
                    if (!$this->isArithmeticOperator($value[$right_ind]['base_expr']))
                        $right_operator = $this->getValue($value[$right_ind]['base_expr']);

                    if ($right_operator == 'not') {
                        $has_negation = true;
                        $right_operator = '';
                        continue;
                    }

                    if ($this->isLogicalOperator($right_operator)) {
                        $log_operator_count++;
                        $right_operator = '';
                        $final[] = $values_;
                        $raws[] = $is_raw;
                        $is_raw = false;
                        $values_ = array();
                        continue;
                    }

                }

                if ($value[$right_ind]['expr_type'] != 'const')
                    $is_raw = true;

                $values_ [] = $this->getAllValue($value[$right_ind]);
            } else
                break;
        }

        if (!empty($values_)) {
            $final[] = $values_;
            $raws[] = $is_raw;
        }
        $curr_index = $right_ind;
        return array('value' => $final, 'has_negation' => $has_negation, 'is_raw' => $raws);
    }

    private function getAllValue($val)
    {
        $this->getExpressionParts(array($val), $parts);
        return $this->mergeExpressionParts($parts);
    }
}