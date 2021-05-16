<?php

namespace RexShijaku\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 * table
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class FromBuilder extends AbstractBuilder implements Builder
{

    public function build(array $parts, array &$skip_bag = array())
    {
        return 'table(' . $this->buildRawable($parts['table'], $parts['is_raw']) . ')';
    }
}