<?php

namespace RexShijaku\extractors;

/**
 * This class extracts and compiles SQL query parts for the following Query Builder methods :
 *
 * delete
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
class DeleteExtractor extends AbstractExtractor implements Extractor
{

    public function extract(array $value, array $parsed = array())
    {
        return array();
    }
}

