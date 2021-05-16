<?php

namespace RexShijaku\extractors;

/**
 *  Extractor.php
 *
 *  Interface declaration for all extractor classes.
 *  An extractor can create pull out SQL query parts in a way which are more understandable and processable by Builder.
 *  The necessary information are provided by the function parameter as array. This array is extracted from
 *  the PHPSQLParser output.
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 */
interface Extractor
{
    /**
     * Builds a part of an SQL statement.
     *
     * @param array $value
     * @param array $parsed
     * @return A string, which contains a part of an SQL statement.
     */
    public function extract(array $value, array $parsed = array());
}

