<?php

namespace RexShijaku\SQLToLaravelBuilder\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  offset
 *  limit
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class LimitBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $query_val = '';

        if (isset($parts['offset']))
            $query_val .= "->offset(" . $parts['offset'] . ')';
        if (isset($parts['rowcount']))
            $query_val .= "->limit(" . $parts['rowcount'] . ')';

        return $query_val;
    }

}