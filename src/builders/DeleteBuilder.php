<?php

namespace RexShijaku\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 * delete
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class DeleteBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        return '->delete()';
    }
}