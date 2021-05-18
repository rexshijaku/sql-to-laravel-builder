<?php

namespace RexShijaku\SQLToLaravelBuilder\extractors;
/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  orderBy
 *  orderByRaw
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class OrderExtractor extends AbstractExtractor implements Extractor
{
    public function extract(array $value, array $parsed = array())
    {
        $this->getExpressionParts($value, $parts_temp);
        $parts = array();
        foreach ($value as $k => $val)
            $parts[] = array('field' => $parts_temp[$k], 'dir' => $val['direction'], 'type' => 'normal', 'raw' => $this->isRaw($val));
        return $parts;
    }
}