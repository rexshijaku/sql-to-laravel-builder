<?php

namespace RexShijaku\builders;

use RexShijaku\utils\CriterionTypes;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  having
 *  orHaving
 *  havingRaw
 *  orHavingRaw
 *  havingBetween
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class HavingBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $query_val = '';
        $group_val = '';
        $in_group = false;

        foreach ($parts as $part) {

            if ($in_group && $part['type'] != 'group') {
                if (!empty($group_val))
                    $group_val .= ' ' . $part['sep'] . ' ';

                if (isset($part['value1']))
                    $value = $part['value1'] . ' AND ' . $part['value2']; // in between
                else
                    $value = $part['value'];

                $group_val .= $part['field'] . ' ' . strtoupper(implode(' ', $part['operators'])) . ' ' . $value;
                continue;
            }

            switch ($part['type']) {
                case CriterionTypes::Group:
                    $in_group = $this->buildGroup($part, $group_val, $query_val);
                    break;
                case CriterionTypes::Comparison:
                case CriterionTypes::Is:
                case CriterionTypes::Like:
                    $op = implode(' ', $part['operators']);
                    $part['value'] = $this->getValue($part['value']) == 'null' ? 'null' : $part['value'];
                    if (in_array('is', $part['operators']) || $part['raw_field'] || $part['raw_value']) {
                        $fn = $this->getValue($part['sep']) == 'or' ? 'orHavingRaw' : 'havingRaw';
                        if ($part['value'] !== 'null')
                            $inner = $this->quote($part['field'] . ' ' . $op . ' ' . '?') . ',' . '[' . $this->wrapValue($part['value']) . ']';
                        else
                            $inner = $this->quote($part['field'] . ' ' . $op . ' ' . $this->wrapValue($part['value']));
                    } else {
                        $fn = $this->getValue($part['sep']) == 'or' ? 'orHaving' : 'having';
                        $inner = $this->quote($part['field']) . ',' . $this->quote($op) . ',' . $this->wrapValue($part['value']);
                    }
                    $query_val .= '->' . $fn . '(' . $inner . ')';
                    break;
                case CriterionTypes::InFieldValue:
                case CriterionTypes::InField: // for sub queries
                    $fn = $this->getValue($part['sep']) == 'or' ? 'orHavingRaw' : 'havingRaw';
                    $inner = $part['field'] . ' ' . strtoupper(implode(' ', $part['operators'])) . ' ' . $part['value'];
                    $query_val .= '->' . $fn . '(' . $this->quote($inner) . ')';
                    break;
                case CriterionTypes::Between:
                    $query_val .= $this->buildBetween($part);
                    break;

                case CriterionTypes::Against:
                    $fn = $this->getValue($part['sep']) == 'or' ? 'orWhereRaw' : 'whereRaw';
                    $query_val .= '->' . $fn . '(' . $this->quote($part['field'] . ' AGAINST ' . $part['value']) . ')';
                    break;
                default:
                    break;
            }

        }
        return $query_val;
    }

    private function buildBetween($part)
    {
        $prefix = $part['sep'] != 'and' ? 'or' : '';

        $query = '->';
        if ($prefix == 'or' || in_array('not', $part['operators'])) { // since not having not present, use having raw
            $fn = 'havingRaw';
            if ($prefix == 'or')
                $fn = 'orHavingRaw';

            $operators = strtoupper(implode(' ', $part['operators']));
            $inner = $part['field'] . ' ' . $operators . ' ' . $part['value1'] . ' AND ' . $part['value2'] . '';
            $query .= $fn . '(' . $this->quote($inner) . ')';
        } else {
            $query .= 'havingBetween(' . $this->buildRawable($part['field'], $part['raw_field']) . ',';
            $query .= '[' . $this->buildRawable($part['value1'], $part['raw_values'][0]) . ',' .
                $this->buildRawable($part['value2'], $part['raw_values'][1]) . ']' . ')';
        }
        return $query;
    }

    private function buildGroup($part, &$group_val, &$query_val)
    {
        if (in_array($part['se'], array('start', 'end'))) {

            if ($part['se'] == 'start')
                return true;
            else if ($part['se'] == 'end') {
                $query_val .= '->havingRaw(' . $this->quote('(' . $group_val . ')') . ')';
                $group_val = ''; // reset collected
                return false;
            }
        }
    }
}