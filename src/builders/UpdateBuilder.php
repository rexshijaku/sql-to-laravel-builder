<?php

namespace RexShijaku\builders;

/**
 * This class constructs and produces following Query Builder methods :
 *
 *  update
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class UpdateBuilder extends AbstractBuilder implements Builder
{
    public function build(array $parts, array &$skip_bag = array())
    {
        $skip_bag[] = 'SET';
        return '->update(' . $this->getSetAsArray($parts['records']) . ')';
    }

    private function getSetAsArray($records)
    {
        if (empty($records))
            return '[]';

        $inner_array = '';
        foreach ($records as $record) {
            if (!empty($inner_array))
                $inner_array .= ',';

            $inner_array .= ($this->quote($record['field']) . '=>');
            $inner_array .= $this->buildRawable($record['value'], $record['raw_val']);

        }
        return '[' . $inner_array . ']';
    }

}