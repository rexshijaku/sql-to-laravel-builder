<?php

namespace RexShijaku\extractors;
/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 *  offset
 *  limit
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class LimitExtractor extends AbstractExtractor implements Extractor
{

    public function extract(array $value, array $parsed = array())
    {
        $possible = array('offset', 'rowcount');

        $parts = array();
        foreach ($value as $k => $val) {
            if (empty($val) || !in_array($k, $possible))
                continue;
            $parts[$k] = $val;
        }
        return $parts;
    }
}