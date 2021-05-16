<?php

namespace RexShijaku\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  union
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class UnionBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        $q = '';
        foreach ($parts as $k => $part) {
            if ($k == 0) {
                $q .= $part['str'];
            } else {

                if ($part['is_all'] === 1)
                    $q .= '->unionAll(' . $part['str'] . ')';
                else
                    $q .= '->union(' . $part['str'] . ')';
            }
        }
        return $q . '->get();';
    }
}